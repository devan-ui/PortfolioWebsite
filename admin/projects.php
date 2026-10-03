<?php
/**
 * Projects Management Listing
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Projects Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();

// Handle Delete Project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token()) {
        set_flash('error', 'Invalid security token.');
    } else {
        $action = $_POST['action'];
        $projectId = (int)($_POST['project_id'] ?? 0);

        if ($action === 'delete' && $projectId > 0) {
            try {
                // Delete project images from disk
                $imgStmt = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :id");
                $imgStmt->execute(['id' => $projectId]);
                while ($img = $imgStmt->fetch()) {
                    $filePath = UPLOADS_PATH . '/' . $img['image_path'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM projects WHERE id = :id");
                $delStmt->execute(['id' => $projectId]);
                set_flash('success', 'Project deleted successfully.');
                header('Location: ' . url('admin/projects.php'));
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Failed to delete project: ' . $e->getMessage());
            }
        } elseif ($action === 'toggle_featured' && $projectId > 0) {
            try {
                $db->prepare("UPDATE projects SET featured = NOT featured WHERE id = :id")->execute(['id' => $projectId]);
                set_flash('success', 'Project featured status updated.');
                header('Location: ' . url('admin/projects.php'));
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Error toggling featured status.');
            }
        }
    }
}

// Fetch all projects with screenshots count
$projects = [];
try {
    $stmt = $db->query("SELECT p.*, (SELECT COUNT(*) FROM project_images WHERE project_id = p.id) AS image_count FROM projects p ORDER BY p.display_order ASC, p.id DESC");
    $projects = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Projects Portfolio</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage showcased web applications, screenshots, repositories, and case studies.
                </p>
            </div>
            <a href="<?= url('admin/project-edit.php') ?>" class="admin-btn admin-btn-primary">
                + Create New Project
            </a>
        </div>

        <div class="admin-card">
            <div class="admin-card-body" style="padding: 0;">
                <?php if (!empty($projects)): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title &amp; Slug</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Media</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                    <tr>
                                        <td style="font-family: var(--font-mono); color: var(--admin-text-dim);">
                                            #<?= $p['display_order'] ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #fff; font-size: 1rem;">
                                                <a href="<?= url('project.php?slug=' . urlencode($p['slug'])) ?>" target="_blank" style="color: #fff; text-decoration: none;">
                                                    <?= e($p['title']) ?> ↗
                                                </a>
                                            </div>
                                            <div style="font-size: 0.8rem; font-family: var(--font-mono); color: var(--admin-primary);">
                                                /<?= e($p['slug']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.825rem; color: var(--admin-text-muted);"><?= e($p['category']) ?></span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.775rem; color: #10b981; font-weight: 600;"><?= e($p['status']) ?></span>
                                        </td>
                                        <td>
                                            <form action="<?= url('admin/projects.php') ?>" method="POST" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="toggle_featured">
                                                <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="admin-btn admin-btn-sm <?= $p['featured'] ? 'admin-btn-primary' : 'admin-btn-secondary' ?>" title="Click to toggle featured on homepage">
                                                    <?= $p['featured'] ? '★ Featured' : '☆ Standard' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="<?= url('admin/project-images.php?project_id=' . $p['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                📸 Images (<?= $p['image_count'] ?>)
                                            </a>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: inline-flex; gap: 0.5rem;">
                                                <a href="<?= url('admin/project-edit.php?id=' . $p['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                    Edit
                                                </a>
                                                <form action="<?= url('admin/projects.php') ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Are you sure you want to permanently delete '<?= e($p['title']) ?>' and all associated screenshots?">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; color: var(--admin-text-muted);">
                        No projects created yet. Click "+ Create New Project" to add your first project.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
