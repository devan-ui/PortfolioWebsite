<?php
/**
 * Admin Dashboard
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Dashboard Overview';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();

// Counts for dashboard
$counts = [
    'projects'       => (int)$db->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'skills'         => (int)$db->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
    'certifications' => (int)$db->query("SELECT COUNT(*) FROM certifications")->fetchColumn(),
    'achievements'   => (int)$db->query("SELECT COUNT(*) FROM achievements")->fetchColumn(),
    'education'      => (int)$db->query("SELECT COUNT(*) FROM education")->fetchColumn(),
    'experience'     => (int)$db->query("SELECT COUNT(*) FROM experience")->fetchColumn(),
    'services'       => (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'messages'       => (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'unread_msg'     => (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn(),
];

// Fetch 5 most recent contact messages
$recentMessages = [];
try {
    $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
    $recentMessages = $stmt->fetchAll();
} catch (Exception $e) {}

// Fetch 5 most recent projects
$recentProjects = [];
try {
    $stmt = $db->query("SELECT id, title, category, status, featured FROM projects ORDER BY created_at DESC LIMIT 5");
    $recentProjects = $stmt->fetchAll();
} catch (Exception $e) {}

$profile = get_profile();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <!-- Welcome Banner -->
        <div class="admin-welcome-banner">
            <div>
                <h2 style="margin: 0 0 0.5rem 0; font-size: 1.5rem; color: #fff;">Hello, <?= e($profile['displayed_name'] ?? 'Devanandan') ?>!</h2>
                <p style="margin: 0; color: var(--admin-text-muted); font-size: 0.95rem;">
                    Welcome to your portfolio CMS. Manage projects, skills, education, and review visitor inquiries.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="<?= url('admin/project-edit.php') ?>" class="admin-btn admin-btn-primary">
                    + Add New Project
                </a>
                <a href="<?= url('admin/skills.php') ?>" class="admin-btn admin-btn-secondary">
                    + Add Skill
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-val"><?= $counts['projects'] ?></span>
                    <span class="stat-lbl">Total Projects</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-val"><?= $counts['skills'] ?></span>
                    <span class="stat-lbl">Skills Catalogued</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-val"><?= $counts['certifications'] ?></span>
                    <span class="stat-lbl">Certifications</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-val"><?= $counts['achievements'] ?></span>
                    <span class="stat-lbl">Achievements</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-rose">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-val"><?= $counts['unread_msg'] ?></span>
                    <span class="stat-lbl">Unread Messages (<?= $counts['messages'] ?> total)</span>
                </div>
            </div>
        </div>

        <!-- 2 Column Section: Recent Messages & Quick Projects Overview -->
        <div class="admin-layout-split" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem;">
            <!-- Recent Contact Messages -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Recent Contact Inquiries</h3>
                    <a href="<?= url('admin/messages.php') ?>" class="admin-btn admin-btn-secondary admin-btn-sm">View All Messages</a>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($recentMessages)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Sender</th>
                                        <th>Subject</th>
                                        <th>Received</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $msg): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #fff;"><?= e($msg['name']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($msg['email']) ?></div>
                                            </td>
                                            <td style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= e($msg['subject']) ?>
                                            </td>
                                            <td style="font-size: 0.825rem; color: var(--admin-text-dim);">
                                                <?= date('M d, H:i', strtotime($msg['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($msg['is_read']): ?>
                                                    <span style="font-size: 0.75rem; color: var(--admin-text-dim);">Read</span>
                                                <?php else: ?>
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #ef4444; background: rgba(239, 68, 68, 0.15); padding: 0.2rem 0.5rem; border-radius: 9999px;">New</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= url('admin/messages.php?id=' . $msg['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                    Read
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted);">
                            No contact inquiries received yet. Test by submitting a message from the public contact page.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Current Projects Quick List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Projects</h3>
                    <a href="<?= url('admin/projects.php') ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Manage All</a>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($recentProjects)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentProjects as $proj): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #fff;"><?= e($proj['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($proj['category']) ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.775rem; color: #10b981; font-weight: 600;"><?= e($proj['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= url('admin/project-edit.php?id=' . $proj['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted);">
                            No projects found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System & Environment Diagnostics -->
        <div class="admin-card" style="margin-top: 1rem;">
            <div class="admin-card-header">
                <h3 class="admin-card-title">System Environment</h3>
            </div>
            <div class="admin-card-body" style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--admin-text-dim);">PHP Version:</span>
                    <strong style="color: #fff; margin-left: 0.5rem;"><?= PHP_VERSION ?></strong>
                </div>
                <div>
                    <span style="color: var(--admin-text-dim);">Database:</span>
                    <strong style="color: #10b981; margin-left: 0.5rem;">MariaDB / MySQL (Connected via PDO)</strong>
                </div>
                <div>
                    <span style="color: var(--admin-text-dim);">Uploads Directory:</span>
                    <strong style="color: #10b981; margin-left: 0.5rem;"><?= is_writable(UPLOADS_PATH) ? 'Writable (Ready)' : 'Not Writable' ?></strong>
                </div>
                <div>
                    <span style="color: var(--admin-text-dim);">Root Path:</span>
                    <code style="color: var(--admin-text-muted); margin-left: 0.5rem;"><?= e(ROOT_PATH) ?></code>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
