<?php use App\Core\Security; ?>
<div class="page-header">
    <h1>Edit FAQ</h1>
    <a href="/admin/faqs" class="btn btn-outline">&larr; Back</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="/admin/faqs/<?= $faq['id'] ?>/update">
            <?= Security::csrfField() ?>
            <div class="form-group">
                <label>Question <span class="required">*</span></label>
                <input type="text" name="question" required value="<?= Security::e($faq['question']) ?>">
            </div>
            <div class="form-group">
                <label>Answer <span class="required">*</span></label>
                <textarea name="answer" required rows="6"><?= Security::e($faq['answer']) ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Sort Order <span class="hint">(lower = higher up)</span></label>
                    <input type="number" name="sort_order" value="<?= (int)$faq['sort_order'] ?>" min="0" step="10">
                </div>
                <div class="form-group" style="justify-content:flex-end;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:24px;">
                        <input type="checkbox" name="is_active" value="1" <?= $faq['is_active'] ? 'checked' : '' ?>>
                        Show on Help page
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/admin/faqs" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
