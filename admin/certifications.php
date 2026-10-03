<?php
/**
 * Certifications Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Certifications Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM certifications WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_certification') {
            $id = (int)($_POST['cert_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $issueDate = trim($_POST['issue_date'] ?? '');
            $expDate = trim($_POST['expiration_date'] ?? 'No Expiration');
            $credId = trim($_POST['credential_id'] ?? '');
            $credUrl = trim($_POST['credential_url'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($title) || empty($issuer)) {
                $errors[] = 'Title and Issuer are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE certifications SET title = :ttl, issuer = :isr, issue_date = :idt, expiration_date = :edt, credential_id = :cid, credential_url = :curl, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'ttl' => $title, 'isr' => $issuer, 'idt' => $issueDate,
                        'edt' => $expDate, 'cid' => $credId, 'curl' => $credUrl,
                        'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Certification updated.');
                } else {
                    $stmt = $db->prepare("INSERT INTO certifications (title, issuer, issue_date, expiration_date, credential_id, credential_url, display_order) VALUES (:ttl, :isr, :idt, :edt, :cid, :curl, :ord)");
                    $stmt->execute([
                        'ttl' => $title, 'isr' => $issuer, 'idt' => $issueDate,
                        'edt' => $expDate, 'cid' => $credId, 'curl' => $credUrl,
                        'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Certification added.');
                }
                header('Location: ' . url('admin/certifications.php'));
                exit;
            }
        } elseif ($action === 'delete_certification') {
            $id = (int)($_POST['cert_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM certifications WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Certification deleted.');
                header('Location: ' . url('admin/certifications.php'));
                exit;
            }
        }
    }
}

$certList = $db->query("SELECT * FROM certifications ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Certifications &amp; Credentials</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage verified certifications, credential IDs, and issuer verification links.
                </p>
            </div>
            <a href="<?= url('certifications.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Certifications ↗
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: start;">
            <!-- Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Credential' : '+ Add Certification' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/certifications.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/certifications.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_certification">
                        <input type="hidden" name="cert_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="title" class="admin-label">Certification Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" id="title" class="admin-input" placeholder="e.g. Full Stack Web Development" value="<?= e($editItem['title'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="issuer" class="admin-label">Issuing Organization <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="issuer" id="issuer" class="admin-input" placeholder="e.g. Coursera / HackerRank / Meta" value="<?= e($editItem['issuer'] ?? '') ?>" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="issue_date" class="admin-label">Issue Date</label>
                                <input type="text" name="issue_date" id="issue_date" class="admin-input" placeholder="2024" value="<?= e($editItem['issue_date'] ?? '') ?>">
                            </div>
                            <div class="admin-form-group">
                                <label for="expiration_date" class="admin-label">Expiration Date</label>
                                <input type="text" name="expiration_date" id="expiration_date" class="admin-input" placeholder="No Expiration" value="<?= e($editItem['expiration_date'] ?? 'No Expiration') ?>">
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="credential_id" class="admin-label">Credential ID</label>
                            <input type="text" name="credential_id" id="credential_id" class="admin-input" placeholder="e.g. CERT-12345" value="<?= e($editItem['credential_id'] ?? '') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="credential_url" class="admin-label">Verification URL</label>
                            <input type="url" name="credential_url" id="credential_url" class="admin-input" placeholder="https://..." value="<?= e($editItem['credential_url'] ?? '') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Certification' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Certifications List</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($certList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Title &amp; Issuer</th>
                                        <th>Date</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($certList as $c): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #fff;"><?= e($c['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($c['issuer']) ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.825rem; color: var(--admin-text-dim);"><?= e($c['issue_date']) ?></span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/certifications.php?edit=' . $c['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/certifications.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_certification">
                                                        <input type="hidden" name="cert_id" value="<?= $c['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this certification?">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted);">
                            No certifications found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
