<?php use App\Core\Security; ?>
<div class="page-header">
    <div>
        <a href="<?= $customer ? '/admin/customers/' . $customer['id'] : '/admin/tickets' ?>" style="font-size:14px;color:#64748b;">&larr; <?= $customer ? Security::e($customer['name']) : 'All Tickets' ?></a>
        <h1 style="margin-top:4px;">New Ticket</h1>
    </div>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="/admin/tickets/create">
            <?= Security::csrfField() ?>

            <div class="form-group">
                <label>Customer <span class="required">*</span></label>
                <select name="customer_id" required>
                    <option value="">— Select customer —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($customer && $c['id'] === $customer['id']) ? 'selected' : '' ?>>
                        <?= Security::e($c['name']) ?><?= $c['company'] ? ' — ' . Security::e($c['company']) : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Subject <span class="required">*</span></label>
                <input type="text" name="subject" required placeholder="Brief description of the issue" value="<?= Security::e($_POST['subject'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority">
                        <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($_POST['priority'] ?? 'medium') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <?php foreach (['general' => 'General', 'technical' => 'Technical', 'billing' => 'Billing', 'account' => 'Account'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($_POST['category'] ?? 'general') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Message <span class="required">*</span></label>
                <textarea name="message" rows="6" required placeholder="Describe the issue in detail..."><?= Security::e($_POST['message'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Ticket</button>
                <a href="<?= $customer ? '/admin/customers/' . $customer['id'] : '/admin/tickets' ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
