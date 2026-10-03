<?php
/**
 * Experience & Internships Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'experience';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$experienceList = [];
try {
    $stmt = $db->query("SELECT * FROM experience ORDER BY display_order ASC, start_date DESC");
    $experienceList = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Experience</span>
        </nav>
        <span class="section-subtitle">Professional Path</span>
        <h1>Experience &amp; Internships</h1>
        <p class="section-description">
            Practical development experience, project lab training, and real-world software engineering practice.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <?php if (!empty($experienceList)): ?>
            <div class="timeline">
                <?php foreach ($experienceList as $exp): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-card">
                            <div class="timeline-header">
                                <h2 class="timeline-title"><?= e($exp['title']) ?></h2>
                                <span class="timeline-period">
                                    <?= e($exp['start_date']) ?> – <?= $exp['is_current'] ? 'Present' : e($exp['end_date']) ?>
                                </span>
                            </div>

                            <div class="timeline-institution">
                                <?= e($exp['company']) ?> • <span style="color: var(--text-muted);"><?= e($exp['location']) ?></span>
                            </div>

                            <div class="timeline-description">
                                <?= nl2br(e($exp['description'])) ?>
                            </div>

                            <?php if (!empty($exp['responsibilities'])): ?>
                                <?php
                                    $bullets = explode(';', $exp['responsibilities']);
                                    if (count($bullets) === 1) {
                                        $bullets = explode("\n", $exp['responsibilities']);
                                    }
                                ?>
                                <ul class="timeline-highlights">
                                    <?php foreach ($bullets as $b): ?>
                                        <?php if (trim($b)): ?>
                                            <li><?= e(trim($b)) ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 1rem; background-color: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); max-width: 680px; margin: 0 auto;">
                <h3>Internships &amp; Experience In Progress</h3>
                <p style="color: var(--text-secondary); margin: 1rem 0 2rem 0;">
                    Currently focusing on MCA studies and end-to-end web application development. Open to web developer internships and entry-level positions.
                </p>
                <a href="<?= url('contact.php') ?>" class="btn btn-primary">Inquire About Availability</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
