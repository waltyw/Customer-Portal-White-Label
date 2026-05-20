<?php use App\Core\Security; ?>
<div class="page-header">
    <h1>New Support Ticket</h1>
    <a href="/tickets" class="btn btn-outline">&larr; Back to Tickets</a>
</div>

<div class="card" style="max-width:760px;">
    <div class="card-body">
        <form method="POST" action="/tickets/create" enctype="multipart/form-data">
            <?= Security::csrfField() ?>

            <div class="form-group">
                <label for="subject">Subject <span class="required">*</span></label>
                <input type="text" id="subject" name="subject" required maxlength="500"
                    placeholder="Brief description of your issue"
                    value="<?= Security::e($_POST['subject'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="general">General</option>
                        <option value="technical">Technical</option>
                        <option value="billing">Billing</option>
                        <option value="account">Account</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" required rows="8"
                    placeholder="Please describe your issue in as much detail as possible..."><?= Security::e($_POST['message'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="attachment">Attachment <span class="hint">(optional — max 5MB, images/PDF/docs)</span></label>
                <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Submit Ticket</button>
                <a href="/tickets" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
