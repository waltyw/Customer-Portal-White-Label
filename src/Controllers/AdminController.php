<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Core\Security;
use App\Core\View;
use App\Email\Mailer;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;

class AdminController
{
    public function dashboard(): void
    {
        Auth::requireAdmin();
        $ticketCounts  = Ticket::counts();
        $invoiceCounts = Invoice::counts();
        $recentTickets = array_slice(Ticket::all(), 0, 10);

        View::render('admin/dashboard', [
            'title'          => 'Admin Dashboard',
            'ticketCounts'   => $ticketCounts,
            'invoiceCounts'  => $invoiceCounts,
            'recentTickets'  => $recentTickets,
            'opcacheEnabled' => function_exists('opcache_reset'),
        ], 'admin');
    }

    public function systemStatus(): void
    {
        Auth::requireAdmin();

        $status = [];

        // Database
        try {
            \App\Core\DB::fetchOne('SELECT 1');
            $status['database'] = ['ok' => true, 'msg' => 'Connected'];
        } catch (\Exception $e) {
            $status['database'] = ['ok' => false, 'msg' => $e->getMessage()];
        }

        // Storage writable
        $storageBase = dirname(__DIR__, 2) . '/storage';
        $status['storage_attachments'] = [
            'ok'  => is_writable($storageBase . '/attachments'),
            'msg' => is_writable($storageBase . '/attachments') ? 'Writable' : 'Not writable — chmod 755',
        ];
        $status['storage_logs'] = [
            'ok'  => is_writable($storageBase . '/logs'),
            'msg' => is_writable($storageBase . '/logs') ? 'Writable' : 'Not writable — chmod 755',
        ];

        // PHP version
        $status['php'] = [
            'ok'  => version_compare(PHP_VERSION, '8.1.0', '>='),
            'msg' => PHP_VERSION,
        ];

        // OPcache
        $status['opcache'] = [
            'ok'  => false,
            'msg' => function_exists('opcache_reset') ? 'Enabled' : 'Not enabled (files reload instantly — no action needed)',
        ];

        // SMTP configured
        $status['smtp'] = [
            'ok'  => !empty($_ENV['SMTP_HOST']) && !empty($_ENV['SMTP_PASS']),
            'msg' => !empty($_ENV['SMTP_HOST']) ? $_ENV['SMTP_HOST'] . ':' . ($_ENV['SMTP_PORT'] ?? '465') : 'Not configured in .env',
        ];

        // Stripe configured
        $status['stripe'] = [
            'ok'  => !empty($_ENV['STRIPE_SECRET_KEY']),
            'msg' => !empty($_ENV['STRIPE_SECRET_KEY']) ? 'Key set' : 'Not configured — payments disabled',
        ];

        View::render('admin/system-status', [
            'title'  => 'System Status',
            'status' => $status,
        ], 'admin');
    }

    // ── Customers ────────────────────────────────────────────────────────────

    public function customers(): void
    {
        Auth::requireAdmin();
        View::render('admin/customers', [
            'title'     => 'Customers',
            'customers' => User::customers(),
        ], 'admin');
    }

    public function createCustomer(): void
    {
        Auth::requireAdmin();
        View::render('admin/customer-create', ['title' => 'Add Customer'], 'admin');
    }

    public function storeCustomer(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        $email   = strtolower(trim($_POST['email'] ?? ''));
        $name    = trim($_POST['name'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');

        if (!$email || !$name) {
            Security::flash('error', 'Name and email are required.');
            Security::redirect('/admin/customers/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Security::flash('error', 'Invalid email address.');
            Security::redirect('/admin/customers/create');
        }

        if (User::findByEmail($email)) {
            Security::flash('error', 'A customer with this email already exists.');
            Security::redirect('/admin/customers/create');
        }

        $tempPassword = bin2hex(random_bytes(8));
        $userId = User::create([
            'email'    => $email,
            'password' => $tempPassword,
            'name'     => $name,
            'company'  => $company,
            'phone'    => $phone,
        ]);

        Mailer::sendWelcome(['email' => $email, 'name' => $name], $tempPassword);

        Security::flash('success', "Customer {$name} created. Welcome email sent.");
        Security::redirect('/admin/customers/' . $userId);
    }

    public function viewCustomer(int $id): void
    {
        Auth::requireAdmin();
        $customer = User::find($id);
        if (!$customer || $customer['role'] !== 'customer') {
            Security::redirect('/admin/customers');
        }

        $stats    = User::stats($id);
        $tickets  = Ticket::forUser($id);
        $invoices = Invoice::forUser($id);

        View::render('admin/customer-view', [
            'title'    => $customer['name'],
            'customer' => $customer,
            'stats'    => $stats,
            'tickets'  => $tickets,
            'invoices' => $invoices,
        ], 'admin');
    }

    public function toggleCustomer(int $id): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();
        User::toggleActive($id);
        Security::flash('success', 'Customer status updated.');
        Security::redirect('/admin/customers/' . $id);
    }

    // ── Tickets ───────────────────────────────────────────────────────────────

    public function tickets(): void
    {
        Auth::requireAdmin();
        $filters = [];
        if (!empty($_GET['status']))   $filters['status']   = $_GET['status'];
        if (!empty($_GET['priority'])) $filters['priority'] = $_GET['priority'];

        View::render('admin/tickets', [
            'title'   => 'All Tickets',
            'tickets' => Ticket::all($filters),
            'filters' => $filters,
        ], 'admin');
    }

    public function viewTicket(int $id): void
    {
        Auth::requireAdmin();
        $ticket = Ticket::find($id);
        if (!$ticket) Security::redirect('/admin/tickets');

        $messages = Ticket::messages($id, true);

        View::render('admin/ticket-view', [
            'title'    => "Ticket #{$ticket['reference']}",
            'ticket'   => $ticket,
            'messages' => $messages,
        ], 'admin');
    }

    public function replyTicket(int $id): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        $ticket  = Ticket::find($id);
        if (!$ticket) Security::redirect('/admin/tickets');

        $message    = trim($_POST['message'] ?? '');
        $isInternal = isset($_POST['internal']);

        if (!$message) {
            Security::flash('error', 'Reply cannot be empty.');
            Security::redirect('/admin/tickets/' . $id);
        }

        Ticket::addMessage($id, Auth::id(), $message, $isInternal);

        if (!$isInternal) {
            Ticket::updateStatus($id, 'waiting_customer');
            $customer = User::find($ticket['user_id']);
            if ($customer) {
                Mailer::sendTicketReply(
                    $ticket,
                    ['message' => $message, 'sender_name' => Auth::user()['name']],
                    $customer,
                    true
                );
            }
        }

        Security::flash('success', $isInternal ? 'Internal note added.' : 'Reply sent to customer.');
        Security::redirect('/admin/tickets/' . $id);
    }

    public function updateTicketStatus(int $id): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])) {
            Security::redirect('/admin/tickets/' . $id);
        }

        Ticket::updateStatus($id, $status);
        Security::flash('success', 'Status updated.');
        Security::redirect('/admin/tickets/' . $id);
    }

    // ── Invoices ─────────────────────────────────────────────────────────────

    public function invoices(): void
    {
        Auth::requireAdmin();
        Invoice::updateOverdue();
        View::render('admin/invoices', [
            'title'    => 'Invoices',
            'invoices' => Invoice::all(),
        ], 'admin');
    }

    public function createInvoice(): void
    {
        Auth::requireAdmin();
        View::render('admin/invoice-create', [
            'title'     => 'Create Invoice',
            'customers' => User::customers(),
            'nextNum'   => Invoice::nextNumber(),
        ], 'admin');
    }

    public function storeInvoice(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        $userId    = (int)($_POST['user_id'] ?? 0);
        $lineItems = [];

        $descriptions = $_POST['item_desc'] ?? [];
        $qtys         = $_POST['item_qty'] ?? [];
        $prices       = $_POST['item_price'] ?? [];

        foreach ($descriptions as $i => $desc) {
            if (trim($desc) && isset($qtys[$i], $prices[$i])) {
                $lineItems[] = [
                    'description' => trim($desc),
                    'qty'         => (float)$qtys[$i],
                    'unit_price'  => (float)$prices[$i],
                ];
            }
        }

        if (!$userId || !$lineItems) {
            Security::flash('error', 'Customer and at least one line item are required.');
            Security::redirect('/admin/invoices/create');
        }

        $invoiceId = Invoice::create([
            'user_id'        => $userId,
            'invoice_number' => $_POST['invoice_number'] ?? Invoice::nextNumber(),
            'vat_rate'       => (float)($_POST['vat_rate'] ?? 20),
            'due_date'       => $_POST['due_date'] ?? null,
            'notes'          => trim($_POST['notes'] ?? ''),
            'line_items'     => $lineItems,
        ]);

        if (isset($_POST['send_email'])) {
            $invoice  = Invoice::find($invoiceId);
            $customer = User::find($userId);
            if ($invoice && $customer) Mailer::sendInvoiceReady($invoice, $customer);
        }

        Security::flash('success', 'Invoice created.');
        Security::redirect('/admin/invoices');
    }

    // ── Settings ──────────────────────────────────────────────────────────────

    public function settings(): void
    {
        Auth::requireAdmin();
        View::render('admin/settings', [
            'title'    => 'Portal Settings',
            'settings' => Setting::all(),
        ], 'admin');
    }

    public function saveSettings(): void
    {
        Auth::requireAdmin();
        Security::checkCsrf();

        Setting::saveMany([
            'app_name'      => trim($_POST['app_name'] ?? ''),
            'primary_color' => $_POST['primary_color'] ?? '#2563eb',
            'primary_dark'  => $_POST['primary_dark']  ?? '#1d4ed8',
            'sidebar_bg'    => $_POST['sidebar_bg']    ?? '#0f172a',
            'sidebar_text'  => $_POST['sidebar_text']  ?? '#94a3b8',
            'sidebar_active'=> $_POST['sidebar_active']?? '#2563eb',
            'support_email' => trim($_POST['support_email'] ?? ''),
        ]);

        // Handle logo upload
        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/webp'];
            $finfo   = new \finfo(FILEINFO_MIME_TYPE);
            $mime    = $finfo->file($_FILES['logo']['tmp_name']);

            if (in_array($mime, $allowed) && $_FILES['logo']['size'] <= 2097152) {
                $ext      = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $logoPath = dirname(__DIR__, 3) . '/public/assets/img/logo.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);

                // If extension changed, save it so templates can reference it
                Setting::set('logo_ext', $ext);
            } else {
                Security::flash('error', 'Logo must be PNG, JPG, SVG or WebP under 2MB.');
                Security::redirect('/admin/settings');
            }
        }

        Security::flash('success', 'Settings saved.');
        Security::redirect('/admin/settings');
    }
}
