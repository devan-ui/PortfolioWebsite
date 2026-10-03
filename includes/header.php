<?php
/**
 * Shared Public Header Component
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

// Default page identifier if not defined
$currentPage = $pageIdentifier ?? 'home';
$pageCustomSeo = $customSeo ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Dynamic SEO & Structured Data -->
    <?= render_seo_meta($currentPage, $pageCustomSeo) ?>
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    <?= render_flashes() ?>
    <main id="main-content">
