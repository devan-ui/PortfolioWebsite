<?php
/**
 * Contact Inquiries & Messages Manager
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Contact Messages';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();

// View single message modal or detail
$viewId = (int)($_GET['id'] ?? 0);
$viewMessage = null;
if ($viewId > 0) {
    $vStmt = $db->prepare("SELECT * FROM contact_messages WHERE id = :id LIMIT 1");
    $vStmt->execute(['id' => $viewId]);
    $viewMessage = $vStmt->fetch();

    if ($viewMessage && !$viewMessage['is_read']) {
        // Automatically mark as read when viewed
        $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = :id")->execute(['id' => $viewId]);
        $viewMessage['is_read'] = 1;
    }
}

// Handle Actions (Toggle Read, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Invalid security token.');
    } else {
        $action = $_POST['action'] ?? '';
        $msgId = (int)($_POST['message_id'] ?? 0);

        if ($action === 'toggle_read' && $msgId > 0) {
            $db->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = :id")->execute(['id' => $msgId]);
            set_flash('success', 'Message status updated.');
            header('Location: ' . url('admin/messages.php'));
            exit;
        } elseif ($action === 'delete_message' && $msgId > 0) {
            $db->prepare("DELETE FROM contact_messages WHERE id = :id")->execute(['id' => $msgId]);
            set_flash('success', 'Message deleted.');
            header('Location: ' . url('admin/messages.php'));
            exit;
        }
    }
}

// Fetch all messages
$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Contact Inquiries</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Inquiries submitted through your public contact form are recorded here in real-time.
                </p>
            </div>
        </div>

        <?php if ($viewMessage): ?>
            <!-- Selected Message Detail View -->
            <div class="admin-card" style="border-color: var(--admin-primary); margin-bottom: 2.5rem;">
                <div class="admin-card-header" style="background: rgba(59, 130, 246, 0.1);">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <h3 class="admin-card-title"><?= e($viewMessage['subject']) ?></h3>
                        <span style="font-size: 0.75rem; color: var(--admin-primary); background: rgba(59, 130, 246, 0.2); padding: 0.2rem 0.5rem; border-radius: 4px;">
                            Received: <?= date('M d, Y H:i:s', strtotime($viewMessage['created_at'])) ?>
                        </span>
                    </div>
                    <a href="<?= url('admin/messages.php') ?>" class="admin-btn admin-btn-secondary admin-btn-sm">&times; Close Detail</a>
                </div>
                <div class="admin-card-body">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border); flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #fff;"><?= e($viewMessage['name']) ?></div>
                            <div style="color: var(--admin-primary); font-size: 0.95rem;">
                                <a href="mailto:<?= e($viewMessage['email']) ?>?subject=Re: <?= urlencode($viewMessage['subject']) ?>" style="color: inherit;">
                                    <?= e($viewMessage['email']) ?> ✉
                                </a>
                            </div>
                            <?php if (!empty($viewMessage['ip_address'])): ?>
                                <div style="font-size: 0.775rem; color: var(--admin-text-dim); margin-top: 0.25rem;">
                                    IP: <?= e($viewMessage['ip_address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <a href="mailto:<?= e($viewMessage['email']) ?>?subject=Re: <?= urlencode($viewMessage['subject']) ?>" class="admin-btn admin-btn-primary admin-btn-sm">
                                Reply via Email &rarr;
                            </a>
                            <form action="<?= url('admin/messages.php') ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle_read">
                                <input type="hidden" name="message_id" value="<?= $viewMessage['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm">
                                    Mark as <?= $viewMessage['is_read'] ? 'Unread' : 'Read' ?>
                                </button>
                            </form>
                            <form action="<?= url('admin/messages.php') ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_message">
                                <input type="hidden" name="message_id" value="<?= $viewMessage['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Permanently delete this message?">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div style="font-size: 1rem; color: var(--admin-text-main); line-height: 1.8; background: var(--admin-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--admin-border); white-space: pre-wrap;">
                        <?= e($viewMessage['message']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Messages Listing Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">All Inquiries (<?= count($messages) ?>)</h3>
            </div>
            <div class="admin-card-body" style="padding: 0;">
                <?php if (!empty($messages)): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Sender</th>
                                    <th>Subject &amp; Snippet</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $m): ?>
                                    <tr style="<?= !$m['is_read'] ? 'background-color: rgba(59, 130, 246, 0.04);' : '' ?>">
                                        <td>
                                            <?php if (!$m['is_read']): ?>
                                                <span style="font-size: 0.725rem; font-weight: 700; color: #ef4444; background: rgba(239, 68, 68, 0.15); padding: 0.2rem 0.55rem; border-radius: 9999px;">NEW</span>
                                            <?php else: ?>
                                                <span style="font-size: 0.75rem; color: var(--admin-text-dim);">Read</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #fff;"><?= e($m['name']) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($m['email']) ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: var(--admin-text-main);"><?= e($m['subject']) ?></div>
                                            <div style="font-size: 0.825rem; color: var(--admin-text-dim); max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= e($m['message']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.825rem; color: var(--admin-text-dim);">
                                                <?= date('M d, Y', strtotime($m['created_at'])) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: inline-flex; gap: 0.4rem;">
                                                <a href="<?= url('admin/messages.php?id=' . $m['id']) ?>" class="admin-btn admin-btn-primary admin-btn-sm">
                                                    Read
                                                </a>
                                                <form action="<?= url('admin/messages.php') ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="toggle_read">
                                                    <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm" title="Toggle read/unread">
                                                        <?= $m['is_read'] ? 'Mark Unread' : 'Mark Read' ?>
                                                    </button>
                                                </form>
                                                <form action="<?= url('admin/messages.php') ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_message">
                                                    <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete message from <?= e($m['name']) ?>?">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; color: var(--admin-text-muted);">
                        No contact messages received yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
