<?php
/**
 * Shared Responsive Navigation Bar
 * Devanandan CS Portfolio Website
 */

$profile = get_profile();
$displayedName = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Portfolio');
$initial = mb_strtoupper(mb_substr($displayedName, 0, 1));

$showServices = get_setting('enable_services', '1') === '1';
$showExperience = get_setting('enable_experience', '1') === '1';
$showCertifications = get_setting('enable_certifications', '1') === '1';
$showAchievements = get_setting('enable_achievements', '1') === '1';
$hasDropdownItems = ($showExperience || $showServices || $showCertifications || $showAchievements);
?>
<header class="site-header" id="site-header">
    <div class="container">
        <nav class="navbar" aria-label="Main Portfolio Navigation">
            <a href="<?= url('') ?>" class="nav-brand" aria-label="<?= e($displayedName) ?> Home">
                <span class="brand-monogram"><?= e($displayedName) ?><span class="brand-accent-dot">.</span></span>
            </a>

            <!-- Mobile Navigation Backdrop Overlay -->
            <div class="nav-backdrop" id="nav-backdrop" aria-hidden="true"></div>

            <!-- Responsive Navigation Menu Wrapper -->
            <div class="nav-menu-container" id="nav-menu-container">
                <ul class="nav-links" id="nav-links">
                    <li><a href="<?= url('') ?>" class="nav-link <?= is_active_page('index') ? 'active' : '' ?>">Home</a></li>
                    <li><a href="<?= url('about.php') ?>" class="nav-link <?= is_active_page('about') ? 'active' : '' ?>">About</a></li>
                    <li><a href="<?= url('projects.php') ?>" class="nav-link <?= is_active_page('projects') || is_active_page('project') ? 'active' : '' ?>">Projects</a></li>
                    <li><a href="<?= url('skills.php') ?>" class="nav-link <?= is_active_page('skills') ? 'active' : '' ?>">Skills</a></li>
                    <li><a href="<?= url('education.php') ?>" class="nav-link <?= is_active_page('education') ? 'active' : '' ?>">Education</a></li>
                    <?php if ($hasDropdownItems): ?>
                        <li class="nav-dropdown-item" id="nav-dropdown-item">
                            <button type="button" class="nav-link nav-dropdown-btn" id="nav-dropdown-btn" aria-haspopup="true" aria-expanded="false" title="More sections">
                                <span class="nav-dropdown-label">
                                    <span>More</span>
                                    <span class="nav-dots-pips" aria-hidden="true" title="More options">
                                        <span class="dot-pip"></span>
                                        <span class="dot-pip"></span>
                                        <span class="dot-pip"></span>
                                    </span>
                                </span>
                                <svg class="dropdown-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <ul class="nav-dropdown-menu" id="nav-dropdown-menu" role="menu">
                                <?php if ($showExperience): ?>
                                    <li role="none">
                                        <a role="menuitem" href="<?= url('experience.php') ?>" class="nav-dropdown-link <?= is_active_page('experience') ? 'active' : '' ?>">
                                            <svg class="nav-dropdown-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                            <span>Experience</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($showServices): ?>
                                    <li role="none">
                                        <a role="menuitem" href="<?= url('services.php') ?>" class="nav-dropdown-link <?= is_active_page('services') ? 'active' : '' ?>">
                                            <svg class="nav-dropdown-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                            <span>Services</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($showCertifications): ?>
                                    <li role="none">
                                        <a role="menuitem" href="<?= url('certifications.php') ?>" class="nav-dropdown-link <?= is_active_page('certifications') ? 'active' : '' ?>">
                                            <svg class="nav-dropdown-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15l-2 5l4 -2l4 2l-2 -5"></path><circle cx="12" cy="9" r="6"></circle></svg>
                                            <span>Certifications</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($showAchievements): ?>
                                    <li role="none">
                                        <a role="menuitem" href="<?= url('achievements.php') ?>" class="nav-dropdown-link <?= is_active_page('achievements') ? 'active' : '' ?>">
                                            <svg class="nav-dropdown-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                            <span>Achievements</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?= url('contact.php') ?>" class="nav-link <?= is_active_page('contact') ? 'active' : '' ?>">Contact</a></li>
                </ul>

                <!-- Mobile Menu CTA Button -->
                <div class="mobile-menu-footer">
                    <a href="<?= url('contact.php') ?>" class="mobile-cta-btn">
                        <span>Let's Talk</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>

            <div class="nav-actions">
                <a href="<?= url('contact.php') ?>" class="nav-cta-btn">
                    <span>Let's Talk</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>

                <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Toggle Light/Dark Theme" aria-label="Toggle Theme">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </button>

                <!-- Modern 3-bar transforming toggle (morphs to clean 'X') -->
                <button type="button" class="mobile-nav-toggle" id="mobile-toggle" aria-label="Toggle Navigation Menu" aria-expanded="false" aria-controls="nav-menu-container">
                    <span class="hamburger-box" aria-hidden="true">
                        <span class="hamburger-line line-1"></span>
                        <span class="hamburger-line line-2"></span>
                        <span class="hamburger-line line-3"></span>
                    </span>
                </button>
            </div>
        </nav>
    </div>
</header>
