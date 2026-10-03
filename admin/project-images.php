<?php
/**
 * Project Media & Screenshots Manager
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$db = get_db();
$projectId = (int)($_GET['project_id'] ?? 0);

if ($projectId <= 0) {
    set_flash('error', 'Invalid project specified.');
    header('Location: ' . url('admin/projects.php'));
    exit;
}

$stmt = $db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $projectId]);
$project = $stmt->fetch();

if (!$project) {
    set_flash('error', 'Project not found.');
    header('Location: ' . url('admin/projects.php'));
    exit;
}

$adminPageTitle = 'Manage Screenshots: ' . $project['title'];
$errors = [];

// Handle Media Operations: Upload, Set Featured, Delete, Update Caption
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'upload_screenshot') {
            $caption = trim($_POST['caption'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($_FILES['image']['name'])) {
                $errors[] = 'Please select an image file to upload.';
            } else {
                $uploadRes = upload_file($_FILES['image'], 'projects');
                if ($uploadRes['success']) {
                    // If setting as featured, un-feature others
                    if ($isFeatured) {
                        $db->prepare("UPDATE project_images SET is_featured = 0 WHERE project_id = :pid")->execute(['pid' => $projectId]);
                    }

                    $insertStmt = $db->prepare("INSERT INTO project_images (project_id, image_path, caption, is_featured, display_order) VALUES (:pid, :path, :cap, :feat, :ord)");
                    $insertStmt->execute([
                        'pid'  => $projectId,
                        'path' => $uploadRes['relative_path'],
                        'cap'  => $caption,
                        'feat' => $isFeatured,
                        'ord'  => $displayOrder
                    ]);

                    set_flash('success', 'Screenshot uploaded successfully.');
                    header('Location: ' . url('admin/project-images.php?project_id=' . $projectId));
                    exit;
                } else {
                    $errors[] = $uploadRes['error'];
                }
            }
        } elseif ($action === 'set_featured') {
            $imageId = (int)($_POST['image_id'] ?? 0);
            if ($imageId > 0) {
                $db->prepare("UPDATE project_images SET is_featured = 0 WHERE project_id = :pid")->execute(['pid' => $projectId]);
                $db->prepare("UPDATE project_images SET is_featured = 1 WHERE id = :id AND project_id = :pid")->execute(['id' => $imageId, 'pid' => $projectId]);
                set_flash('success', 'Primary featured screenshot updated.');
                header('Location: ' . url('admin/project-images.php?project_id=' . $projectId));
                exit;
            }
        } elseif ($action === 'delete_image') {
            $imageId = (int)($_POST['image_id'] ?? 0);
            if ($imageId > 0) {
                $imgStmt = $db->prepare("SELECT image_path FROM project_images WHERE id = :id AND project_id = :pid LIMIT 1");
                $imgStmt->execute(['id' => $imageId, 'pid' => $projectId]);
                $imgPath = $imgStmt->fetchColumn();

                if ($imgPath) {
                    $fullDiskPath = UPLOADS_PATH . '/' . $imgPath;
                    if (file_exists($fullDiskPath)) {
                        @unlink($fullDiskPath);
                    }
                    $db->prepare("DELETE FROM project_images WHERE id = :id")->execute(['id' => $imageId]);
                    set_flash('success', 'Screenshot removed.');
                }
                header('Location: ' . url('admin/project-images.php?project_id=' . $projectId));
                exit;
            }
        } elseif ($action === 'update_image') {
            $imageId = (int)($_POST['image_id'] ?? 0);
            $caption = trim($_POST['caption'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if ($imageId > 0) {
                $db->prepare("UPDATE project_images SET caption = :cap, display_order = :ord WHERE id = :id AND project_id = :pid")
                   ->execute(['cap' => $caption, 'ord' => $displayOrder, 'id' => $imageId, 'pid' => $projectId]);
                set_flash('success', 'Image details saved.');
                header('Location: ' . url('admin/project-images.php?project_id=' . $projectId));
                exit;
            }
        }
    }
}

// Fetch all project images
$images = [];
try {
    $stmt = $db->prepare("SELECT * FROM project_images WHERE project_id = :pid ORDER BY is_featured DESC, display_order ASC, id ASC");
    $stmt->execute(['pid' => $projectId]);
    $images = $stmt->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <a href="<?= url('admin/projects.php') ?>" style="color: var(--admin-text-muted); font-size: 0.85rem; text-decoration: none;">&larr; Back to Projects</a>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0.5rem 0 0 0;">Screenshots: <?= e($project['title']) ?></h2>
            </div>
            <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Project Page ↗
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

        <!-- Upload New Screenshot Card -->
        <div class="admin-card" style="margin-bottom: 2rem;">
            <div class="admin-card-header">
                <h3 class="admin-card-title">+ Upload New Screenshot / Visual</h3>
            </div>
            <div class="admin-card-body">
                <form action="<?= url('admin/project-images.php?project_id=' . $projectId) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload_screenshot">

                    <div style="display: grid; grid-template-columns: 1.5fr 2fr 1fr; gap: 1.5rem; align-items: end;">
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label class="admin-label">Screenshot File <span style="color:#ef4444;">*</span></label>
                            <input type="file" name="image" class="admin-input" accept="image/*" required>
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label for="caption" class="admin-label">Caption / Description</label>
                            <input type="text" name="caption" id="caption" class="admin-input" placeholder="e.g. Mentor dashboard and session scheduler">
                        </div>

                        <div>
                            <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                                Upload Image
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; gap: 2rem; margin-top: 1rem; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-text-main); font-size: 0.875rem; cursor: pointer;">
                            <input type="checkbox" name="is_featured" value="1" <?= empty($images) ? 'checked' : '' ?>>
                            <span>Set as primary featured image for project card</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                            <label for="display_order" style="color: var(--admin-text-muted);">Display Order:</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="0" style="width: 80px; padding: 0.4rem 0.6rem;">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Screenshots Gallery & Management Grid -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Project Gallery (<?= count($images) ?> images)</h3>
            </div>
            <div class="admin-card-body">
                <?php if (!empty($images)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($images as $img): ?>
                            <div style="background-color: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
                                <div style="position: relative; aspect-ratio: 16/9; background-color: #000; overflow: hidden;">
                                    <img src="<?= e(upload_url($img['image_path'], 'projects')) ?>" alt="<?= e($img['caption']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php if ($img['is_featured']): ?>
                                        <div style="position: absolute; top: 0.5rem; left: 0.5rem; background: #3b82f6; color: #fff; font-size: 0.725rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                            ★ Primary Featured
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div style="padding: 1rem; flex: 1; display: flex; flex-direction: column;">
                                    <form action="<?= url('admin/project-images.php?project_id=' . $projectId) ?>" method="POST" style="margin-bottom: 0.75rem;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update_image">
                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">

                                        <div style="margin-bottom: 0.5rem;">
                                            <input type="text" name="caption" class="admin-input" value="<?= e($img['caption']) ?>" placeholder="Caption..." style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                                        </div>
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <span style="font-size: 0.775rem; color: var(--admin-text-muted);">Order:</span>
                                            <input type="number" name="display_order" class="admin-input" value="<?= (int)$img['display_order'] ?>" style="width: 65px; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                                            <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm" style="margin-left: auto;">Save</button>
                                        </div>
                                    </form>

                                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border); padding-top: 0.75rem; margin-top: auto;">
                                        <?php if (!$img['is_featured']): ?>
                                            <form action="<?= url('admin/project-images.php?project_id=' . $projectId) ?>" method="POST" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="set_featured">
                                                <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                                <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                    Set as Primary
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 0.775rem; color: var(--admin-primary); font-weight: 600;">Main Preview</span>
                                        <?php endif; ?>

                                        <form action="<?= url('admin/project-images.php?project_id=' . $projectId) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_image">
                                            <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this screenshot?">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; color: var(--admin-text-muted);">
                        No screenshots uploaded for this project yet. Use the upload form above to add your first screenshot.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
