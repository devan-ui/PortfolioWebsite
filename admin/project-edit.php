<?php
/**
 * Project Add / Edit Form
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$db = get_db();
$id = (int)($_GET['id'] ?? 0);
$isEditing = ($id > 0);

$project = [
    'title' => '',
    'slug' => '',
    'category' => 'Web Application',
    'role' => 'Full Stack Developer',
    'status' => 'In Development',
    'summary' => '',
    'description' => '',
    'technologies' => '',
    'github_url' => '',
    'live_url' => '',
    'featured' => 0,
    'display_order' => 0,
    'seo_title' => '',
    'seo_description' => ''
];

if ($isEditing) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        set_flash('error', 'Project not found.');
        header('Location: ' . url('admin/projects.php'));
        exit;
    }
    $project = array_merge($project, $existing);
}

$adminPageTitle = $isEditing ? 'Edit Project: ' . $project['title'] : 'Add New Project';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $category = trim($_POST['category'] ?? 'Web Application');
        $role = trim($_POST['role'] ?? 'Full Stack Developer');
        $status = trim($_POST['status'] ?? 'In Development');
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $technologies = trim($_POST['technologies'] ?? '');
        $githubUrl = trim($_POST['github_url'] ?? '');
        $liveUrl = trim($_POST['live_url'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $seoTitle = trim($_POST['seo_title'] ?? '');
        $seoDesc = trim($_POST['seo_description'] ?? '');

        // Retain user input in $project array
        $project['title'] = $title;
        $project['slug'] = $slug;
        $project['category'] = $category;
        $project['role'] = $role;
        $project['status'] = $status;
        $project['summary'] = $summary;
        $project['description'] = $description;
        $project['technologies'] = $technologies;
        $project['github_url'] = $githubUrl;
        $project['live_url'] = $liveUrl;
        $project['featured'] = $featured;
        $project['display_order'] = $displayOrder;
        $project['seo_title'] = $seoTitle;
        $project['seo_description'] = $seoDesc;

        if (empty($title)) {
            $errors[] = 'Project title is required.';
        }

        if (empty($slug)) {
            $slug = slugify($title);
        } else {
            $slug = slugify($slug);
        }

        // Check unique slug
        $slugCheck = $db->prepare("SELECT id FROM projects WHERE slug = :slug AND id != :id LIMIT 1");
        $slugCheck->execute(['slug' => $slug, 'id' => $id]);
        if ($slugCheck->fetch()) {
            $errors[] = 'A project with this slug already exists. Please choose a unique slug.';
        }

        if (empty($summary)) {
            $errors[] = 'Short summary is required.';
        }
        if (empty($description)) {
            $errors[] = 'Detailed description is required.';
        }
        if (empty($technologies)) {
            $errors[] = 'Please specify at least one technology.';
        }

        if (empty($errors)) {
            try {
                if ($isEditing) {
                    $stmt = $db->prepare("UPDATE projects SET 
                        title = :title,
                        slug = :slug,
                        category = :category,
                        role = :role,
                        status = :status,
                        summary = :summary,
                        description = :description,
                        technologies = :technologies,
                        github_url = :github_url,
                        live_url = :live_url,
                        featured = :featured,
                        display_order = :display_order,
                        seo_title = :seo_title,
                        seo_description = :seo_description
                        WHERE id = :id");

                    $stmt->execute([
                        'title' => $title,
                        'slug' => $slug,
                        'category' => $category,
                        'role' => $role,
                        'status' => $status,
                        'summary' => $summary,
                        'description' => $description,
                        'technologies' => $technologies,
                        'github_url' => $githubUrl,
                        'live_url' => $liveUrl,
                        'featured' => $featured,
                        'display_order' => $displayOrder,
                        'seo_title' => $seoTitle,
                        'seo_description' => $seoDesc,
                        'id' => $id
                    ]);

                    set_flash('success', 'Project updated successfully.');
                    header('Location: ' . url('admin/projects.php'));
                    exit;
                } else {
                    $stmt = $db->prepare("INSERT INTO projects (
                        title, slug, category, role, status, summary, description,
                        technologies, github_url, live_url, featured, display_order,
                        seo_title, seo_description
                    ) VALUES (
                        :title, :slug, :category, :role, :status, :summary, :description,
                        :technologies, :github_url, :live_url, :featured, :display_order,
                        :seo_title, :seo_description
                    )");

                    $stmt->execute([
                        'title' => $title,
                        'slug' => $slug,
                        'category' => $category,
                        'role' => $role,
                        'status' => $status,
                        'summary' => $summary,
                        'description' => $description,
                        'technologies' => $technologies,
                        'github_url' => $githubUrl,
                        'live_url' => $liveUrl,
                        'featured' => $featured,
                        'display_order' => $displayOrder,
                        'seo_title' => $seoTitle,
                        'seo_description' => $seoDesc
                    ]);

                    $newId = (int)$db->lastInsertId();

                    // Optional image upload on creation
                    if (!empty($_FILES['screenshot']['name'])) {
                        $upRes = upload_file($_FILES['screenshot'], 'projects');
                        if ($upRes['success']) {
                            $db->prepare("INSERT INTO project_images (project_id, image_path, caption, is_featured, display_order) VALUES (:pid, :path, :cap, 1, 0)")
                               ->execute([
                                   'pid' => $newId,
                                   'path' => $upRes['relative_path'],
                                   'cap' => $title . ' Preview'
                               ]);
                        }
                    }

                    set_flash('success', 'Project created successfully. You can now manage screenshots.');
                    header('Location: ' . url('admin/project-images.php?project_id=' . $newId));
                    exit;
                }
            } catch (PDOException $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <a href="<?= url('admin/projects.php') ?>" style="color: var(--admin-text-muted); font-size: 0.85rem; text-decoration: none;">&larr; Back to Projects</a>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0.5rem 0 0 0;"><?= $isEditing ? 'Edit Project' : 'Create New Project' ?></h2>
            </div>
            <?php if ($isEditing): ?>
                <a href="<?= url('admin/project-images.php?project_id=' . $project['id']) ?>" class="admin-btn admin-btn-secondary">
                    📸 Manage Screenshots
                </a>
            <?php endif; ?>
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

        <div class="admin-card">
            <div class="admin-card-body">
                <form action="<?= url('admin/project-edit.php' . ($isEditing ? '?id=' . $id : '')) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="title-input" class="admin-label">Project Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" id="title-input" class="admin-input" value="<?= e($project['title']) ?>" placeholder="e.g. MentorHub" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="slug-input" class="admin-label">URL Slug <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="slug" id="slug-input" class="admin-input" value="<?= e($project['slug']) ?>" placeholder="e.g. mentorhub" required>
                            <span class="admin-help-text">Auto-generated from title</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="category" class="admin-label">Category</label>
                            <input type="text" name="category" id="category" class="admin-input" value="<?= e($project['category']) ?>" placeholder="e.g. Community Platform / Web App" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="role" class="admin-label">Developer Role</label>
                            <input type="text" name="role" id="role" class="admin-input" value="<?= e($project['role']) ?>" placeholder="e.g. Full Stack Developer" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="status" class="admin-label">Status</label>
                            <select name="status" id="status" class="admin-select">
                                <option value="In Development" <?= $project['status'] === 'In Development' ? 'selected' : '' ?>>In Development</option>
                                <option value="Active Project" <?= $project['status'] === 'Active Project' ? 'selected' : '' ?>>Active Project</option>
                                <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Planning" <?= $project['status'] === 'Planning' ? 'selected' : '' ?>>Planning</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="summary" class="admin-label">Short Summary (1-2 sentences) <span style="color:#ef4444;">*</span></label>
                        <textarea name="summary" id="summary" class="admin-textarea" rows="2" style="min-height:70px;" placeholder="Brief synopsis for cards and previews..." required><?= e($project['summary']) ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label for="description" class="admin-label">Full Architecture &amp; System Description <span style="color:#ef4444;">*</span></label>
                        <textarea name="description" id="description" class="admin-textarea" rows="6" placeholder="Provide factual details on roles, architecture, flows, and capabilities..." required><?= e($project['description']) ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label for="technologies" class="admin-label">Technologies (Comma-separated) <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="technologies" id="technologies" class="admin-input" value="<?= e($project['technologies']) ?>" placeholder="e.g. FastAPI, MongoDB, React, JavaScript, CSS" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="github_url" class="admin-label">GitHub Repository URL</label>
                            <input type="url" name="github_url" id="github_url" class="admin-input" value="<?= e($project['github_url']) ?>" placeholder="https://github.com/...">
                        </div>
                        <div class="admin-form-group">
                            <label for="live_url" class="admin-label">Live Demo / Preview URL</label>
                            <input type="url" name="live_url" id="live_url" class="admin-input" value="<?= e($project['live_url']) ?>" placeholder="https://...">
                        </div>
                    </div>

                    <?php if (!$isEditing): ?>
                        <div class="admin-form-group">
                            <label class="admin-label">Primary Screenshot / Banner Image</label>
                            <input type="file" name="screenshot" class="admin-input" accept="image/*">
                            <span class="admin-help-text">Upload project thumbnail (JPG, PNG, WebP)</span>
                        </div>
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: center; background: rgba(255,255,255,0.02); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.75rem; color: #fff; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="featured" value="1" <?= $project['featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Featured on Homepage</span>
                            </label>
                            <span class="admin-help-text">Display in the featured project grid on the homepage</span>
                        </div>
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)$project['display_order'] ?>">
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #fff; margin: 2rem 0 1rem 0; border-top: 1px solid var(--admin-border); padding-top: 1.5rem;">Custom SEO &amp; Social Meta (Optional)</h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="seo_title" class="admin-label">Custom SEO Title</label>
                            <input type="text" name="seo_title" id="seo_title" class="admin-input" value="<?= e($project['seo_title']) ?>" placeholder="Leave blank to use default project title">
                        </div>
                        <div class="admin-form-group">
                            <label for="seo_description" class="admin-label">Custom SEO Description</label>
                            <input type="text" name="seo_description" id="seo_description" class="admin-input" value="<?= e($project['seo_description']) ?>" placeholder="Leave blank to use summary">
                        </div>
                    </div>

                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.85rem 2rem;">
                            <?= $isEditing ? 'Save Changes' : 'Create Project' ?>
                        </button>
                        <a href="<?= url('admin/projects.php') ?>" class="admin-btn admin-btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
