<?php
/**
 * Services Offered Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'services';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$servicesList = [];
try {
    $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
    $servicesList = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Services</span>
        </nav>
        <span class="section-subtitle">What I Can Build For You</span>
        <h1>Technical Services &amp; Capabilities</h1>
        <p class="section-description">
            Practical development services tailored for modern web applications, scalable database architectures, and responsive user experiences.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="grid-2" style="gap: 2.5rem;">
            <?php foreach ($servicesList as $service): ?>
                <?php
                    $deliverables = explode("\n", $service['deliverables'] ?? '');
                ?>
                <div class="service-card">
                    <div class="service-icon-box">
                        <?php if ($service['icon'] === 'database'): ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                        <?php elseif ($service['icon'] === 'cpu'): ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                        <?php elseif ($service['icon'] === 'layout'): ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                        <?php else: ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <?php endif; ?>
                    </div>

                    <h2 class="service-title" style="font-size: 1.4rem;"><?= e($service['title']) ?></h2>
                    <p class="service-summary" style="font-weight: 500; color: var(--text-primary);"><?= e($service['short_description']) ?></p>
                    <p style="font-size: 0.925rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
                        <?= nl2br(e($service['full_description'])) ?>
                    </p>

                    <?php if (!empty($deliverables)): ?>
                        <div style="margin-top: auto; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                            <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Key Deliverables</span>
                            <ul class="service-deliverables" style="border-top: none; padding-top: 0;">
                                <?php foreach ($deliverables as $deliv): ?>
                                    <?php if (trim($deliv)): ?>
                                        <li><?= e(trim($deliv)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 4rem; text-align: center; background-color: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 3rem 1.5rem;">
            <h3 style="margin-bottom: 1rem;">Need a Tailored Web Development Solution?</h3>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto 2rem auto;">
                Whether you need a custom PHP/MySQL portal, a high-speed FastAPI backend, or a responsive modern frontend, I am available to help bring your project to reality.
            </p>
            <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg">Discuss Your Project &rarr;</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
