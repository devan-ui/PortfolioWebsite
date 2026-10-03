<?php
/**
 * Achievements & Highlights Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'achievements';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$achievementsList = [];
try {
    $stmt = $db->query("SELECT * FROM achievements ORDER BY display_order ASC, achievement_date DESC");
    $achievementsList = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Achievements</span>
        </nav>
        <span class="section-subtitle">Milestones &amp; Honors</span>
        <h1>Achievements &amp; Highlights</h1>
        <p class="section-description">
            Key academic milestones, programming recognitions, and competitive project completions.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <?php if (!empty($achievementsList)): ?>
            <div class="grid-2" style="gap: 2rem;">
                <?php foreach ($achievementsList as $ach): ?>
                    <div class="service-card" style="position: relative;">
                        <?php if (!empty($ach['highlight_badge'])): ?>
                            <div style="position: absolute; top: 1.5rem; right: 1.5rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: rgba(16, 185, 129, 0.12); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.25rem 0.65rem; border-radius: var(--radius-full);">
                                    <?= e($ach['highlight_badge']) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="service-icon-box" style="background: rgba(245, 158, 11, 0.12); color: var(--accent-amber); border-color: rgba(245, 158, 11, 0.25);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.45 1-1 1H7v4h10v-4h-2c-.55 0-1-.45-1-1v-2.34c0-.34.13-.67.36-.91l1.58-1.58A6 6 0 0 0 18 9V4H6v5c0 1.6.63 3.12 1.76 4.24l1.58 1.58c.23.24.36.57.36.91z"></path></svg>
                        </div>

                        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem;">
                            <?= e($ach['category']) ?> • <?= e($ach['achievement_date']) ?>
                        </div>

                        <h2 style="font-size: 1.35rem; margin-bottom: 0.75rem; padding-right: 3rem;">
                            <?= e($ach['title']) ?>
                        </h2>

                        <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.975rem;">
                            <?= nl2br(e($ach['description'])) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 1rem; background-color: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); max-width: 680px; margin: 0 auto;">
                <h3>Milestones &amp; Academic Honors</h3>
                <p style="color: var(--text-secondary); margin: 1rem 0 2rem 0;">
                    Additional competitive programming and project milestones can be entered through the admin panel.
                </p>
                <a href="<?= url('projects.php') ?>" class="btn btn-primary">View Practical Projects</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
