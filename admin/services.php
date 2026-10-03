<?php
/**
 * Services Offered Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Services Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM services WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_service') {
            $id = (int)($_POST['service_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $shortDesc = trim($_POST['short_description'] ?? '');
            $fullDesc = trim($_POST['full_description'] ?? '');
            $deliverables = trim($_POST['deliverables'] ?? '');
            $icon = trim($_POST['icon'] ?? 'globe');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($title) || empty($shortDesc)) {
                $errors[] = 'Title and short description are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE services SET title = :ttl, short_description = :sd, full_description = :fd, deliverables = :del, icon = :ico, is_active = :act, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'ttl' => $title, 'sd' => $shortDesc, 'fd' => $fullDesc,
                        'del' => $deliverables, 'ico' => $icon, 'act' => $isActive,
                        'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Service updated.');
                } else {
                    $stmt = $db->prepare("INSERT INTO services (title, short_description, full_description, deliverables, icon, is_active, display_order) VALUES (:ttl, :sd, :fd, :del, :ico, :act, :ord)");
                    $stmt->execute([
                        'ttl' => $title, 'sd' => $shortDesc, 'fd' => $fullDesc,
                        'del' => $deliverables, 'ico' => $icon, 'act' => $isActive,
                        'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Service added.');
                }
                header('Location: ' . url('admin/services.php'));
                exit;
            }
        } elseif ($action === 'delete_service') {
            $id = (int)($_POST['service_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM services WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Service deleted.');
                header('Location: ' . url('admin/services.php'));
                exit;
            }
        }
    }
}

$servicesList = $db->query("SELECT * FROM services ORDER BY display_order ASC, id ASC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Services Offered</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage technical services, solution descriptions, and key deliverables.
                </p>
            </div>
            <a href="<?= url('services.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Services Page ↗
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
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Service' : '+ Add Service' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/services.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/services.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_service">
                        <input type="hidden" name="service_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="title" class="admin-label">Service Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" id="title" class="admin-input" placeholder="e.g. Full Stack Web Development" value="<?= e($editItem['title'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="short_description" class="admin-label">Short Summary <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="short_description" id="short_description" class="admin-input" placeholder="One sentence summary for cards" value="<?= e($editItem['short_description'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="full_description" class="admin-label">Detailed Description</label>
                            <textarea name="full_description" id="full_description" class="admin-textarea" rows="3" placeholder="Explain the service approach, technologies, and workflow..."><?= e($editItem['full_description'] ?? '') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="deliverables" class="admin-label">Key Deliverables (One per line)</label>
                            <textarea name="deliverables" id="deliverables" class="admin-textarea" rows="3" placeholder="Database schema design&#10;RESTful API documentation&#10;Responsive UI templates"><?= e($editItem['deliverables'] ?? '') ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="icon" class="admin-label">Icon Type</label>
                                <select name="icon" id="icon" class="admin-select">
                                    <option value="globe" <?= ($editItem && $editItem['icon'] === 'globe') ? 'selected' : '' ?>>Globe (Web App)</option>
                                    <option value="database" <?= ($editItem && $editItem['icon'] === 'database') ? 'selected' : '' ?>>Database (SQL / Storage)</option>
                                    <option value="cpu" <?= ($editItem && $editItem['icon'] === 'cpu') ? 'selected' : '' ?>>CPU (REST API / Backend)</option>
                                    <option value="layout" <?= ($editItem && $editItem['icon'] === 'layout') ? 'selected' : '' ?>>Layout (Frontend UI)</option>
                                </select>
                            </div>
                            <div class="admin-form-group">
                                <label for="display_order" class="admin-label">Display Order</label>
                                <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #fff; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_active" value="1" <?= (!isset($editItem['is_active']) || $editItem['is_active']) ? 'checked' : '' ?>>
                                <span>Active (Visible on public services page)</span>
                            </label>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Service' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Services List</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($servicesList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicesList as $s): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #fff;"><?= e($s['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($s['short_description']) ?></div>
                                            </td>
                                            <td>
                                                <?= $s['is_active'] ? '<span style="color:#10b981;font-size:0.8rem;font-weight:600;">Active</span>' : '<span style="color:var(--admin-text-dim);font-size:0.8rem;">Hidden</span>' ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/services.php?edit=' . $s['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/services.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_service">
                                                        <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this service?">Delete</button>
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
                            No services added yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
