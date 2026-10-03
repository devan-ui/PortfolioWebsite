<?php
/**
 * Academic Education History
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'education';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$educationList = [];
try {
    $stmt = $db->query("SELECT * FROM education ORDER BY display_order ASC, start_year DESC");
    $educationList = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Education</span>
        </nav>
        <span class="section-subtitle">Academic Credentials</span>
        <h1>Educational Journey</h1>
        <p class="section-description">
            A structured path in Computer Applications: BCA graduate currently pursuing Master of Computer Applications (MCA).
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="timeline">
            <?php foreach ($educationList as $edu): ?>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <h2 class="timeline-title"><?= e($edu['degree']) ?></h2>
                            <span class="timeline-period">
                                <?= e($edu['start_year']) ?> – <?= $edu['is_current'] ? 'Present (Pursuing)' : e($edu['end_year']) ?>
                            </span>
                        </div>

                        <div class="timeline-institution">
                            <?= e($edu['institution']) ?> • <span style="color: var(--text-muted);"><?= e($edu['location']) ?></span>
                        </div>

                        <?php if (!empty($edu['field_of_study'])): ?>
                            <div style="font-size: 0.9rem; color: var(--accent-primary); font-weight: 600; margin-bottom: 0.75rem;">
                                Major: <?= e($edu['field_of_study']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($edu['grade'])): ?>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="font-size: 0.8rem; background-color: rgba(59, 130, 246, 0.1); color: var(--accent-primary); padding: 0.2rem 0.55rem; border-radius: var(--radius-sm); font-weight: 600;">
                                    Status: <?= e($edu['grade']) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($edu['description'])): ?>
                            <div class="timeline-description">
                                <?= nl2br(e($edu['description'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 4rem;">
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">
                Looking for practical software projects and verified technical abilities?
            </p>
            <a href="<?= url('projects.php') ?>" class="btn btn-primary">Explore Featured Projects &rarr;</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
