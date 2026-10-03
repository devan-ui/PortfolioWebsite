<?php
/**
 * Education History Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Education Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM education WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_education') {
            $id = (int)($_POST['education_id'] ?? 0);
            $degree = trim($_POST['degree'] ?? '');
            $field = trim($_POST['field_of_study'] ?? '');
            $institution = trim($_POST['institution'] ?? '');
            $location = trim($_POST['location'] ?? 'Kerala, India');
            $startYear = trim($_POST['start_year'] ?? '');
            $endYear = trim($_POST['end_year'] ?? '');
            $isCurrent = isset($_POST['is_current']) ? 1 : 0;
            $grade = trim($_POST['grade'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($degree) || empty($institution) || empty($startYear)) {
                $errors[] = 'Degree, Institution, and Start Year are required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE education SET degree = :deg, field_of_study = :fld, institution = :inst, location = :loc, start_year = :sy, end_year = :ey, is_current = :cur, grade = :grd, description = :dsc, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'deg' => $degree, 'fld' => $field, 'inst' => $institution, 'loc' => $location,
                        'sy' => $startYear, 'ey' => $endYear, 'cur' => $isCurrent, 'grd' => $grade,
                        'dsc' => $desc, 'ord' => $displayOrder, 'id' => $id
                    ]);
                    set_flash('success', 'Education record updated successfully.');
                } else {
                    $stmt = $db->prepare("INSERT INTO education (degree, field_of_study, institution, location, start_year, end_year, is_current, grade, description, display_order) VALUES (:deg, :fld, :inst, :loc, :sy, :ey, :cur, :grd, :dsc, :ord)");
                    $stmt->execute([
                        'deg' => $degree, 'fld' => $field, 'inst' => $institution, 'loc' => $location,
                        'sy' => $startYear, 'ey' => $endYear, 'cur' => $isCurrent, 'grd' => $grade,
                        'dsc' => $desc, 'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Education record added successfully.');
                }
                header('Location: ' . url('admin/education.php'));
                exit;
            }
        } elseif ($action === 'delete_education') {
            $id = (int)($_POST['education_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM education WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Education record removed.');
                header('Location: ' . url('admin/education.php'));
                exit;
            }
        }
    }
}

$educationList = $db->query("SELECT * FROM education ORDER BY display_order ASC, start_year DESC")->fetchAll();
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Academic Education</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage degrees, universities, graduation years, and academic descriptions.
                </p>
            </div>
            <a href="<?= url('education.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Education Page ↗
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
                    <h3 class="admin-card-title"><?= $editItem ? 'Edit Education Entry' : '+ Add Education Entry' ?></h3>
                    <?php if ($editItem): ?>
                        <a href="<?= url('admin/education.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/education.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_education">
                        <input type="hidden" name="education_id" value="<?= $editItem['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="degree" class="admin-label">Degree Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="degree" id="degree" class="admin-input" placeholder="e.g. Master of Computer Applications (MCA)" value="<?= e($editItem['degree'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="field_of_study" class="admin-label">Major / Field of Study</label>
                            <input type="text" name="field_of_study" id="field_of_study" class="admin-input" placeholder="e.g. Computer Applications" value="<?= e($editItem['field_of_study'] ?? '') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="institution" class="admin-label">Institution / University <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="institution" id="institution" class="admin-input" placeholder="e.g. APJ Abdul Kalam Technological University" value="<?= e($editItem['institution'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="location" class="admin-label">Location</label>
                            <input type="text" name="location" id="location" class="admin-input" value="<?= e($editItem['location'] ?? 'Kerala, India') ?>">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="start_year" class="admin-label">Start Year</label>
                                <input type="text" name="start_year" id="start_year" class="admin-input" placeholder="2024" value="<?= e($editItem['start_year'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="end_year" class="admin-label">End Year</label>
                                <input type="text" name="end_year" id="end_year" class="admin-input" placeholder="2026" value="<?= e($editItem['end_year'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #fff; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_current" value="1" <?= (!empty($editItem['is_current'])) ? 'checked' : '' ?>>
                                <span>Currently Studying / Pursuing</span>
                            </label>
                        </div>

                        <div class="admin-form-group">
                            <label for="grade" class="admin-label">Grade / Status</label>
                            <input type="text" name="grade" id="grade" class="admin-input" placeholder="e.g. Pursuing / Completed" value="<?= e($editItem['grade'] ?? '') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="description" class="admin-label">Description / Coursework</label>
                            <textarea name="description" id="description" class="admin-textarea" rows="3" placeholder="Key topics studied, algorithms, projects..."><?= e($editItem['description'] ?? '') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editItem['display_order'] ?? 0) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editItem ? 'Save Changes' : 'Add Education Record' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Academic History</h3>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($educationList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Degree</th>
                                        <th>Institution</th>
                                        <th>Period</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($educationList as $edu): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #fff;"><?= e($edu['degree']) ?></div>
                                                <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($edu['field_of_study']) ?></div>
                                            </td>
                                            <td>
                                                <div style="color: var(--admin-text-main);"><?= e($edu['institution']) ?></div>
                                                <div style="font-size: 0.775rem; color: var(--admin-text-dim);"><?= e($edu['location']) ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.825rem; color: var(--admin-primary); font-family: var(--font-mono);">
                                                    <?= e($edu['start_year']) ?> – <?= $edu['is_current'] ? 'Present' : e($edu['end_year']) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/education.php?edit=' . $edu['id']) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>
                                                    <form action="<?= url('admin/education.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_education">
                                                        <input type="hidden" name="education_id" value="<?= $edu['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Delete this education entry?">Delete</button>
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
                            No education entries found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
