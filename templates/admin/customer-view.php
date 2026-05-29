<?php use App\Core\Security; ?>
<div class="page-header">
    <div>
        <a href="/admin/customers" style="font-size:14px;color:#64748b;">&larr; All Customers</a>
        <h1 style="margin-top:4px;"><?= Security::e($customer['name']) ?></h1>
        <div style="color:#64748b;margin-top:4px;"><?= Security::e($customer['email']) ?></div>
    </div>
    <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/toggle">
        <?= Security::csrfField() ?>
        <button type="submit" class="btn <?= $customer['is_active'] ? 'btn-danger-outline' : 'btn-outline' ?>">
            <?= $customer['is_active'] ? 'Deactivate Account' : 'Activate Account' ?>
        </button>
    </form>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-body">
            <div class="stat-value"><?= (int)$stats['ticket_count'] ?></div>
            <div class="stat-label">Total Tickets</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-body">
            <div class="stat-value"><?= (int)$stats['invoice_count'] ?></div>
            <div class="stat-label">Unpaid Invoices</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-body">
            <div class="stat-value" style="color:#dc2626;">£<?= number_format($stats['amount_outstanding'], 2) ?></div>
            <div class="stat-label">Outstanding</div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h2>Customer Details</h2></div>
        <div class="card-body">
            <table style="width:100%;font-size:14px;">
                <tr><td style="color:#64748b;padding:6px 0;width:40%;">Company</td><td><?= Security::e($customer['company'] ?? '—') ?></td></tr>
                <tr><td style="color:#64748b;padding:6px 0;">Phone</td><td><?= Security::e($customer['phone'] ?? '—') ?></td></tr>
                <tr><td style="color:#64748b;padding:6px 0;">Website</td><td>
                    <?php if ($customer['website_url']): ?>
                        <a href="<?= Security::e($customer['website_url']) ?>" target="_blank" rel="noopener"><?= Security::e($customer['website_url']) ?></a>
                    <?php else: ?>—<?php endif; ?>
                </td></tr>
                <tr><td style="color:#64748b;padding:6px 0;">Mail Server</td><td>
                    <?php $ms = \App\Models\User::mailServer($customer['website_url'] ?? null); ?>
                    <?= $ms ? Security::e($ms) : '—' ?>
                </td></tr>
                <tr><td style="color:#64748b;padding:6px 0;">Status</td><td><span class="badge <?= $customer['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $customer['is_active'] ? 'Active' : 'Inactive' ?></span></td></tr>
                <tr><td style="color:#64748b;padding:6px 0;">Member Since</td><td><?= date('j F Y', strtotime($customer['created_at'])) ?></td></tr>
            </table>
        </div>
    </div>

    <div class="card p-0">
        <div class="card-header" style="padding:16px 20px;"><h2>Recent Tickets</h2><a href="/admin/customers/<?= $customer['id'] ?>" class="btn btn-sm btn-primary" style="margin-left:auto;">+ New Ticket</a></div>
        <?php if (empty($tickets)): ?>
        <div class="empty-state">No tickets</div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Ref</th><th>Subject</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach (array_slice($tickets, 0, 5) as $t): ?>
            <tr onclick="location.href='/admin/tickets/<?= $t['id'] ?>'" style="cursor:pointer;">
                <td><code><?= Security::e($t['reference']) ?></code></td>
                <td><?= Security::e($t['subject']) ?></td>
                <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst(str_replace('_', ' ', $t['status'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Invoices -->
<div class="card p-0" style="margin-top:16px;">
    <div class="card-header" style="padding:16px 20px;">
        <h2>Invoices</h2>
        <a href="/admin/invoices/create?customer=<?= $customer['id'] ?>" class="btn btn-sm btn-primary">+ New Invoice</a>
    </div>
    <?php if (empty($invoices)): ?>
    <div class="empty-state">No invoices</div>
    <?php else: ?>
    <table class="table">
        <thead><tr><th>Invoice</th><th>Total</th><th>Due</th><th>Status</th><th>Due Date</th></tr></thead>
        <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr onclick="location.href='/admin/invoices'" style="cursor:pointer;">
            <td><?= Security::e($inv['invoice_number']) ?></td>
            <td>£<?= number_format((float)$inv['total'], 2) ?></td>
            <td>£<?= number_format((float)$inv['amount_due'], 2) ?></td>
            <td><span class="badge badge-<?= $inv['status'] ?>"><?= ucfirst($inv['status']) ?></span></td>
            <td class="text-muted"><?= $inv['due_date'] ? date('j M Y', strtotime($inv['due_date'])) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
