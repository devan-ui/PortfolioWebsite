<?php
/**
 * Certifications & Credentials Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'certifications';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$certificationsList = [];
try {
    $stmt = $db->query("SELECT * FROM certifications ORDER BY display_order ASC, issue_date DESC");
    $certificationsList = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Certifications</span>
        </nav>
        <span class="section-subtitle">Verified Knowledge</span>
        <h1>Certifications &amp; Credentials</h1>
        <p class="section-description">
            Technical certifications, specialized coursework, and verified professional development milestones.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <?php if (!empty($certificationsList)): ?>
            <div class="grid-3">
                <?php foreach ($certificationsList as $cert): ?>
                    <div class="project-card" style="padding: 2rem;">
                        <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: rgba(59, 130, 246, 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        </div>

                        <h2 style="font-size: 1.2rem; margin-bottom: 0.5rem;"><?= e($cert['title']) ?></h2>
                        <div style="font-size: 0.95rem; font-weight: 600; color: var(--accent-primary); margin-bottom: 0.5rem;">
                            <?= e($cert['issuer']) ?>
                        </div>

                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                            Issued: <?= e($cert['issue_date']) ?>
                            <?php if (!empty($cert['expiration_date']) && strtolower($cert['expiration_date']) !== 'no expiration'): ?>
                                • Expires: <?= e($cert['expiration_date']) ?>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($cert['credential_id'])): ?>
                            <div style="margin-bottom: 1.5rem; font-family: var(--font-mono); font-size: 0.8rem; background-color: var(--bg-surface); padding: 0.35rem 0.65rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: inline-block;">
                                ID: <?= e($cert['credential_id']) ?>
                            </div>
                        <?php endif; ?>

                        <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <?php if (!empty($cert['credential_url'])): ?>
                                <a href="<?= e($cert['credential_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm" style="width: 100%;">
                                    Verify Credential &rarr;
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">Verified Credential</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 1rem; background-color: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); max-width: 680px; margin: 0 auto;">
                <h3>Certifications In Progress</h3>
                <p style="color: var(--text-secondary); margin: 1rem 0 2rem 0;">
                    Currently working on industry-recognized full-stack certifications. Credentials can be added and verified through the administration portal.
                </p>
                <a href="<?= url('skills.php') ?>" class="btn btn-primary">Review Current Skills Catalogue</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
