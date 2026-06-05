<?php use App\Core\Security; ?>
<div class="page-header">
    <div>
        <a href="/admin/tickets" style="font-size:14px;color:#64748b;">&larr; All Tickets</a>
        <h1 style="margin-top:4px;"><?= Security::e($ticket['subject']) ?></h1>
        <div style="display:flex;gap:8px;margin-top:6px;align-items:center;flex-wrap:wrap;">
            <code><?= Security::e($ticket['reference']) ?></code>
            <span class="badge badge-<?= $ticket['status'] ?>"><?= ucfirst(str_replace('_',' ',$ticket['status'])) ?></span>
            <span class="badge badge-priority-<?= $ticket['priority'] ?>"><?= ucfirst($ticket['priority']) ?></span>
            <a href="/admin/customers/<?= $ticket['user_id'] ?>" style="color:#2563eb;font-size:13px;"><?= Security::e($ticket['customer_name']) ?></a>
            <?php if (!empty($ticket['website_url'])): ?>
            <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:4px;font-size:12px;">
                🌐 <?= Security::e($ticket['website_url']) ?>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status update -->
    <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/status" style="display:flex;gap:8px;align-items:center;">
        <?= Security::csrfField() ?>
        <select name="status" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;">
            <?php foreach (['open','in_progress','waiting_customer','resolved','closed'] as $s): ?>
            <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Update Status</button>
    </form>
</div>

<!-- Thread -->
<div class="ticket-thread">
    <?php foreach ($messages as $msg): ?>
    <?php $isAdmin = $msg['sender_role'] === 'admin'; ?>
    <div class="ticket-message <?= $isAdmin ? 'message-admin' : 'message-customer' ?> <?= $msg['is_internal'] ? 'message-internal' : '' ?>">
        <div class="message-avatar" style="<?= $isAdmin ? 'background:#7c3aed;' : '' ?>"><?= strtoupper(substr($msg['sender_name'],0,1)) ?></div>
        <div class="message-body">
            <div class="message-header">
                <strong><?= Security::e($msg['sender_name']) ?></strong>
                <?php if ($isAdmin): ?><span class="badge-support">Support</span><?php endif; ?>
                <?php if ($msg['is_internal']): ?><span class="badge-internal">Internal Note</span><?php endif; ?>
                <span class="message-time"><?= date('j M Y, H:i', strtotime($msg['created_at'])) ?></span>
            </div>
            <div class="message-text"><?= nl2br(Security::e($msg['message'])) ?></div>
            <?php if (!empty($attachmentMap[$msg['id']])): ?>
            <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:8px;align-items:flex-start;">
                <?php foreach ($attachmentMap[$msg['id']] as $att): ?>
                <?php $isImage = str_starts_with($att['mime_type'], 'image/'); ?>
                <?php if ($isImage): ?>
                <a href="/admin/attachments?file=<?= urlencode($att['filename']) ?>" target="_blank" title="<?= Security::e($att['original_filename']) ?>">
                    <img src="/admin/attachments?file=<?= urlencode($att['filename']) ?>" alt="<?= Security::e($att['original_filename']) ?>" style="max-width:220px;max-height:160px;border-radius:6px;border:1px solid #e2e8f0;display:block;">
                </a>
                <?php else: ?>
                <a href="/admin/attachments?file=<?= urlencode($att['filename']) ?>" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;color:#2563eb;text-decoration:none;">
                    &#128206; <?= Security::e($att['original_filename']) ?>
                </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Reply / Internal note -->
<div class="card" style="margin-top:24px;max-width:760px;">
    <div class="card-header"><h2>Reply</h2></div>
    <div class="card-body">
        <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/reply">
            <?= Security::csrfField() ?>
            <div class="form-group">
                <textarea name="message" required rows="6" placeholder="Type your reply to the customer..."></textarea>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;">
                    <input type="checkbox" name="internal" value="1">
                    Internal note only (not visible to customer)
                </label>
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </div>
        </form>
    </div>
</div>
