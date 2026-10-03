<?php
/**
 * Projects Listing Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'projects';
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Load all projects ordered by display_order
$projects = [];
$categories = [];
try {
    $stmt = $db->query("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
    $projects = $stmt->fetchAll();

    foreach ($projects as $p) {
        if (!empty($p['category']) && !in_array($p['category'], $categories)) {
            $categories[] = $p['category'];
        }
    }
} catch (Exception $e) {
    // Silence error
}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Projects</span>
        </nav>
        <span class="section-subtitle">Portfolio Showcase</span>
        <h1>Engineered Web Applications</h1>
        <p class="section-description">
            Explore practical software projects engineered with modern frameworks, reliable databases, and clean code standards.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <!-- Filter Tabs -->
        <?php if (!empty($categories)): ?>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center; margin-bottom: 3.5rem;">
                <button type="button" class="btn btn-secondary btn-sm active" data-filter="all">All Projects (<?= count($projects) ?>)</button>
                <?php foreach ($categories as $cat): ?>
                    <button type="button" class="btn btn-secondary btn-sm" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Projects Grid -->
        <div class="grid-2">
            <?php foreach ($projects as $project): ?>
                <?php
                    $techList = array_map('trim', explode(',', $project['technologies']));
                    // Check if project has images
                    $imgStmt = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :pid ORDER BY is_featured DESC, display_order ASC LIMIT 1");
                    $imgStmt->execute(['pid' => $project['id']]);
                    $primaryImg = $imgStmt->fetchColumn();
                    $thumbnailSrc = !empty($primaryImg) ? upload_url($primaryImg, 'projects') : asset('images/project-placeholder.svg');
                ?>
                <article class="project-card" data-category="<?= e($project['category']) ?>">
                    <div class="project-thumbnail-wrapper">
                        <img src="<?= e($thumbnailSrc) ?>" alt="<?= e($project['title']) ?>" class="project-thumbnail" loading="lazy">
                        <span class="project-category-badge"><?= e($project['category']) ?></span>
                    </div>
                    <div class="project-body">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                            <h2 class="project-title" style="font-size: 1.45rem;">
                                <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>"><?= e($project['title']) ?></a>
                            </h2>
                            <span style="font-size: 0.775rem; color: var(--accent-emerald); font-weight: 600; font-family: var(--font-mono); background: rgba(16,185,129,0.1); padding: 0.2rem 0.5rem; border-radius: var(--radius-full);">
                                <?= e($project['status']) ?>
                            </span>
                        </div>

                        <p class="project-summary"><?= e($project['summary']) ?></p>
                        
                        <div class="project-tech-stack">
                            <?php foreach ($techList as $tech): ?>
                                <span class="tech-badge"><?= e($tech) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="project-footer">
                            <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>" class="btn btn-primary btn-sm">
                                View Case Study &rarr;
                            </a>
                            <div class="project-links">
                                <?php if (!empty($project['github_url'])): ?>
                                    <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="social-pill" title="GitHub Repository">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($project['live_url'])): ?>
                                    <a href="<?= e($project['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="social-pill" title="Live Preview">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
