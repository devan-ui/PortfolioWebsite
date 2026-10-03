<?php
/**
 * Dynamic XML Sitemap Generator
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$db = get_db();
$projects = [];
try {
    $stmt = $db->query("SELECT slug, updated_at FROM projects ORDER BY id ASC");
    $projects = $stmt->fetchAll();
} catch (Exception $e) {}

$staticPages = [
    '' => ['priority' => '1.0', 'freq' => 'weekly'],
    'about.php' => ['priority' => '0.8', 'freq' => 'monthly'],
    'skills.php' => ['priority' => '0.8', 'freq' => 'monthly'],
    'projects.php' => ['priority' => '0.9', 'freq' => 'weekly'],
    'education.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    'experience.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    'services.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    'certifications.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    'achievements.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    'contact.php' => ['priority' => '0.8', 'freq' => 'monthly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page => $meta): ?>
    <url>
        <loc><?= e(url($page)) ?></loc>
        <changefreq><?= $meta['freq'] ?></changefreq>
        <priority><?= $meta['priority'] ?></priority>
    </url>
<?php endforeach; ?>

<?php foreach ($projects as $proj): ?>
    <url>
        <loc><?= e(url('project.php?slug=' . urlencode($proj['slug']))) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($proj['updated_at'] ?? 'now')) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.85</priority>
    </url>
<?php endforeach; ?>
</urlset>
