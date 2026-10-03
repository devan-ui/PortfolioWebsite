<?php
/**
 * Website Configuration & Module Settings
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Website Settings';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

// Handle Saving Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $settingsToSave = [
            'site_name'             => trim($_POST['site_name'] ?? 'Devanandan CS | Portfolio'),
            'hero_greeting'         => trim($_POST['hero_greeting'] ?? 'Hello, World! I am'),
            'footer_tagline'        => trim($_POST['footer_tagline'] ?? ''),
            'footer_copyright'      => trim($_POST['footer_copyright'] ?? ''),
            'enable_services'       => isset($_POST['enable_services']) ? '1' : '0',
            'enable_experience'     => isset($_POST['enable_experience']) ? '1' : '0',
            'enable_certifications' => isset($_POST['enable_certifications']) ? '1' : '0',
            'enable_achievements'   => isset($_POST['enable_achievements']) ? '1' : '0',
        ];

        try {
            $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_value = :val");
            foreach ($settingsToSave as $k => $v) {
                $stmt->execute(['key' => $k, 'val' => $v]);
            }
            $GLOBALS['_cached_settings'] = null; // Clear cache
            set_flash('success', 'Website settings saved successfully.');
            header('Location: ' . url('admin/settings.php'));
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to save settings: ' . $e->getMessage();
        }
    }
}
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Website Settings</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Configure global branding, text content, and module visibility across the public site.
                </p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="admin-card-body">
                <form action="<?= url('admin/settings.php') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Branding &amp; Global Text</h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="site_name" class="admin-label">Website Name</label>
                            <input type="text" name="site_name" id="site_name" class="admin-input" value="<?= e(get_setting('site_name', 'Devanandan CS | Portfolio')) ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="hero_greeting" class="admin-label">Hero Greeting Subtitle</label>
                            <input type="text" name="hero_greeting" id="hero_greeting" class="admin-input" value="<?= e(get_setting('hero_greeting', 'Hello, World! I am')) ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-form-group">
                            <label for="footer_tagline" class="admin-label">Footer Tagline</label>
                            <input type="text" name="footer_tagline" id="footer_tagline" class="admin-input" value="<?= e(get_setting('footer_tagline', 'Crafting high-quality web applications with PHP, MySQL, FastAPI & modern frontend.')) ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="footer_copyright" class="admin-label">Footer Copyright Notice</label>
                            <input type="text" name="footer_copyright" id="footer_copyright" class="admin-input" value="<?= e(get_setting('footer_copyright', '© 2026 Devanandan CS. All rights reserved.')) ?>">
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #fff; margin: 2rem 0 1rem 0; border-top: 1px solid var(--admin-border); padding-top: 1.5rem;">Section &amp; Module Visibility</h3>
                    <p style="color: var(--admin-text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">
                        Toggle whether these pages and navigation items appear on the public website.
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; background: rgba(255, 255, 255, 0.02); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--admin-border);">
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.75rem; color: #fff; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="enable_services" value="1" <?= get_setting('enable_services', '1') === '1' ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Enable Services Module &amp; Page</span>
                            </label>
                            <div class="admin-help-text">Shows "Services" link in navbar &amp; footer</div>
                        </div>

                        <div>
                            <label style="display: flex; align-items: center; gap: 0.75rem; color: #fff; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="enable_experience" value="1" <?= get_setting('enable_experience', '1') === '1' ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Enable Experience &amp; Internships Page</span>
                            </label>
                            <div class="admin-help-text">Shows "Experience" link in navbar &amp; footer</div>
                        </div>

                        <div>
                            <label style="display: flex; align-items: center; gap: 0.75rem; color: #fff; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="enable_certifications" value="1" <?= get_setting('enable_certifications', '1') === '1' ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Enable Certifications Page</span>
                            </label>
                            <div class="admin-help-text">Shows "Certifications" link in navbar &amp; footer</div>
                        </div>

                        <div>
                            <label style="display: flex; align-items: center; gap: 0.75rem; color: #fff; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="enable_achievements" value="1" <?= get_setting('enable_achievements', '1') === '1' ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Enable Achievements Page</span>
                            </label>
                            <div class="admin-help-text">Shows "Achievements" link in navbar &amp; footer</div>
                        </div>
                    </div>

                    <button type="submit" class="admin-btn admin-btn-primary" style="margin-top: 2rem; padding: 0.85rem 2rem;">
                        Save Configuration
                    </button>
                </form>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
