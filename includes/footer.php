<?php
/**
 * Shared Public Footer Component
 * Devanandan CS Portfolio Website
 */

$profile = get_profile();
$displayedName = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Portfolio');
$footerTagline = get_setting('footer_tagline', 'Crafting high-quality web applications with PHP, MySQL, FastAPI & modern frontend.');
$rawCopyright = get_setting('footer_copyright', '© ' . date('Y') . ' ' . $displayedName . '. All rights reserved.');
$footerCopyright = str_ireplace('Devanandan CS', $displayedName, $rawCopyright);

// Retrieve active social links
$socialLinks = [];
try {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order ASC");
    $socialLinks = $stmt->fetchAll();
} catch (Exception $e) {
    // Silence error
}
?>
    </main>

    <footer class="site-footer" id="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col footer-col-brand">
                    <a href="<?= url('') ?>" class="nav-brand" aria-label="<?= e($displayedName) ?> Home">
                        <span class="brand-monogram"><?= e($displayedName) ?><span class="brand-accent-dot">.</span></span>
                    </a>
                    <p class="footer-brand-bio">
                        <?= e($profile['professional_title'] ?? 'MCA Student, Full Stack Developer') ?> based in <?= e($profile['location'] ?? 'Ernakulam, Kerala, India') ?>.
                    </p>
                    <p class="footer-brand-bio" style="margin-top: 0.5rem; font-size: 0.875rem;">
                        <?= e($footerTagline) ?>
                    </p>
                </div>

                <div class="footer-col">
                    <h3 class="footer-heading">Navigation</h3>
                    <ul class="footer-links">
                        <li><a href="<?= url('') ?>">Home</a></li>
                        <li><a href="<?= url('about.php') ?>">About Me</a></li>
                        <li><a href="<?= url('skills.php') ?>">Skills &amp; Tech</a></li>
                        <li><a href="<?= url('projects.php') ?>">Projects</a></li>
                        <li><a href="<?= url('education.php') ?>">Education</a></li>
                        <li><a href="<?= url('contact.php') ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3 class="footer-heading">Connect</h3>
                    <ul class="footer-links">
                        <li><span>📍 <?= e($profile['location'] ?? 'Ernakulam, Kerala, India') ?></span></li>
                        <li><a href="mailto:<?= e($profile['email'] ?? 'devanandancs@example.com') ?>">✉️ <?= e($profile['email'] ?? 'devanandancs@example.com') ?></a></li>
                        <li><span style="color: var(--accent-emerald);">● <?= e($profile['availability_status'] ?? 'Available for opportunities') ?></span></li>
                    </ul>

                    <?php if (!empty($socialLinks)): ?>
                        <div class="social-pills" style="margin-top: 1.25rem;">
                            <?php foreach ($socialLinks as $link): ?>
                                <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener noreferrer" class="social-pill" title="<?= e($link['platform']) ?>">
                                    <?php if (stripos($link['platform'], 'git') !== false): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                    <?php elseif (stripos($link['platform'], 'link') !== false): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                                    <?php else: ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p><?= e($footerCopyright) ?></p>
                <div class="footer-admin-link">
                    <a href="<?= url('admin/login.php') ?>" style="color: var(--text-muted); font-size: 0.8rem;">🔒 Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
