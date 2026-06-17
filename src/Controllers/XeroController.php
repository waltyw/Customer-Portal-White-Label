<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Core\Security;
use App\Core\View;
use App\Core\XeroAPI;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;

class XeroController
{
    public function index(): void
    {
        Auth::requireAdmin();
        View::render('admin/xero', [
            'title'       => 'Xero Integration',
            'connected'   => XeroAPI::isConnected(),
            'tenantName'  => Setting::get('xero_tenant_name'),
            'clientId'    => Setting::get('xero_client_id'),
            'redirectUri' => Setting::get('xero_redirect_uri') ?: ($_ENV['APP_URL'] . '/admin/xero/callback'),
        ], 'admin');
    }

    public function saveConfig(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        try {
            $clientId = trim($_POST['xero_client_id'] ?? '');
            $secret   = trim($_POST['xero_client_secret'] ?? '');

            if ($clientId) {
                Setting::set('xero_client_id', $clientId);
            }

            if ($secret) {
                Setting::set('xero_client_secret', $secret);
            }

            $appUrl = $_ENV['APP_URL'] ?? '';
            Setting::set('xero_redirect_uri', rtrim($appUrl, '/') . '/admin/xero/callback');

            $secretStatus = $secret ? 'Client ID and Secret saved.' : 'Client ID saved. Existing secret kept.';
            Security::flash('success', $secretStatus . ' Now click Connect to Xero.');
        } catch (\Throwable $e) {
            error_log('XeroController::saveConfig error: ' . $e->getMessage());
            Security::flash('error', 'Save failed: ' . $e->getMessage());
        }

        Security::redirect('/admin/xero');
    }

    public function connect(): void
    {
        Auth::requireAdmin();

        if (!Setting::get('xero_client_id') || !Setting::get('xero_client_secret')) {
            Security::flash('error', 'Please save your Xero Client ID and Secret first.');
            Security::redirect('/admin/xero');
        }

        Security::redirect(XeroAPI::authUrl());
    }

    public function callback(): void
    {
        Auth::requireAdmin();

        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';
        $error = $_GET['error'] ?? '';

        if ($error || !$code) {
            Security::flash('error', 'Xero connection was cancelled or failed: ' . Security::e($error));
            Security::redirect('/admin/xero');
        }

        if (XeroAPI::exchangeCode($code, $state)) {
            Security::flash('success', 'Successfully connected to Xero! You can now sync invoices.');
        } else {
            Security::flash('error', 'Failed to connect to Xero. Please check your credentials and try again.');
        }

        Security::redirect('/admin/xero');
    }

    public function disconnect(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();
        XeroAPI::disconnect();
        Security::flash('success', 'Disconnected from Xero.');
        Security::redirect('/admin/xero');
    }

    public function sync(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        if (!XeroAPI::isConnected()) {
            Security::flash('error', 'Not connected to Xero.');
            Security::redirect('/admin/xero');
        }

        try {
            $xeroInvoices   = XeroAPI::getAllInvoices();
            $contactEmailMap = XeroAPI::getContactEmailMap();
        } catch (\Throwable $e) {
            error_log('XeroController::sync API error: ' . $e->getMessage());
            Security::flash('error', 'Xero API error: ' . $e->getMessage());
            Security::redirect('/admin/xero');
        }

        $imported       = 0;
        $updated        = 0;
        $skipped        = 0;
        $noEmail        = 0;
        $unmatchedEmails= [];

        foreach ($xeroInvoices as $xi) {
            try {
                $contactId = $xi['Contact']['ContactID'] ?? '';
                $email     = $contactEmailMap[$contactId] ?? strtolower(trim($xi['Contact']['EmailAddress'] ?? ''));
                $xeroId    = $xi['InvoiceID'] ?? '';
                $invoiceNo = $xi['InvoiceNumber'] ?? '';

                if (!$xeroId) { $skipped++; continue; }

                if (!$email) {
                    $noEmail++;
                    $skipped++;
                    continue;
                }

                $customer = User::findByEmail($email);
                if (!$customer) {
                    $unmatchedEmails[$email] = true;
                    $skipped++;
                    continue;
                }

                $status = match($xi['Status'] ?? '') {
                    'AUTHORISED' => 'authorised',
                    'PAID'       => 'paid',
                    'VOIDED'     => 'voided',
                    default      => 'authorised',
                };

                $lineItems = array_map(fn($li) => [
                    'description' => $li['Description'] ?? '',
                    'qty'         => (float)($li['Quantity'] ?? 1),
                    'unit_price'  => (float)($li['UnitAmount'] ?? 0),
                ], $xi['LineItems'] ?? []);

                $issueDate = self::parseXeroDate($xi['DateString'] ?? '');
                $dueDate   = self::parseXeroDate($xi['DueDateString'] ?? '');

                $existing = \App\Core\DB::fetchOne(
                    'SELECT id FROM invoices WHERE xero_invoice_id = ?', [$xeroId]
                );

                if ($existing) {
                    \App\Core\DB::execute(
                        'UPDATE invoices SET status=?, amount_paid=?, amount_due=?, updated_at=NOW() WHERE xero_invoice_id=?',
                        [$status, (float)($xi['AmountPaid'] ?? 0), (float)($xi['AmountDue'] ?? 0), $xeroId]
                    );
                    $updated++;
                } else {
                    \App\Core\DB::execute(
                        'INSERT INTO invoices (user_id, xero_invoice_id, invoice_number, status, subtotal, vat_amount, total, amount_paid, amount_due, currency, issue_date, due_date, line_items)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                        [
                            $customer['id'],
                            $xeroId,
                            $invoiceNo,
                            $status,
                            (float)($xi['SubTotal']   ?? 0),
                            (float)($xi['TotalTax']   ?? 0),
                            (float)($xi['Total']      ?? 0),
                            (float)($xi['AmountPaid'] ?? 0),
                            (float)($xi['AmountDue']  ?? 0),
                            $xi['CurrencyCode']  ?? 'GBP',
                            $issueDate,
                            $dueDate,
                            json_encode($lineItems),
                        ]
                    );
                    $imported++;
                }
            } catch (\Throwable $e) {
                error_log('XeroController::sync invoice error (' . ($xi['InvoiceNumber'] ?? '?') . '): ' . $e->getMessage());
                $skipped++;
            }
        }

        Setting::set('xero_last_sync', date('Y-m-d H:i:s'));

        $msg = "Sync complete — {$imported} imported, {$updated} updated, {$skipped} skipped.";
        if ($noEmail) {
            $msg .= " {$noEmail} Xero contact(s) have no email address set — open those contacts in Xero and add an email.";
        }
        if ($unmatchedEmails) {
            $list = implode(', ', array_keys($unmatchedEmails));
            $msg .= " These Xero emails have no matching portal user: {$list}";
        }
        Security::flash($imported + $updated > 0 ? 'success' : 'warning', $msg);
        Security::redirect('/admin/xero');
    }

    private static function parseXeroDate(string $date): ?string
    {
        if (!$date) return null;
        // Xero returns "2024-01-15T00:00:00"
        try {
            return (new \DateTime($date))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
