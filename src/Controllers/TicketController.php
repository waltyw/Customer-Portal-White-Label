<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Core\Security;
use App\Core\View;
use App\Email\Mailer;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;

class TicketController
{
    private const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
    ];

    private const MAX_FILE_SIZE = 5242880; // 5MB

    public function index(): void
    {
        Auth::requireAuth();
        $tickets = Ticket::forUser(Auth::id());
        View::render('customer/tickets', ['title' => 'Support Tickets', 'tickets' => $tickets]);
    }

    public function create(): void
    {
        Auth::requireAuth();
        View::render('customer/ticket-create', [
            'title'    => 'New Support Ticket',
            'websites' => Website::forDropdown(Auth::id()),
        ]);
    }

    public function store(): void
    {
        Auth::requireAuth();
        Security::checkCsrf();

        $subject    = trim($_POST['subject'] ?? '');
        $message    = trim($_POST['message'] ?? '');
        $priority   = $_POST['priority'] ?? 'medium';
        $category   = $_POST['category'] ?? 'general';
        $websiteUrl = trim($_POST['website_url'] ?? '');

        if (!$subject || !$message) {
            Security::flash('error', 'Subject and message are required.');
            Security::redirect('/tickets/create');
        }

        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) $priority = 'medium';
        if (!in_array($category, ['billing', 'technical', 'general', 'account'])) $category = 'general';

        $userId   = Auth::id();
        $ticketId = Ticket::create($userId, [
            'subject'     => $subject,
            'priority'    => $priority,
            'category'    => $category,
            'website_url' => $websiteUrl ?: null,
        ]);
        $msgId    = Ticket::addMessage($ticketId, $userId, $message);

        if (!empty($_FILES['attachment']['name'])) {
            $this->handleAttachment($msgId, $_FILES['attachment']);
        }

        $ticket = Ticket::find($ticketId);

        // Notify admins
        $admins = \App\Core\DB::fetchAll("SELECT email, name FROM users WHERE role = 'admin' AND is_active = 1");
        foreach ($admins as $admin) {
            $html = "<p>New support ticket from <strong>{$ticket['customer_name']}</strong>:</p><p><strong>#{$ticket['reference']}</strong>: " . Security::e($subject) . "</p><p>Priority: <strong>" . ucfirst($priority) . "</strong></p><p><a href=\"{$_ENV['APP_URL']}/admin/tickets/{$ticketId}\" style=\"background:#2563eb;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;\">View Ticket</a></p>";
            Mailer::send($admin['email'], $admin['name'], "[New Ticket #{$ticket['reference']}] " . $subject, $html);
        }

        Security::flash('success', "Ticket #{$ticket['reference']} submitted. We'll get back to you soon.");
        Security::redirect('/tickets/' . $ticketId);
    }

    public function show(int $id): void
    {
        Auth::requireAuth();
        $ticket = Ticket::findForUser($id, Auth::id());
        if (!$ticket) { http_response_code(404); die('Ticket not found.'); }

        $messages = Ticket::messages($id, false);

        $attachmentMap = [];
        foreach ($messages as $msg) {
            $atts = Ticket::attachments((int)$msg['id']);
            if ($atts) $attachmentMap[$msg['id']] = $atts;
        }

        View::render('customer/ticket-view', [
            'title'         => "Ticket #{$ticket['reference']}",
            'ticket'        => $ticket,
            'messages'      => $messages,
            'attachmentMap' => $attachmentMap,
        ]);
    }

    public function serveAttachment(): void
    {
        Auth::requireAuth();

        $file = $_GET['file'] ?? '';
        if (!preg_match('/^[a-f0-9]{32}\.[a-z0-9]{1,10}$/', $file)) {
            http_response_code(400); exit('Invalid file.');
        }

        $row = \App\Core\DB::fetchOne(
            'SELECT ta.*, tm.ticket_id FROM ticket_attachments ta
             JOIN ticket_messages tm ON ta.ticket_message_id = tm.id
             WHERE ta.filename = ?',
            [$file]
        );
        if (!$row) { http_response_code(404); exit('Not found.'); }

        $ticket = Ticket::findForUser((int)$row['ticket_id'], Auth::id());
        if (!$ticket) { http_response_code(403); exit('Access denied.'); }

        $path = dirname(__DIR__, 2) . '/storage/attachments/' . $file;
        if (!file_exists($path)) { http_response_code(404); exit('File not found on disk.'); }

        $isImage = str_starts_with($row['mime_type'], 'image/');
        header('Content-Type: ' . $row['mime_type']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . ($isImage ? 'inline' : 'attachment') . '; filename="' . rawurlencode($row['original_filename']) . '"');
        header('Cache-Control: private, max-age=3600');
        readfile($path);
        exit;
    }

    public function reply(int $id): void
    {
        Auth::requireAuth();
        Security::checkCsrf();

        $ticket = Ticket::findForUser($id, Auth::id());
        if (!$ticket) { http_response_code(404); die('Ticket not found.'); }

        if (in_array($ticket['status'], ['closed', 'resolved'])) {
            Security::flash('error', 'This ticket is closed. Please open a new ticket if you need further help.');
            Security::redirect('/tickets/' . $id);
        }

        $message = trim($_POST['message'] ?? '');
        if (!$message) {
            Security::flash('error', 'Message cannot be empty.');
            Security::redirect('/tickets/' . $id);
        }

        $userId = Auth::id();
        $msgId  = Ticket::addMessage($id, $userId, $message);
        Ticket::updateStatus($id, 'open');

        if (!empty($_FILES['attachment']['name'])) {
            $this->handleAttachment($msgId, $_FILES['attachment']);
        }

        // Notify admins
        $user   = User::find($userId);
        $admins = \App\Core\DB::fetchAll("SELECT email, name FROM users WHERE role = 'admin' AND is_active = 1");
        foreach ($admins as $admin) {
            Mailer::sendTicketReply($ticket, ['message' => $message, 'sender_name' => $user['name']], $admin, false);
        }

        Security::flash('success', 'Reply sent.');
        Security::redirect('/tickets/' . $id);
    }

    private function handleAttachment(int $msgId, array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return;
        if ($file['size'] > self::MAX_FILE_SIZE) return;

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, self::ALLOWED_MIME)) return;

        $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
        $stored     = bin2hex(random_bytes(16)) . '.' . $ext;
        $uploadDir  = dirname(__DIR__, 2) . '/storage/attachments/';

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $stored)) {
            try {
                Ticket::saveAttachment($msgId, [
                    'stored_name'   => $stored,
                    'original_name' => $file['name'],
                    'mime_type'     => $mimeType,
                    'size'          => $file['size'],
                ]);
            } catch (\Throwable $e) {
                error_log('Attachment save error: ' . $e->getMessage());
            }
        }
    }
}
