<?php
/**
 * Skills & Technologies Catalogue
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'skills';
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Load all skills dynamically from MySQL ordered by display order
$skills = [];
try {
    $stmt = $db->query("SELECT * FROM skills ORDER BY category ASC, display_order ASC, name ASC");
    $skills = $stmt->fetchAll();
} catch (Exception $e) {
    // Silence error
}

// Group skills by category
$categories = [
    'Programming' => [],
    'Frontend'    => [],
    'Backend'     => [],
    'Databases'   => [],
    'Tools'       => [],
    'AI/ML'       => []
];

foreach ($skills as $skill) {
    $cat = $skill['category'];
    if (isset($categories[$cat])) {
        $categories[$cat][] = $skill;
    } else {
        $categories[$cat] = [$skill];
    }
}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Skills</span>
        </nav>
        <span class="section-subtitle">Technical Competence</span>
        <h1>Skills &amp; Technologies Catalogue</h1>
        <p class="section-description">
            A comprehensive overview of programming languages, frameworks, databases, and development tools loaded dynamically from MySQL.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <!-- Category Filter Tabs -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center; margin-bottom: 3.5rem;">
            <button type="button" class="btn btn-secondary btn-sm active" data-filter="all">All Technologies (<?= count($skills) ?>)</button>
            <?php foreach ($categories as $catName => $catSkills): ?>
                <?php if (!empty($catSkills)): ?>
                    <button type="button" class="btn btn-secondary btn-sm" data-filter="<?= e($catName) ?>">
                        <?= e($catName) ?> (<?= count($catSkills) ?>)
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Categorized Grid -->
        <div class="grid-2" style="gap: 2rem;">
            <?php foreach ($categories as $catName => $catSkills): ?>
                <?php if (!empty($catSkills)): ?>
                    <div class="skill-category-card" data-category="<?= e($catName) ?>">
                        <div class="category-title-bar">
                            <div class="category-icon">
                                <?php if ($catName === 'Programming'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                                <?php elseif ($catName === 'Frontend'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                <?php elseif ($catName === 'Backend'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                <?php elseif ($catName === 'Databases'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                <?php elseif ($catName === 'Tools'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                                <?php else: ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>
                                <?php endif; ?>
                            </div>
                            <h2 class="category-name"><?= e($catName) ?></h2>
                            <span style="margin-left: auto; font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);"><?= count($catSkills) ?> items</span>
                        </div>

                        <div class="skills-list">
                            <?php foreach ($catSkills as $skill): ?>
                                <div class="skill-pill">
                                    <span><?= e($skill['name']) ?></span>
                                    <?= render_proficiency_badge($skill['proficiency']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Note regarding proficiency level -->
        <div style="margin-top: 3.5rem; padding: 1.5rem; border-radius: var(--radius-md); background-color: var(--bg-card); border: 1px solid var(--border-color); text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
            <p style="font-size: 0.95rem; margin-bottom: 0;">
                ℹ️ <strong>Proficiency Standard:</strong> Initial proficiency levels are calibrated as foundational / beginner as supplied, and are actively managed through the database administration system as practical experience expands.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
