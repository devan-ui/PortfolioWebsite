<?php
/**
 * SEO & Search Engine Optimization Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'SEO & Search Engine Settings';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editPage = null;
if ($editId > 0) {
    $eStmt = $db->prepare("SELECT * FROM seo_pages WHERE id = :id LIMIT 1");
    $eStmt->execute(['id' => $editId]);
    $editPage = $eStmt->fetch();
}

// Handle Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_seo') {
            $id = (int)($_POST['page_id'] ?? 0);
            $title = trim($_POST['meta_title'] ?? '');
            $desc = trim($_POST['meta_description'] ?? '');
            $keywords = trim($_POST['meta_keywords'] ?? '');
            $canonical = trim($_POST['canonical_url'] ?? '');

            if (empty($title) || empty($desc)) {
                $errors[] = 'Title and description are required for SEO tags.';
            }

            if (empty($errors) && $id > 0) {
                $stmt = $db->prepare("UPDATE seo_pages SET meta_title = :ttl, meta_description = :dsc, meta_keywords = :kw, canonical_url = :cn WHERE id = :id");
                $stmt->execute([
                    'ttl' => $title, 'dsc' => $desc, 'kw' => $keywords, 'cn' => $canonical, 'id' => $id
                ]);
                set_flash('success', 'SEO metadata updated successfully.');
                header('Location: ' . url('admin/seo.php'));
                exit;
            }
        }
    }
}

$seoPages = $db->query("SELECT * FROM seo_pages ORDER BY id ASC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Search Engine Optimization (SEO)</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage page meta titles, descriptions, canonical URLs, and sitemap crawling for Google discovery.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="<?= url('sitemap.xml') ?>" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm">
                    View sitemap.xml ↗
                </a>
                <a href="<?= url('robots.txt') ?>" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm">
                    View robots.txt ↗
                </a>
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

        <?php if ($editPage): ?>
            <!-- Edit SEO Modal / Form -->
            <div class="admin-card" style="border-color: var(--admin-primary); margin-bottom: 2.5rem;">
                <div class="admin-card-header" style="background: rgba(59, 130, 246, 0.1);">
                    <h3 class="admin-card-title">Editing SEO for: <code><?= e($editPage['page_identifier']) ?></code></h3>
                    <a href="<?= url('admin/seo.php') ?>" class="admin-btn admin-btn-secondary admin-btn-sm">&times; Cancel</a>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/seo.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_seo">
                        <input type="hidden" name="page_id" value="<?= $editPage['id'] ?>">

                        <div class="admin-form-group">
                            <label for="meta_title" class="admin-label">Meta Title (< 65 characters optimal)</label>
                            <input type="text" name="meta_title" id="meta_title" class="admin-input" value="<?= e($editPage['meta_title']) ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="meta_description" class="admin-label">Meta Description (150-160 characters optimal)</label>
                            <textarea name="meta_description" id="meta_description" class="admin-textarea" rows="3" required><?= e($editPage['meta_description']) ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="meta_keywords" class="admin-label">Meta Keywords (Comma-separated)</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" class="admin-input" value="<?= e($editPage['meta_keywords']) ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="canonical_url" class="admin-label">Canonical URL (Relative or Absolute)</label>
                            <input type="text" name="canonical_url" id="canonical_url" class="admin-input" value="<?= e($editPage['canonical_url']) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary">
                            Save SEO Settings
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- SEO Pages Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Public Page SEO Metadata</h3>
            </div>
            <div class="admin-card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Page Identifier</th>
                                <th>Meta Title</th>
                                <th>Meta Description Snippet</th>
                                <th>Canonical</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seoPages as $sp): ?>
                                <tr>
                                    <td>
                                        <code style="color: var(--admin-primary); font-weight: 700; font-size: 0.9rem;">
                                            <?= e($sp['page_identifier']) ?>
                                        </code>
                                    </td>
                                    <td style="font-weight: 600; color: #fff; max-width: 260px;">
                                        <?= e($sp['meta_title']) ?>
                                    </td>
                                    <td style="color: var(--admin-text-muted); font-size: 0.825rem; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($sp['meta_description']) ?>
                                    </td>
                                    <td>
                                        <code style="font-size: 0.775rem; color: var(--admin-text-dim);">
                                            <?= e($sp['canonical_url']) ?>
                                        </code>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="<?= url('admin/seo.php?edit=' . $sp['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                            Edit SEO
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
