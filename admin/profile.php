<?php
/**
 * Profile & Resume Management
 * Devanandan CS Portfolio Website
 */

$adminPageTitle = 'Manage Profile & Resume';
require_once __DIR__ . '/includes/admin-header.php';

$db = get_db();
$profile = get_profile();
$errors = [];

// Handle Profile and File Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security token invalid.';
    } else {
        $action = $_POST['action'] ?? 'update_profile';

        if ($action === 'update_profile') {
            $fullName = trim($_POST['full_name'] ?? '');
            $displayedName = trim($_POST['displayed_name'] ?? '');
            $profTitle = trim($_POST['professional_title'] ?? '');
            $tagline = trim($_POST['tagline'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $careerGoal = trim($_POST['career_goal'] ?? '');
            $availStatus = trim($_POST['availability_status'] ?? '');
            $githubUrl = trim($_POST['github_url'] ?? '');
            $linkedinUrl = trim($_POST['linkedin_url'] ?? '');

            $photoPath = $profile['profile_photo'] ?? null;
            $resumePath = $profile['resume_file'] ?? null;

            // Handle Profile Photo Upload if present
            if (!empty($_FILES['profile_photo']['name'])) {
                $uploadRes = upload_file($_FILES['profile_photo'], 'profile', ALLOWED_IMAGE_TYPES, ALLOWED_IMAGE_EXTS);
                if ($uploadRes['success']) {
                    if (!empty($profile['profile_photo'])) {
                        $oldPhoto = UPLOADS_PATH . '/' . ltrim(str_replace('\\', '/', $profile['profile_photo']), '/');
                        if (file_exists($oldPhoto)) {
                            @unlink($oldPhoto);
                        }
                    }
                    $photoPath = $uploadRes['relative_path'];
                } else {
                    $errors[] = 'Photo upload error: ' . $uploadRes['error'];
                }
            }

            // Handle Resume Upload if present
            if (!empty($_FILES['resume_file']['name'])) {
                $uploadRes = upload_file($_FILES['resume_file'], 'resumes', ALLOWED_DOC_TYPES, ALLOWED_DOC_EXTS);
                if ($uploadRes['success']) {
                    if (!empty($profile['resume_file'])) {
                        $oldResume = UPLOADS_PATH . '/' . ltrim(str_replace('\\', '/', $profile['resume_file']), '/');
                        if (file_exists($oldResume)) {
                            @unlink($oldResume);
                        }
                    }
                    $resumePath = $uploadRes['relative_path'];
                } else {
                    $errors[] = 'Resume upload error: ' . $uploadRes['error'];
                }
            }

            if (empty($errors)) {
                try {
                    $stmt = $db->prepare("UPDATE profile SET 
                        full_name = :full_name,
                        displayed_name = :displayed_name,
                        professional_title = :professional_title,
                        tagline = :tagline,
                        bio = :bio,
                        location = :location,
                        email = :email,
                        phone = :phone,
                        career_goal = :career_goal,
                        availability_status = :availability_status,
                        profile_photo = :profile_photo,
                        resume_file = :resume_file,
                        github_url = :github_url,
                        linkedin_url = :linkedin_url
                        WHERE id = :id");

                    $stmt->execute([
                        'full_name' => $fullName,
                        'displayed_name' => $displayedName,
                        'professional_title' => $profTitle,
                        'tagline' => $tagline,
                        'bio' => $bio,
                        'location' => $location,
                        'email' => $email,
                        'phone' => $phone,
                        'career_goal' => $careerGoal,
                        'availability_status' => $availStatus,
                        'profile_photo' => $photoPath,
                        'resume_file' => $resumePath,
                        'github_url' => $githubUrl,
                        'linkedin_url' => $linkedinUrl,
                        'id' => $profile['id'] ?? 1
                    ]);

                    // Invalidate profile cache
                    $GLOBALS['_cached_profile'] = null;
                    set_flash('success', 'Profile and resume information updated successfully!');
                    header('Location: ' . url('admin/profile.php'));
                    exit;
                } catch (PDOException $e) {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'change_password') {
            $currentPass = $_POST['current_password'] ?? '';
            $newPass = $_POST['new_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            if ($newPass !== $confirmPass) {
                $errors[] = 'New passwords do not match.';
            } else {
                $pwResult = admin_change_password($_SESSION['admin_id'], $currentPass, $newPass);
                if ($pwResult['success']) {
                    set_flash('success', 'Admin password changed successfully!');
                    header('Location: ' . url('admin/profile.php'));
                    exit;
                } else {
                    $errors[] = $pwResult['error'];
                }
            }
        }
    }
}

// Refresh profile
$profile = get_profile();
$photoUrl = !empty($profile['profile_photo']) ? upload_url($profile['profile_photo'], 'profile') : asset('images/avatar-placeholder.svg');
$resumeUrl = !empty($profile['resume_file']) ? upload_url($profile['resume_file'], 'resumes') : null;
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <?php if (!empty($errors)): ?>
            <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left: Main Profile Edit Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Personal Identity &amp; Bio</h2>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/profile.php') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_profile">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="full_name" class="admin-label">Full Name</label>
                                <input type="text" name="full_name" id="full_name" class="admin-input" value="<?= e($profile['full_name'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="displayed_name" class="admin-label">Displayed Name</label>
                                <input type="text" name="displayed_name" id="displayed_name" class="admin-input" value="<?= e($profile['displayed_name'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="professional_title" class="admin-label">Professional Identity / Title</label>
                            <input type="text" name="professional_title" id="professional_title" class="admin-input" value="<?= e($profile['professional_title'] ?? '') ?>" required>
                            <span class="admin-help-text">E.g., "MCA Student, Full Stack Developer"</span>
                        </div>

                        <div class="admin-form-group">
                            <label for="tagline" class="admin-label">Tagline (Concise Professional Statement)</label>
                            <input type="text" name="tagline" id="tagline" class="admin-input" value="<?= e($profile['tagline'] ?? '') ?>" required>
                            <span class="admin-help-text">Highlights MCA studies, web development, and practical projects</span>
                        </div>

                        <div class="admin-form-group">
                            <label for="bio" class="admin-label">Full Biography / About Story</label>
                            <textarea name="bio" id="bio" class="admin-textarea" rows="5" required><?= e($profile['bio'] ?? '') ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="location" class="admin-label">Location</label>
                                <input type="text" name="location" id="location" class="admin-input" value="<?= e($profile['location'] ?? 'Ernakulam, Kerala, India') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="career_goal" class="admin-label">Career Goal</label>
                                <input type="text" name="career_goal" id="career_goal" class="admin-input" value="<?= e($profile['career_goal'] ?? 'Become a web developer') ?>" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="email" class="admin-label">Email Address</label>
                                <input type="email" name="email" id="email" class="admin-input" value="<?= e($profile['email'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="availability_status" class="admin-label">Availability Status</label>
                                <input type="text" name="availability_status" id="availability_status" class="admin-input" value="<?= e($profile['availability_status'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="admin-form-group">
                                <label for="github_url" class="admin-label">GitHub URL</label>
                                <input type="url" name="github_url" id="github_url" class="admin-input" value="<?= e($profile['github_url'] ?? '') ?>">
                            </div>
                            <div class="admin-form-group">
                                <label for="linkedin_url" class="admin-label">LinkedIn URL</label>
                                <input type="url" name="linkedin_url" id="linkedin_url" class="admin-input" value="<?= e($profile['linkedin_url'] ?? '') ?>">
                            </div>
                        </div>

                        <h3 style="margin: 2rem 0 1rem 0; font-size: 1.1rem; color: #fff; border-top: 1px solid var(--admin-border); padding-top: 1.5rem;">Media &amp; Documents</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <!-- Profile Photo -->
                            <div class="admin-form-group">
                                <label class="admin-label">Profile Photo</label>
                                <div class="image-upload-preview" id="photo-preview-target">
                                    <img src="<?= e($photoUrl) ?>" alt="Current Avatar">
                                </div>
                                <input type="file" name="profile_photo" id="profile_photo" class="admin-input" accept="image/*" data-preview="photo-preview-target">
                                <span class="admin-help-text">JPG, PNG, WebP up to 5MB</span>
                            </div>

                            <!-- Resume PDF -->
                            <div class="admin-form-group">
                                <label class="admin-label">Resume Document (PDF)</label>
                                <div style="margin-bottom: 0.75rem;">
                                    <?php if ($resumeUrl): ?>
                                        <a href="<?= e($resumeUrl) ?>" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm">
                                            📄 View Current Resume (<?= htmlspecialchars(basename($profile['resume_file'])) ?>)
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.825rem; color: var(--admin-text-muted);">No resume PDF uploaded yet.</span>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="resume_file" id="resume_file" class="admin-input" accept="application/pdf">
                                <span class="admin-help-text">PDF format, max 5MB</span>
                            </div>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary" style="margin-top: 1.5rem; padding: 0.85rem 1.75rem;">
                            Save Profile &amp; Media
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Change Admin Password -->
            <div>
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Security &amp; Password</h2>
                    </div>
                    <div class="admin-card-body">
                        <form action="<?= url('admin/profile.php') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="change_password">

                            <div class="admin-form-group">
                                <label for="current_password" class="admin-label">Current Password</label>
                                <input type="password" name="current_password" id="current_password" class="admin-input" required>
                            </div>

                            <div class="admin-form-group">
                                <label for="new_password" class="admin-label">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="admin-input" minlength="8" required>
                                <span class="admin-help-text">Minimum 8 characters</span>
                            </div>

                            <div class="admin-form-group">
                                <label for="confirm_password" class="admin-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="admin-input" minlength="8" required>
                            </div>

                            <button type="submit" class="admin-btn admin-btn-secondary" style="width: 100%;">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
