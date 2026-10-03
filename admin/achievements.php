<?php
/**
 * Achievements & Highlights Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Achievements Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM achievements WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_achievement') {
            $id = (int)($_POST['achievement_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? 'Academic');
            $date = trim($_POST['achievement_date'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $badge = trim($_POST['highlight_badge'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($title) || empty($desc)) {
                $errors[] = 'Title and description are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE achievements SET title = :ttl, category = :cat, achievement_date = :dt, description = :dsc, highlight_badge = :bdg, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'ttl' => $title, 'cat' => $category, 'dt' => $date,
                        'dsc' => $desc, 'bdg' => $badge, 'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Achievement updated.');
                } else {
                    $stmt = $db->prepare("INSERT INTO achievements (title, category, achievement_date, description, highlight_badge, display_order) VALUES (:ttl, :cat, :dt, :dsc, :bdg, :ord)");
                    $stmt->execute([
                        'ttl' => $title, 'cat' => $category, 'dt' => $date,
                        'dsc' => $desc, 'bdg' => $badge, 'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Achievement added.');
                }
                header('Location: ' . url('admin/achievements.php'));
                exit;
            }
        } elseif ($action === 'delete_achievement') {
            $id = (int)($_POST['achievement_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM achievements WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Achievement removed.');
                header('Location: ' . url('admin/achievements.php'));
                exit;
            }
        }
    }
}

$achievementsList = $db->query("SELECT * FROM achievements ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Achievements &amp; Honors</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage academic highlights, competitions, and notable milestones.
                </p>
            </div>
            <a href="<?= url('achievements.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Achievements ↗
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
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Milestone' : '+ Add Achievement' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/achievements.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/achievements.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_achievement">
                        <input type="hidden" name="achievement_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="title" class="admin-label">Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" id="title" class="admin-input" placeholder="e.g. BCA Degree Distinction" value="<?= e($editItem['title'] ?? '') ?>" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="category" class="admin-label">Category</label>
                                <input type="text" name="category" id="category" class="admin-input" placeholder="e.g. Academic / Hackathon" value="<?= e($editItem['category'] ?? 'Academic') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="achievement_date" class="admin-label">Date / Year</label>
                                <input type="text" name="achievement_date" id="achievement_date" class="admin-input" placeholder="2024" value="<?= e($editItem['achievement_date'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="highlight_badge" class="admin-label">Highlight Badge Text</label>
                            <input type="text" name="highlight_badge" id="highlight_badge" class="admin-input" placeholder="e.g. Distinction / Finalist" value="<?= e($editItem['highlight_badge'] ?? '') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="description" class="admin-label">Description <span style="color:#ef4444;">*</span></label>
                            <textarea name="description" id="description" class="admin-textarea" rows="3" placeholder="Explain the achievement..." required><?= e($editItem['description'] ?? '') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Milestone' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Achievements List</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($achievementsList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Title &amp; Category</th>
                                        <th>Date</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($achievementsList as $a): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #fff;"><?= e($a['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);">
                                                    <?= e($a['category']) ?>
                                                    <?php if (!empty($a['highlight_badge'])): ?>
                                                        • <span style="color: #10b981;"><?= e($a['highlight_badge']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.825rem; color: var(--admin-text-dim);"><?= e($a['achievement_date']) ?></span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/achievements.php?edit=' . $a['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/achievements.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_achievement">
                                                        <input type="hidden" name="achievement_id" value="<?= $a['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this achievement?">Delete</button>
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
                            No achievements recorded.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
