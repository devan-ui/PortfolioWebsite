<?php
/**
 * Social Media Links Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Social Links Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM social_links WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_social') {
            $id = (int)($_POST['social_id'] ?? 0);
            $platform = trim($_POST['platform'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $icon = trim($_POST['icon'] ?? 'link');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($platform) || empty($url)) {
                $errors[] = 'Platform name and URL are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE social_links SET platform = :plt, url = :url, icon = :ico, is_active = :act, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'plt' => $platform, 'url' => $url, 'ico' => $icon,
                        'act' => $isActive, 'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Social link updated.');
                } else {
                    $stmt = $db->prepare("INSERT INTO social_links (platform, url, icon, is_active, display_order) VALUES (:plt, :url, :ico, :act, :ord)");
                    $stmt->execute([
                        'plt' => $platform, 'url' => $url, 'ico' => $icon,
                        'act' => $isActive, 'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Social link added.');
                }
                header('Location: ' . url('admin/social-links.php'));
                exit;
            }
        } elseif ($action === 'delete_social') {
            $id = (int)($_POST['social_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM social_links WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Social link removed.');
                header('Location: ' . url('admin/social-links.php'));
                exit;
            }
        }
    }
}

$socialList = $db->query("SELECT * FROM social_links ORDER BY display_order ASC, id ASC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Social Media Profiles</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage external profiles displayed in the website header, footer, and contact sections.
                </p>
            </div>
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
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Social Link' : '+ Add Social Link' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/social-links.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/social-links.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_social">
                        <input type="hidden" name="social_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="platform" class="admin-label">Platform Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="platform" id="platform" class="admin-input" placeholder="e.g. GitHub / LinkedIn / Twitter" value="<?= e($editItem['platform'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="url" class="admin-label">Profile URL <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="url" id="url" class="admin-input" placeholder="https://github.com/..." value="<?= e($editItem['url'] ?? '') ?>" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="icon" class="admin-label">Icon Identifier</label>
                                <input type="text" name="icon" id="icon" class="admin-input" placeholder="github, linkedin, mail" value="<?= e($editItem['icon'] ?? 'link') ?>">
                            </div>
                            <div class="admin-form-group">
                                <label for="display_order" class="admin-label">Display Order</label>
                                <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #fff; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_active" value="1" <?= (!isset($editItem['is_active']) || $editItem['is_active']) ? 'checked' : '' ?>>
                                <span>Active (Visible on public site)</span>
                            </label>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Link' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Configured Links</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($socialList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Platform</th>
                                        <th>URL</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($socialList as $sl): ?>
                                        <tr>
                                            <td style="font-weight: 700; color: #fff;">
                                                <?= e($sl['platform']) ?>
                                            </td>
                                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.85rem;">
                                                <a href="<?= e($sl['url']) ?>" target="_blank" style="color: var(--admin-primary);"><?= e($sl['url']) ?></a>
                                            </td>
                                            <td>
                                                <?= $sl['is_active'] ? '<span style="color:#10b981;font-size:0.8rem;font-weight:600;">Active</span>' : '<span style="color:var(--admin-text-dim);font-size:0.8rem;">Inactive</span>' ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/social-links.php?edit=' . $sl['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/social-links.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_social">
                                                        <input type="hidden" name="social_id" value="<?= $sl['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Remove this social link?">Delete</button>
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
                            No social links configured.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
