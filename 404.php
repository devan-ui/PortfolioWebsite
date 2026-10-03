<?php
/**
 * 404 Not Found Page
 * Devanandan CS Portfolio Website
 */

// Send genuine 404 Not Found header for SEO & Search Console compliance
http_response_code(404);

require_once __DIR__ . '/includes/functions.php';

$pageIdentifier = '404';
$profile = get_profile();
$displayedName = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Portfolio');
$customSeo = [
    'title' => 'Page Not Found (404) | ' . $displayedName,
    'description' => 'The page you requested could not be located on ' . $displayedName . ' portfolio website.'
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container" style="text-align: center; max-width: 650px;">
        <div style="font-family: var(--font-mono); font-size: 5rem; font-weight: 800; color: var(--accent-primary); line-height: 1; margin-bottom: 1rem;">
            404
        </div>
        <h1 style="font-size: 2rem; margin-bottom: 1rem;">Page Not Found</h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2.5rem;">
            The endpoint or resource you were looking for does not exist or has been relocated.
        </p>

        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?= url('') ?>" class="btn btn-primary btn-lg">Return to Home</a>
            <a href="<?= url('projects.php') ?>" class="btn btn-secondary btn-lg">View Projects</a>
            <a href="<?= url('contact.php') ?>" class="btn btn-outline btn-lg">Contact Me</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
