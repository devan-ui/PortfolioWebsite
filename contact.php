<?php
/**
 * Contact Page & Form Handler
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/includes/functions.php';

$profile = get_profile();
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf_token()) {
        $errors[] = 'Security token mismatch. Please try submitting again.';
    }

    // 2. Anti-spam Honeypot Check
    if (!empty($_POST['website_hp'])) {
        // Silent block for bot submissions
        set_flash('success', 'Thank you! Your message has been received.');
        header('Location: ' . url('contact.php'));
        exit;
    }

    // 3. Simple Rate Limiting (1 message every 15 seconds per session)
    if (isset($_SESSION['last_contact_time']) && (time() - $_SESSION['last_contact_time'] < 15)) {
        $errors[] = 'Please wait a moment before sending another message.';
    }

    // 4. Sanitize and retrieve form inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $old = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ];

    // 5. Server-Side Validations
    if (empty($name) || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Please provide a valid name between 2 and 100 characters.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (empty($subject) || mb_strlen($subject) < 3 || mb_strlen($subject) > 150) {
        $errors[] = 'Please provide a descriptive subject (3 to 150 characters).';
    }

    if (empty($message) || mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
        $errors[] = 'Message content must be between 10 and 5000 characters.';
    }

    // 6. If no errors, persist to database using PDO prepared statements
    if (empty($errors)) {
        try {
            $db = get_db();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address, is_read) VALUES (:name, :email, :subject, :message, :ip, 0)");
            
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmt->execute([
                'name'    => $name,
                'email'   => $email,
                'subject' => $subject,
                'message' => $message,
                'ip'      => substr($ip, 0, 45)
            ]);

            $_SESSION['last_contact_time'] = time();
            set_flash('success', 'Your message has been sent successfully! I will review it and get back to you shortly.');
            header('Location: ' . url('contact.php'));
            exit;
        } catch (PDOException $e) {
            $errors[] = 'A database error occurred while saving your message. Please try again.';
        }
    }
}

$pageIdentifier = 'contact';
$displayedName = !empty($profile['displayed_name']) ? $profile['displayed_name'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Me');
$profTitle = !empty($profile['professional_title']) ? $profile['professional_title'] : 'Web Developer';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>Contact</span>
        </nav>
        <span class="section-subtitle">Get In Touch</span>
        <h1>Contact <?= e($displayedName) ?></h1>
        <p class="section-description">
            Have a project in mind, an opportunity, or want to discuss technical collaboration? Send a direct message below.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="grid-2" style="align-items: start; gap: 4rem;">
            <!-- Left Column: Contact Information Cards -->
            <div>
                <span class="section-subtitle">Direct Coordinates</span>
                <h2 style="margin-bottom: 1.5rem;">Let's Connect</h2>
                <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 2.5rem;">
                    I am actively seeking <?= e(strtolower($profTitle)) ?> opportunities and projects. If you are looking for a dedicated developer, I'd love to hear from you.
                </p>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
                        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(59, 130, 246, 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Email Address</div>
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 1rem;"><?= e($profile['email'] ?? 'devanandancs@example.com') ?></div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" data-copy="<?= e($profile['email'] ?? 'devanandancs@example.com') ?>">
                            Copy
                        </button>
                    </div>

                    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
                        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Location</div>
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 1rem;"><?= e($profile['location'] ?? 'Ernakulam, Kerala, India') ?></div>
                        </div>
                    </div>

                    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
                        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Current Availability</div>
                            <div style="font-weight: 600; color: var(--accent-emerald); font-size: 1rem;"><?= e($profile['availability_status'] ?? 'Available') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Database-Backed Contact Form -->
            <div>
                <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Send a Message</h2>
                    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1.75rem;">
                        Fill out the details below and your message will be securely recorded in the database.
                    </p>

                    <?php if (!empty($errors)): ?>
                        <div class="flash-message flash-error" style="margin-bottom: 1.5rem;">
                            <ul style="padding-left: 1.25rem; margin: 0;">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= e($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('contact.php') ?>" method="POST" novalidate id="contact-form">
                        <?= csrf_field() ?>

                        <!-- Anti-spam honeypot (hidden from human visitors) -->
                        <div style="display: none;" aria-hidden="true">
                            <label for="website_hp">Leave this empty</label>
                            <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Full Name <span style="color: var(--accent-rose);">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Jane Doe" value="<?= e($old['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address <span style="color: var(--accent-rose);">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="e.g. jane@company.com" value="<?= e($old['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Subject <span style="color: var(--accent-rose);">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. Web Developer Role / Project Inquiry" value="<?= e($old['subject'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message <span style="color: var(--accent-rose);">*</span></label>
                            <textarea name="message" id="message" class="form-control" rows="5" placeholder="Share the details of your inquiry or opportunity..." required><?= e($old['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            Submit Message
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
