<?php
/**
 * Experience & Internships Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Experience Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM experience WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_experience') {
            $id = (int)($_POST['experience_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $location = trim($_POST['location'] ?? 'Ernakulam, Kerala, India');
            $startDate = trim($_POST['start_date'] ?? '');
            $endDate = trim($_POST['end_date'] ?? 'Present');
            $isCurrent = isset($_POST['is_current']) ? 1 : 0;
            $description = trim($_POST['description'] ?? '');
            $responsibilities = trim($_POST['responsibilities'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($title) || empty($company) || empty($startDate)) {
                $errors[] = 'Title, Company, and Start Date are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE experience SET title = :ttl, company = :cmp, location = :loc, start_date = :sd, end_date = :ed, is_current = :cur, description = :dsc, responsibilities = :resp, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'ttl' => $title, 'cmp' => $company, 'loc' => $location,
                        'sd' => $startDate, 'ed' => $endDate, 'cur' => $isCurrent,
                        'dsc' => $description, 'resp' => $responsibilities,
                        'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Experience record updated successfully.');
                } else {
                    $stmt = $db->prepare("INSERT INTO experience (title, company, location, start_date, end_date, is_current, description, responsibilities, display_order) VALUES (:ttl, :cmp, :loc, :sd, :ed, :cur, :dsc, :resp, :ord)");
                    $stmt->execute([
                        'ttl' => $title, 'cmp' => $company, 'loc' => $location,
                        'sd' => $startDate, 'ed' => $endDate, 'cur' => $isCurrent,
                        'dsc' => $description, 'resp' => $responsibilities,
                        'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Experience record created successfully.');
                }
                header('Location: ' . url('admin/experience.php'));
                exit;
            }
        } elseif ($action === 'delete_experience') {
            $id = (int)($_POST['experience_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM experience WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Experience record deleted.');
                header('Location: ' . url('admin/experience.php'));
                exit;
            }
        }
    }
}

$experienceList = $db->query("SELECT * FROM experience ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Experience &amp; Internships</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage practical project labs, internships, roles, and accomplishments.
                </p>
            </div>
            <a href="<?= url('experience.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Experience Page ↗
            </a>
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

        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: start;">
            <!-- Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Experience' : '+ Add Experience / Internship' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/experience.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/experience.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_experience">
                        <input type="hidden" name="experience_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="title" class="admin-label">Role Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" id="title" class="admin-input" placeholder="e.g. Web Development Intern" value="<?= e($editItem['title'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="company" class="admin-label">Company / Lab <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="company" id="company" class="admin-input" placeholder="e.g. Academic Project Lab" value="<?= e($editItem['company'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="location" class="admin-label">Location</label>
                            <input type="text" name="location" id="location" class="admin-input" value="<?= e($editItem['location'] ?? 'Ernakulam, Kerala, India') ?>">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="start_date" class="admin-label">Start Date</label>
                                <input type="text" name="start_date" id="start_date" class="admin-input" placeholder="e.g. Jan 2024" value="<?= e($editItem['start_date'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="end_date" class="admin-label">End Date</label>
                                <input type="text" name="end_date" id="end_date" class="admin-input" placeholder="e.g. Present" value="<?= e($editItem['end_date'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #fff; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_current" value="1" <?= (!empty($editItem['is_current'])) ? 'checked' : '' ?>>
                                <span>Currently Working / Active Role</span>
                            </label>
                        </div>

                        <div class="admin-form-group">
                            <label for="description" class="admin-label">Overview Description</label>
                            <textarea name="description" id="description" class="admin-textarea" rows="3" placeholder="Summary of role and technologies used..."><?= e($editItem['description'] ?? '') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="responsibilities" class="admin-label">Key Responsibilities / Bullet Points</label>
                            <textarea name="responsibilities" id="responsibilities" class="admin-textarea" rows="3" placeholder="Separate points with new lines or semicolons..."><?= e($editItem['responsibilities'] ?? '') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Experience Entry' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Experience Entries</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($experienceList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Role &amp; Company</th>
                                        <th>Dates</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($experienceList as $exp): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #fff;"><?= e($exp['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($exp['company']) ?> (<?= e($exp['location']) ?>)</div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.825rem; color: var(--admin-primary); font-family: var(--font-mono);">
                                                    <?= e($exp['start_date']) ?> – <?= $exp['is_current'] ? 'Present' : e($exp['end_date']) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/experience.php?edit=' . $exp['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/experience.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_experience">
                                                        <input type="hidden" name="experience_id" value="<?= $exp['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this experience record?">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted);">
                            No experience entries found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
