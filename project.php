<?php
/**
 * Individual Project Details Page
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/includes/functions.php';

$db = get_db();
$slug = trim($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);

$project = null;

if (!empty($slug)) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE slug = :slug LIMIT 1");
    $stmt->execute(['slug' => $slug]);
    $project = $stmt->fetch();
} elseif ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $project = $stmt->fetch();
}

// Redirect to 404 if project doesn't exist
if (!$project) {
    header("HTTP/1.0 404 Not Found");
    include __DIR__ . '/404.php';
    exit;
}

// Fetch project screenshots
$screenshots = [];
try {
    $imgStmt = $db->prepare("SELECT * FROM project_images WHERE project_id = :pid ORDER BY is_featured DESC, display_order ASC, id ASC");
    $imgStmt->execute(['pid' => $project['id']]);
    $screenshots = $imgStmt->fetchAll();
} catch (Exception $e) {}

// Fetch adjacent projects for next/prev navigation
$prevProject = null;
$nextProject = null;
try {
    $prevStmt = $db->prepare("SELECT id, title, slug FROM projects WHERE id < :id ORDER BY id DESC LIMIT 1");
    $prevStmt->execute(['id' => $project['id']]);
    $prevProject = $prevStmt->fetch();

    $nextStmt = $db->prepare("SELECT id, title, slug FROM projects WHERE id > :id ORDER BY id ASC LIMIT 1");
    $nextStmt->execute(['id' => $project['id']]);
    $nextProject = $nextStmt->fetch();
} catch (Exception $e) {}

$profile = get_profile();
$displayedName = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Portfolio');

// Setup custom SEO
$pageIdentifier = 'project';
$customSeo = [
    'title' => !empty($project['seo_title']) ? $project['seo_title'] : $project['title'] . ' | ' . $displayedName . ' Portfolio',
    'description' => !empty($project['seo_description']) ? $project['seo_description'] : substr(strip_tags($project['summary']), 0, 160),
    'canonical' => url('project.php?slug=' . urlencode($project['slug'])),
    'schema' => [
        '@type' => 'SoftwareSourceCode',
        'name' => $project['title'],
        'description' => $project['summary'],
        'programmingLanguage' => $project['technologies'],
        'codeRepository' => $project['github_url'] ?? '',
        'author' => ['@id' => url('#person')]
    ]
];

if (!empty($screenshots)) {
    $customSeo['og_image'] = upload_url($screenshots[0]['image_path'], 'projects');
}

require_once __DIR__ . '/includes/header.php';
$techList = array_map('trim', explode(',', $project['technologies']));
?>

<!-- Project Detail Header -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <a href="<?= url('projects.php') ?>">Projects</a>
            <span>/</span>
            <span><?= e($project['title']) ?></span>
        </nav>
        
        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem;">
            <span class="project-category-badge" style="position: static;"><?= e($project['category']) ?></span>
            <span style="font-size: 0.825rem; font-weight: 600; color: var(--accent-emerald); font-family: var(--font-mono); background: rgba(16,185,129,0.1); padding: 0.25rem 0.65rem; border-radius: var(--radius-full);">
                ● <?= e($project['status']) ?>
            </span>
        </div>

        <h1 style="margin-bottom: 1rem;"><?= e($project['title']) ?></h1>
        <p class="section-description" style="max-width: 800px;">
            <?= e($project['summary']) ?>
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <!-- Project Metadata Strip -->
        <div class="project-meta-strip">
            <div class="meta-item">
                <span class="meta-label">My Role</span>
                <span class="meta-value"><?= e($project['role']) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Category</span>
                <span class="meta-value"><?= e($project['category']) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="meta-value"><?= e($project['status']) ?></span>
            </div>
            <div class="meta-item" style="margin-left: auto; display: flex; flex-direction: row; gap: 0.75rem; align-items: center;">
                <?php if (!empty($project['github_url'])): ?>
                    <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                        GitHub Repository
                    </a>
                <?php endif; ?>
                <?php if (!empty($project['live_url'])): ?>
                    <a href="<?= e($project['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Live Demonstration
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Technologies Used -->
        <div style="margin: 2rem 0;">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem;">Technology Stack</h3>
            <div class="project-tech-stack">
                <?php foreach ($techList as $tech): ?>
                    <span class="tech-badge" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;"><?= e($tech) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Screenshots / Media Gallery -->
        <div style="margin: 3rem 0;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Screenshots &amp; Visuals</h3>
            <?php if (!empty($screenshots)): ?>
                <div class="gallery-grid">
                    <?php foreach ($screenshots as $img): ?>
                        <div class="gallery-item">
                            <a href="<?= e(upload_url($img['image_path'], 'projects')) ?>" target="_blank" title="<?= e($img['caption'] ?? $project['title']) ?>">
                                <img src="<?= e(upload_url($img['image_path'], 'projects')) ?>" alt="<?= e($img['caption'] ?? $project['title']) ?>" loading="lazy">
                            </a>
                            <?php if (!empty($img['caption'])): ?>
                                <div style="padding: 0.75rem; font-size: 0.85rem; color: var(--text-muted); background-color: var(--bg-card); border-top: 1px solid var(--border-color);">
                                    <?= e($img['caption']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border-color); max-width: 800px;">
                    <img src="<?= asset('images/project-placeholder.svg') ?>" alt="<?= e($project['title']) ?> illustration" style="width: 100%; height: auto;">
                    <div style="padding: 1rem; background-color: var(--bg-card); font-size: 0.85rem; color: var(--text-muted); text-align: center;">
                        📸 Project screenshots can be uploaded and managed directly through the admin panel.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Detailed Description -->
        <div style="margin: 3.5rem 0; max-width: 860px;">
            <h3 style="font-size: 1.4rem; margin-bottom: 1.25rem; color: var(--text-primary);">Architecture &amp; System Overview</h3>
            <div style="color: var(--text-secondary); line-height: 1.8; font-size: 1.05rem;">
                <?= nl2br(e($project['description'])) ?>
            </div>
        </div>

        <!-- Navigation Between Projects -->
        <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <?php if ($prevProject): ?>
                    <a href="<?= url('project.php?slug=' . urlencode($prevProject['slug'])) ?>" class="btn btn-secondary">
                        &larr; <?= e($prevProject['title']) ?>
                    </a>
                <?php else: ?>
                    <a href="<?= url('projects.php') ?>" class="btn btn-secondary">&larr; Back to All Projects</a>
                <?php endif; ?>
            </div>

            <div>
                <?php if ($nextProject): ?>
                    <a href="<?= url('project.php?slug=' . urlencode($nextProject['slug'])) ?>" class="btn btn-secondary">
                        <?= e($nextProject['title']) ?> &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
