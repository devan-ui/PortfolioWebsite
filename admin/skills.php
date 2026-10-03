<?php
/**
 * Skills & Technologies Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Skills Management';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$errors = [];

// Edit mode check
$editId = (int)($_GET['edit'] ?? 0);
$editSkill = null;
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM skills WHERE id = :id LIMIT 1");
    $editStmt->execute(['id' => $editId]);
    $editSkill = $editStmt->fetch();
}

// Handle Form Submissions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_skill') {
            $id = (int)($_POST['skill_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? 'Programming');
            $proficiency = trim($_POST['proficiency'] ?? 'Beginner');
            $displayOrder = (int)($_POST['display_order'] ?? 0);

            if (empty($name)) {
                $errors[] = 'Skill name is required.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE skills SET name = :name, category = :category, proficiency = :proficiency, display_order = :ord WHERE id = :id");
                    $stmt->execute([
                        'name' => $name,
                        'category' => $category,
                        'proficiency' => $proficiency,
                        'ord' => $displayOrder,
                        'id' => $id
                    ]);
                    set_flash('success', 'Skill updated successfully.');
                } else {
                    $stmt = $db->prepare("INSERT INTO skills (name, category, proficiency, display_order) VALUES (:name, :category, :proficiency, :ord)");
                    $stmt->execute([
                        'name' => $name,
                        'category' => $category,
                        'proficiency' => $proficiency,
                        'ord' => $displayOrder
                    ]);
                    set_flash('success', 'Skill added successfully.');
                }
                header('Location: ' . url('admin/skills.php'));
                exit;
            }
        } elseif ($action === 'delete_skill') {
            $id = (int)($_POST['skill_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM skills WHERE id = :id")->execute(['id' => $id]);
                set_flash('success', 'Skill removed.');
                header('Location: ' . url('admin/skills.php'));
                exit;
            }
        }
    }
}

// Category filter
$filterCat = trim($_GET['category'] ?? '');
$sql = "SELECT * FROM skills";
$params = [];
if (!empty($filterCat)) {
    $sql .= " WHERE category = :cat";
    $params['cat'] = $filterCat;
}
$sql .= " ORDER BY category ASC, display_order ASC, name ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$skillsList = $stmt->fetchAll();

$categories = ['Programming', 'Frontend', 'Backend', 'Databases', 'Tools', 'AI/ML'];
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.25rem 0;">Skills &amp; Technologies</h2>
                <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.9rem;">
                    Manage technical abilities, frameworks, tools, and adjust proficiency levels (Beginner / Intermediate / Advanced).
                </p>
            </div>
            <a href="<?= url('skills.php') ?>" target="_blank" class="admin-btn admin-btn-secondary">
                View Public Skills Page ↗
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

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
            <!-- Left: Add / Edit Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><?= $editSkill ? 'Edit Skill: ' . e($editSkill['name']) : '+ Add New Skill' ?></h3>
                    <?php if ($editSkill): ?>
                        <a href="<?= url('admin/skills.php') ?>" style="font-size: 0.8rem; color: var(--admin-text-muted);">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/skills.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_skill">
                        <input type="hidden" name="skill_id" value="<?= $editSkill['id'] ?? 0 ?>">

                        <div class="admin-form-group">
                            <label for="name" class="admin-label">Skill / Technology Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="name" class="admin-input" placeholder="e.g. FastAPI" value="<?= e($editSkill['name'] ?? '') ?>" required>
                        </div>

                        <div class="admin-form-group">
                            <label for="category" class="admin-label">Category</label>
                            <select name="category" id="category" class="admin-select">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= e($cat) ?>" <?= ($editSkill && $editSkill['category'] === $cat) ? 'selected' : '' ?>>
                                        <?= e($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="proficiency" class="admin-label">Proficiency Level</label>
                            <select name="proficiency" id="proficiency" class="admin-select">
                                <option value="Beginner" <?= ($editSkill && $editSkill['proficiency'] === 'Beginner') ? 'selected' : '' ?>>Beginner</option>
                                <option value="Intermediate" <?= ($editSkill && $editSkill['proficiency'] === 'Intermediate') ? 'selected' : '' ?>>Intermediate</option>
                                <option value="Advanced" <?= ($editSkill && $editSkill['proficiency'] === 'Advanced') ? 'selected' : '' ?>>Advanced</option>
                            </select>
                            <span class="admin-help-text">Can be updated anytime as experience deepens</span>
                        </div>

                        <div class="admin-form-group">
                            <label for="display_order" class="admin-label">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="admin-input" value="<?= (int)($editSkill['display_order'] ?? 0) ?>">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                            <?= $editSkill ? 'Save Changes' : 'Add Skill' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Skills List with Category Filters -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Catalogue (<?= count($skillsList) ?> skills)</h3>
                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                        <a href="<?= url('admin/skills.php') ?>" class="admin-btn admin-btn-sm <?= empty($filterCat) ? 'admin-btn-primary' : 'admin-btn-secondary' ?>">All</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= url('admin/skills.php?category=' . urlencode($cat)) ?>" class="admin-btn admin-btn-sm <?= $filterCat === $cat ? 'admin-btn-primary' : 'admin-btn-secondary' ?>">
                                <?= e($cat) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($skillsList)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Proficiency</th>
                                        <th>Order</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($skillsList as $s): ?>
                                        <tr>
                                            <td style="font-weight: 700; color: #fff;">
                                                <?= e($s['name']) ?>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= e($s['category']) ?></span>
                                            </td>
                                            <td>
                                                <?= render_proficiency_badge($s['proficiency']) ?>
                                            </td>
                                            <td style="color: var(--admin-text-dim); font-family: var(--font-mono);">
                                                <?= $s['display_order'] ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <a href="<?= url('admin/skills.php?edit=' . $s['id'] . (!empty($filterCat) ? '&category=' . urlencode($filterCat) : '')) ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                        Edit
                                                    </a>
                                                    <form action="<?= url('admin/skills.php') ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_skill">
                                                        <input type="hidden" name="skill_id" value="<?= $s['id'] ?>">
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" data-confirm="Remove '<?= e($s['name']) ?>' from skills?">
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
                        <div style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted);">
                            No skills found in this category.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
