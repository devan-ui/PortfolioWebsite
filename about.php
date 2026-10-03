<?php
/**
 * About Me Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'about';
require_once __DIR__ . '/includes/header.php';

$profile = get_profile();
$avatarSrc = !empty($profile['profile_photo']) ? upload_url($profile['profile_photo'], 'profile') : asset('images/avatar-placeholder.svg');
$resumeSrc = !empty($profile['resume_file']) ? upload_url($profile['resume_file'], 'resumes') : null;
?>

<!-- Page Hero -->
<header class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= url('') ?>">Home</a>
            <span>/</span>
            <span>About</span>
        </nav>
        <span class="section-subtitle">Biography &amp; Background</span>
        <h1>About <?= e($profile['displayed_name'] ?? 'Me') ?></h1>
        <p class="section-description">
            <?= e($profile['professional_title'] ?? 'Full Stack Developer') ?> based in <?= e($profile['location'] ?? 'Ernakulam, Kerala') ?>.
        </p>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="grid-2" style="align-items: start; gap: 4rem;">
            <!-- Left Column: Photo & Quick Facts -->
            <div>
                <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; text-align: center;">
                    <div style="width: 160px; height: 160px; margin: 0 auto 1.5rem auto; border-radius: 50%; overflow: hidden; border: 4px solid var(--accent-primary); box-shadow: var(--shadow-md);">
                        <img src="<?= e($avatarSrc) ?>" alt="<?= e($profile['displayed_name'] ?? 'Devanandan CS') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><?= e($profile['displayed_name'] ?? 'Devanandan CS') ?></h2>
                    <p style="color: var(--accent-primary); font-weight: 600; font-size: 0.95rem; margin-bottom: 1.25rem;">
                        <?= e($profile['professional_title'] ?? 'MCA Student, Full Stack Developer') ?>
                    </p>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem; text-align: left; font-size: 0.925rem;">
                        <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Location:</span>
                            <span style="font-weight: 600; color: var(--text-primary);"><?= e($profile['location'] ?? 'Ernakulam, Kerala, India') ?></span>
                        </div>
                        <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Education:</span>
                            <span style="font-weight: 600; color: var(--text-primary);">MCA (Pursuing)</span>
                        </div>
                        <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Undergraduate:</span>
                            <span style="font-weight: 600; color: var(--text-primary);">BCA (Completed)</span>
                        </div>
                        <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Career Goal:</span>
                            <span style="font-weight: 600; color: var(--accent-emerald);"><?= e($profile['career_goal'] ?? 'Become a web developer') ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Status:</span>
                            <span style="font-weight: 600; color: var(--accent-primary);"><?= e($profile['availability_status'] ?? 'Available') ?></span>
                        </div>
                    </div>

                    <div style="margin-top: 1.75rem; display: flex; flex-direction: column; gap: 0.75rem;" id="resume">
                        <?php if ($resumeSrc): ?>
                            <a href="<?= e($resumeSrc) ?>" target="_blank" class="btn btn-primary" download>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Download Resume (PDF)
                            </a>
                        <?php else: ?>
                            <div style="padding: 0.75rem; border-radius: var(--radius-md); background-color: var(--bg-surface); border: 1px dashed var(--border-color); font-size: 0.85rem; color: var(--text-muted);">
                                📄 Resume is available upon request or via the contact form.
                            </div>
                        <?php endif; ?>
                        <a href="<?= url('contact.php') ?>" class="btn btn-secondary">Contact Me Directly</a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Biography & Technical Vision -->
            <div>
                <span class="section-subtitle">Personal Profile</span>
                <h2 style="margin-bottom: 1.5rem;">Passionate About Clean Architecture &amp; Meaningful Web Solutions</h2>

                <div style="color: var(--text-secondary); line-height: 1.8; font-size: 1.05rem;">
                    <p>
                        <?= nl2br(e($profile['bio'] ?? '')) ?>
                    </p>

                    <h3 style="margin: 2rem 0 1rem 0; color: var(--text-primary);">Educational Progression</h3>
                    <p>
                        Having completed my <strong>Bachelor of Computer Applications (BCA)</strong>, I am currently continuing my advanced studies in the <strong>Master of Computer Applications (MCA)</strong> program. This academic trajectory has equipped me with both rigorous computer science fundamentals—spanning data structures, algorithm design, and relational database systems—as well as modern software engineering methodologies.
                    </p>

                    <h3 style="margin: 2rem 0 1rem 0; color: var(--text-primary);">Practical Project Building</h3>
                    <p>
                        I believe that the best way to master web development is through hands-on project creation. To date, I have engineered full-featured systems including:
                    </p>
                    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;">
                            <strong>MentorHub:</strong> A community-based mentorship platform engineered with FastAPI, MongoDB, and React, featuring subscription management, OTP authentication, scheduled sessions, and multi-role workflows.
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <strong>TurfBooking:</strong> A sports ground and turf slot booking web application powered by clean PHP and MySQL, focusing on reliable scheduling and database integrity.
                        </li>
                    </ul>

                    <h3 style="margin: 2rem 0 1rem 0; color: var(--text-primary);">Career Objective</h3>
                    <p>
                        My definitive career objective is to <strong><?= e($profile['career_goal'] ?? 'become a web developer') ?></strong>. I am eager to contribute to forward-thinking engineering teams where I can bring dedication, solid full-stack fundamentals, and a relentless desire to learn and excel.
                    </p>

                    <div style="margin-top: 2.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="<?= url('skills.php') ?>" class="btn btn-primary">View Technical Skills &rarr;</a>
                        <a href="<?= url('education.php') ?>" class="btn btn-outline">Review Academic Journey &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
