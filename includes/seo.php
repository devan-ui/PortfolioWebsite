<?php
/**
 * SEO & Structured Data Generator
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/functions.php';

/**
 * Fetch SEO data for a page or construct dynamic defaults
 */
function get_page_seo(string $pageKey, array $custom = []): array {
    $profile = get_profile();
    $name = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Developer');
    $profTitle = !empty($profile['professional_title']) ? $profile['professional_title'] : 'Full Stack Developer';
    $loc = !empty($profile['location']) ? $profile['location'] : 'India';

    $defaultTitle = $name . ' - ' . $profTitle;
    $defaultDesc = 'Portfolio of ' . $name . ', ' . $profTitle . ' based in ' . $loc . '.';
    $defaultKeywords = $name . ', ' . $profTitle . ', web developer, PHP, MySQL, portfolio';

    $seo = [
        'title' => $defaultTitle,
        'description' => $defaultDesc,
        'keywords' => $defaultKeywords,
        'canonical' => url($pageKey === 'home' ? '' : $pageKey . '.php'),
        'og_type' => 'website',
        'og_image' => asset('images/og-preview.png'),
        'schema' => []
    ];

    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM seo_pages WHERE page_identifier = :key LIMIT 1");
        $stmt->execute(['key' => $pageKey]);
        $row = $stmt->fetch();
        if ($row) {
            if (!empty($row['meta_title'])) $seo['title'] = str_ireplace('Devanandan CS', $name, $row['meta_title']);
            if (!empty($row['meta_description'])) $seo['description'] = str_ireplace('Devanandan CS', $name, $row['meta_description']);
            if (!empty($row['meta_keywords'])) $seo['keywords'] = str_ireplace('Devanandan CS', $name, $row['meta_keywords']);
            if (!empty($row['canonical_url'])) {
                $seo['canonical'] = (strpos($row['canonical_url'], 'http') === 0) ? $row['canonical_url'] : url($row['canonical_url']);
            }
        }
    } catch (Exception $e) {
        // Fallback to defaults
    }

    // Override with custom page parameters if provided (e.g., project details page)
    if (!empty($custom['title'])) $seo['title'] = $custom['title'];
    if (!empty($custom['description'])) $seo['description'] = $custom['description'];
    if (!empty($custom['keywords'])) $seo['keywords'] = $custom['keywords'];
    if (!empty($custom['canonical'])) $seo['canonical'] = $custom['canonical'];
    if (!empty($custom['og_type'])) $seo['og_type'] = $custom['og_type'];
    if (!empty($custom['og_image'])) $seo['og_image'] = $custom['og_image'];
    if (!empty($custom['schema'])) $seo['schema'] = $custom['schema'];

    return $seo;
}

/**
 * Render complete SEO meta tags and Schema.org JSON-LD
 */
function render_seo_meta(string $pageKey, array $custom = []): string {
    $seo = get_page_seo($pageKey, $custom);
    $profile = get_profile();

    $title = e($seo['title']);
    $desc = e($seo['description']);
    $keywords = e($seo['keywords']);
    $canonical = e($seo['canonical']);
    $ogType = e($seo['og_type']);
    $ogImage = e($seo['og_image']);
    $siteName = e(get_setting('site_name', 'Devanandan CS | Portfolio'));
    $author = e($profile['full_name'] ?? 'Devanandan CS');

    $html = <<<HTML
    <!-- Primary Meta Tags -->
    <title>{$title}</title>
    <meta name="title" content="{$title}">
    <meta name="description" content="{$desc}">
    <meta name="keywords" content="{$keywords}">
    <meta name="author" content="{$author}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1e293b">
    <link rel="canonical" href="{$canonical}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{$ogType}">
    <meta property="og:url" content="{$canonical}">
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$desc}">
    <meta property="og:image" content="{$ogImage}">
    <meta property="og:site_name" content="{$siteName}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{$canonical}">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$desc}">
    <meta name="twitter:image" content="{$ogImage}">
HTML;

    // Structured Data JSON-LD
    $schemaData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                '@id' => url('#person'),
                'name' => $profile['full_name'] ?? 'Devanandan CS',
                'jobTitle' => $profile['professional_title'] ?? 'MCA Student, Full Stack Developer',
                'description' => $profile['tagline'] ?? '',
                'url' => url(''),
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Ernakulam',
                    'addressRegion' => 'Kerala',
                    'addressCountry' => 'IN'
                ],
                'sameAs' => array_filter([
                    $profile['github_url'] ?? '',
                    $profile['linkedin_url'] ?? ''
                ])
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('#website'),
                'url' => url(''),
                'name' => $siteName,
                'author' => ['@id' => url('#person')]
            ]
        ]
    ];

    if (!empty($custom['schema'])) {
        $schemaData['@graph'][] = $custom['schema'];
    }

    $jsonLd = json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $html .= "\n    <!-- Structured Data (Schema.org) -->\n    <script type=\"application/ld+json\">\n" . $jsonLd . "\n    </script>\n";

    return $html;
}
