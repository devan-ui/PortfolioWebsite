<?php
/**
 * Shared Admin Sidebar Navigation
 * Devanandan CS Portfolio Website
 */

$currentAdminScript = basename($_SERVER['PHP_SELF'] ?? '');
$db = get_db();

// Unread messages count
$unreadMessagesCount = 0;
try {
    $unreadMessagesCount = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
} catch (Exception $e) {}
?>
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-brand">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#3b82f6;color:#fff;font-weight:700;font-size:0.85rem;">DCS</span>
            <span>Portfolio CMS</span>
        </div>
        <button type="button" class="admin-sidebar-close" id="admin-sidebar-close" aria-label="Close Navigation Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <ul class="admin-sidebar-nav">
        <li class="admin-nav-item">
            <a href="<?= url('admin/index.php') ?>" class="<?= $currentAdminScript === 'index.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/profile.php') ?>" class="<?= $currentAdminScript === 'profile.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Profile &amp; Resume</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/projects.php') ?>" class="<?= in_array($currentAdminScript, ['projects.php', 'project-edit.php', 'project-images.php']) ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <span>Projects</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/skills.php') ?>" class="<?= $currentAdminScript === 'skills.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                <span>Skills &amp; Tech</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/education.php') ?>" class="<?= $currentAdminScript === 'education.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                <span>Education</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/experience.php') ?>" class="<?= $currentAdminScript === 'experience.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <span>Experience</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/certifications.php') ?>" class="<?= $currentAdminScript === 'certifications.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                <span>Certifications</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/achievements.php') ?>" class="<?= $currentAdminScript === 'achievements.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>
                <span>Achievements</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/services.php') ?>" class="<?= $currentAdminScript === 'services.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                <span>Services</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/social-links.php') ?>" class="<?= $currentAdminScript === 'social-links.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Social Links</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/messages.php') ?>" class="<?= $currentAdminScript === 'messages.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <span>Messages</span>
                <?php if ($unreadMessagesCount > 0): ?>
                    <span class="admin-nav-badge"><?= $unreadMessagesCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/settings.php') ?>" class="<?= $currentAdminScript === 'settings.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Site Settings</span>
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="<?= url('admin/seo.php') ?>" class="<?= $currentAdminScript === 'seo.php' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>SEO &amp; Metadata</span>
            </a>
        </li>
    </ul>

    <div class="admin-sidebar-footer">
        <a href="<?= url('admin/logout.php') ?>" class="admin-btn admin-btn-danger" style="width: 100%; box-sizing: border-box;" data-confirm="Are you sure you want to log out?">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            <span>Log Out</span>
        </a>
    </div>
</aside>
