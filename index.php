<?php
/**
 * Home / Landing Page
 * Devanandan CS Portfolio Website
 */

$pageIdentifier = 'home';
require_once __DIR__ . '/includes/header.php';

$profile = get_profile();
$db = get_db();

// Fetch featured projects
$featuredProjects = [];
try {
    $stmt = $db->query("SELECT * FROM projects WHERE featured = 1 ORDER BY display_order ASC LIMIT 3");
    $featuredProjects = $stmt->fetchAll();
} catch (Exception $e) {
    // Silence error
}

// Fetch preview skills (dynamically ordered by display order from database)
$previewSkills = [];
try {
    $stmt = $db->query("SELECT * FROM skills ORDER BY display_order ASC LIMIT 10");
    $previewSkills = $stmt->fetchAll();
} catch (Exception $e) {
    // Silence error
}

// Stats
$totalProjects = 0;
$totalSkills = 0;
try {
    $totalProjects = (int)$db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $totalSkills = (int)$db->query("SELECT COUNT(*) FROM skills")->fetchColumn();
} catch (Exception $e) {}

// Dynamic Profile Fields (from admin panel)
$heroName = !empty($profile['displayed_name']) ? trim($profile['displayed_name']) : (!empty($profile['full_name']) ? trim($profile['full_name']) : 'Developer');
$nameParts = preg_split('/\s+/', $heroName, 2);
$firstName = $nameParts[0] ?? $heroName;
$secondName = $nameParts[1] ?? '';

$profTitle = !empty($profile['professional_title']) ? $profile['professional_title'] : 'Full Stack Developer';
$tagline = !empty($profile['tagline']) ? $profile['tagline'] : 'I craft solid, database-driven web applications and scalable software products.';
$availStatus = !empty($profile['availability_status']) ? $profile['availability_status'] : 'Available for Developer Roles & Internships';

$avatarSrc = !empty($profile['profile_photo']) ? upload_url($profile['profile_photo'], 'profile') : asset('images/avatar-placeholder.svg');
$resumeUrl = !empty($profile['resume_file']) ? upload_url($profile['resume_file'], 'resumes') : url('about.php#resume');
?>

<style>
/* Modern High-End Creative Hero (Olaolu Design Adaptation) */
.creative-hero {
    position: relative;
    padding: 5.5rem 0 6rem 0;
    overflow: hidden;
    background: radial-gradient(circle at 85% 45%, rgba(59, 130, 246, 0.15) 0%, rgba(16, 185, 129, 0.08) 35%, transparent 65%);
}

.creative-hero-grid {
    display: grid;
    grid-template-columns: 1.18fr 0.82fr;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 2;
}

.hero-editorial-content {
    display: flex;
    flex-direction: column;
    gap: 1.6rem;
}

.hero-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.95rem;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: var(--radius-full);
    font-size: 0.825rem;
    font-weight: 600;
    color: var(--accent-emerald);
    width: fit-content;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--accent-emerald);
    box-shadow: 0 0 8px var(--accent-emerald);
}

.hero-name-headline {
    font-family: var(--font-heading);
    font-size: clamp(3.6rem, 8vw, 6.2rem);
    font-weight: 800;
    letter-spacing: -0.045em;
    line-height: 0.94;
    color: var(--text-primary);
    margin: 0;
}

.hero-surname-gradient {
    background: linear-gradient(135deg, var(--accent-primary) 0%, #60a5fa 45%, var(--accent-emerald) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline;
}

.dot-accent {
    color: var(--accent-emerald);
    -webkit-text-fill-color: var(--accent-emerald);
}

.hero-role-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-top: -0.25rem;
}

.hero-role-highlight {
    color: var(--accent-primary);
}

.hero-role-badge {
    background: rgba(59, 130, 246, 0.12);
    border: 1px solid rgba(59, 130, 246, 0.25);
    padding: 0.2rem 0.7rem;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    color: var(--accent-primary);
    font-weight: 600;
}

.hero-editorial-lead {
    font-size: 1.25rem;
    line-height: 1.65;
    color: var(--text-secondary);
    max-width: 560px;
    margin: 0;
}

/* Two-column feature highlights (from reference design) */
.hero-feature-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 1.25rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.hero-feat-item {
    font-size: 0.925rem;
    line-height: 1.6;
    color: var(--text-secondary);
}

.hero-feat-item strong {
    color: var(--text-primary);
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
    font-size: 0.95rem;
}

.hero-actions-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}

/* Portrait & Geometric Accents Canvas */
.creative-visual-stage {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2.5rem 1.5rem;
}

.portrait-frame-wrapper {
    position: relative;
    width: 100%;
    max-width: 380px;
    aspect-ratio: 0.92 / 1;
}

/* Offset geometric wireframe border behind the photo */
.portrait-wireframe-border {
    position: absolute;
    top: 24px;
    left: 24px;
    right: -24px;
    bottom: -24px;
    border: 2.5px solid var(--accent-primary);
    border-radius: var(--radius-lg);
    z-index: 1;
    pointer-events: none;
    transition: transform var(--transition-normal);
}

[data-theme="light"] .portrait-wireframe-border {
    border-color: var(--accent-primary);
}

/* Clean Portrait Photo Container */
.portrait-photo-card {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--bg-card);
    box-shadow: 0 25px 50px -15px rgba(0, 0, 0, 0.7), 0 0 40px rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.14);
    z-index: 2;
    transition: transform var(--transition-normal), box-shadow var(--transition-normal);
}

.portrait-photo-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.8), 0 0 50px rgba(59, 130, 246, 0.35);
}

.portrait-photo-card:hover ~ .portrait-wireframe-border {
    transform: translate(6px, 6px);
}

.portrait-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}

/* Geometric Dot Matrix Accents */
.geo-dot-grid {
    position: absolute;
    z-index: 0;
    pointer-events: none;
    opacity: 0.55;
}

.geo-dot-grid-top {
    top: -30px;
    left: -40px;
    color: var(--accent-primary);
}

.geo-dot-grid-bottom {
    bottom: -35px;
    right: -40px;
    color: var(--accent-emerald);
}

/* Geometric Staircase / Zigzag Vectors */
.geo-zigzag {
    position: absolute;
    z-index: 3;
    pointer-events: none;
    opacity: 0.85;
}

.geo-zigzag-left {
    bottom: 35px;
    left: -52px;
    color: var(--accent-emerald);
}

.geo-zigzag-right {
    top: 45px;
    right: -52px;
    color: var(--accent-primary);
}

/* Playful geometry strip on the far right */
.geo-decor-strip {
    position: absolute;
    right: -32px;
    bottom: 22%;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 2;
    opacity: 0.75;
}

.geo-shape-box {
    width: 11px;
    height: 11px;
    border: 2px solid var(--accent-primary);
}

.geo-shape-diamond {
    width: 9px;
    height: 9px;
    background-color: var(--accent-primary);
    transform: rotate(45deg);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .creative-hero {
        padding: 3.5rem 0 4.5rem 0;
        overflow: hidden;
    }
    .creative-hero-grid {
        grid-template-columns: 1fr;
        gap: 2.5rem;
    }
    .creative-visual-stage {
        order: -1;
        padding: 1rem 0;
        overflow: hidden;
    }
    .portrait-frame-wrapper {
        max-width: 320px;
    }
    .hero-editorial-lead {
        max-width: 100%;
    }
    .geo-zigzag-left, .geo-zigzag-right, .geo-decor-strip {
        display: none;
    }
    .geo-dot-grid-top {
        left: -12px;
    }
    .geo-dot-grid-bottom {
        right: -12px;
    }
}

@media (max-width: 576px) {
    .hero-name-headline {
        font-size: clamp(2.75rem, 11vw, 3.4rem);
    }
    .hero-feature-columns {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    .portrait-frame-wrapper {
        max-width: 270px;
    }
    .portrait-wireframe-border {
        top: 14px;
        left: 14px;
        right: -14px;
        bottom: -14px;
    }
    .hero-editorial-content {
        gap: 1.25rem;
    }
    .hero-actions-group {
        flex-direction: column;
        align-items: stretch;
    }
    .hero-actions-group .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Hero Section (Editorial Creative Layout) -->
<section class="creative-hero" id="hero">
    <div class="container">
        <div class="creative-hero-grid">
            <!-- Left Column: Giant Name & Editorial Content -->
            <div class="hero-editorial-content">
                <div class="hero-status-pill">
                    <span class="status-dot"></span>
                    <span><?= e($availStatus) ?></span>
                </div>

                <div class="hero-heading-group">
                    <h1 class="hero-name-headline">
                        <?php if (!empty($secondName)): ?>
                            <?= e($firstName) ?><br>
                            <span class="hero-surname-gradient"><?= e($secondName) ?><span class="dot-accent">.</span></span>
                        <?php else: ?>
                            <span class="hero-surname-gradient"><?= e($firstName) ?><span class="dot-accent">.</span></span>
                        <?php endif; ?>
                    </h1>
                    <div class="hero-role-bar">
                        <span class="hero-role-highlight"><?= e($profTitle) ?></span>
                    </div>
                </div>

                <p class="hero-editorial-lead">
                    <?= e($tagline) ?>
                </p>

                <!-- Dual Feature Blurbs (Dynamically pulled from Profile Bio & Career Goal in Admin) -->
                <div class="hero-feature-columns">
                    <div class="hero-feat-item">
                        <strong>About &amp; Specialization</strong>
                        <?= !empty($profile['bio']) ? e(mb_strimwidth(strip_tags($profile['bio']), 0, 160, '...')) : 'Architecting robust web applications, clean APIs, and database-driven solutions.' ?>
                    </div>
                    <div class="hero-feat-item">
                        <strong>Career Goal &amp; Vision</strong>
                        <?= !empty($profile['career_goal']) ? 'Focused on: ' . e($profile['career_goal']) . '. Dedicated to high-performance, maintainable software.' : 'Building scalable applications with intuitive user experiences.' ?>
                    </div>
                </div>

                <div class="hero-actions-group">
                    <a href="<?= url('projects.php') ?>" class="btn btn-primary btn-lg">
                        Explore Projects
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <a href="<?= url('contact.php') ?>" class="btn btn-secondary btn-lg">
                        Get In Touch
                    </a>
                    <?php if (!empty($profile['resume_file'])): ?>
                        <a href="<?= e($resumeUrl) ?>" target="_blank" class="btn btn-outline btn-lg" download>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Resume
                        </a>
                    <?php endif; ?>
                </div>

                <div class="hero-stats" style="margin-top: 0.25rem; padding-top: 1.25rem;">
                    <div class="stat-item">
                        <span class="stat-number"><?= $totalProjects ?>+</span>
                        <span class="stat-label">Featured Projects</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $totalSkills ?>+</span>
                        <span class="stat-label">Technologies &amp; Tools</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= !empty($profile['location']) ? '📍' : 'MCA' ?></span>
                        <span class="stat-label"><?= !empty($profile['location']) ? e($profile['location']) : 'Ernakulam, India' ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Visual Stage with Portrait & Geometric Accents -->
            <div class="creative-visual-stage">
                <div class="portrait-frame-wrapper">
                    <!-- Offset Wireframe Border Box -->
                    <div class="portrait-wireframe-border" aria-hidden="true"></div>

                    <!-- Dot Matrix Pattern (Top-Left) -->
                    <svg class="geo-dot-grid geo-dot-grid-top" width="128" height="80" viewBox="0 0 128 80" fill="none" aria-hidden="true">
                        <pattern id="dotPatternTop" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.8" fill="currentColor"/>
                        </pattern>
                        <rect width="128" height="80" fill="url(#dotPatternTop)"/>
                    </svg>

                    <!-- Dot Matrix Pattern (Bottom-Right) -->
                    <svg class="geo-dot-grid geo-dot-grid-bottom" width="128" height="96" viewBox="0 0 128 96" fill="none" aria-hidden="true">
                        <pattern id="dotPatternBottom" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.8" fill="currentColor"/>
                        </pattern>
                        <rect width="128" height="96" fill="url(#dotPatternBottom)"/>
                    </svg>

                    <!-- Staircase / Zigzag Vectors -->
                    <svg class="geo-zigzag geo-zigzag-left" width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                        <path d="M2 42V30H14V18H26V6H38" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <svg class="geo-zigzag geo-zigzag-right" width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                        <path d="M6 38H18V26H30V14H42" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <!-- Geometric Right Accent Column -->
                    <div class="geo-decor-strip" aria-hidden="true">
                        <div class="geo-shape-box"></div>
                        <div class="geo-shape-diamond"></div>
                        <div class="geo-shape-diamond"></div>
                        <div class="geo-shape-diamond"></div>
                        <div class="geo-shape-diamond"></div>
                    </div>

                    <!-- Clean Photo Card (Unobstructed Portrait) -->
                    <div class="portrait-photo-card">
                        <img src="<?= e($avatarSrc) ?>" alt="<?= e($heroName) ?>" class="portrait-photo-img" loading="eager">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="section" id="featured-projects" style="background-color: var(--bg-surface); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Practical Implementation</span>
            <h2 class="section-title">Featured Projects</h2>
            <p class="section-description">
                Real-world web applications built with a focus on database integrity, clean APIs, and responsive architectures.
            </p>
        </div>

        <div class="grid-2">
            <?php foreach ($featuredProjects as $project): ?>
                <?php
                    $techList = array_map('trim', explode(',', $project['technologies']));
                    // Check if project has images
                    $imgStmt = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :pid ORDER BY is_featured DESC, display_order ASC LIMIT 1");
                    $imgStmt->execute(['pid' => $project['id']]);
                    $primaryImg = $imgStmt->fetchColumn();
                    $thumbnailSrc = !empty($primaryImg) ? upload_url($primaryImg, 'projects') : asset('images/project-placeholder.svg');
                ?>
                <article class="project-card">
                    <div class="project-thumbnail-wrapper">
                        <img src="<?= e($thumbnailSrc) ?>" alt="<?= e($project['title']) ?> Screenshot" class="project-thumbnail" loading="lazy">
                        <span class="project-category-badge"><?= e($project['category']) ?></span>
                    </div>
                    <div class="project-body">
                        <h3 class="project-title">
                            <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>"><?= e($project['title']) ?></a>
                        </h3>
                        <p class="project-summary"><?= e($project['summary']) ?></p>
                        
                        <div class="project-tech-stack">
                            <?php foreach ($techList as $tech): ?>
                                <span class="tech-badge"><?= e($tech) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="project-footer">
                            <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>" class="btn btn-outline btn-sm">
                                View Details &rarr;
                            </a>
                            <div class="project-links">
                                <?php if (!empty($project['github_url'])): ?>
                                    <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="social-pill" title="GitHub Repository">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($project['live_url'])): ?>
                                    <a href="<?= e($project['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="social-pill" title="Live Preview">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?= url('projects.php') ?>" class="btn btn-secondary btn-lg">
                View All Projects &rarr;
            </a>
        </div>
    </div>
</section>

<!-- Skills & Technology Overview -->
<section class="section" id="skills-overview">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Technical Competencies</span>
            <h2 class="section-title">Core Skills &amp; Technologies</h2>
            <p class="section-description">
                A solid technical foundation combining core programming languages, modern frameworks, and relational databases.
            </p>
        </div>

        <div class="skills-list" style="justify-content: center; max-width: 900px; margin: 0 auto 2.5rem auto;">
            <?php foreach ($previewSkills as $sk): ?>
                <div class="skill-pill">
                    <span><?= e($sk['name']) ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">(<?= e($sk['category']) ?>)</span>
                    <?= render_proficiency_badge($sk['proficiency']) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center;">
            <a href="<?= url('skills.php') ?>" class="btn btn-outline">
                Explore Full 25+ Skills Catalogue &rarr;
            </a>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="section-sm" style="background-color: var(--bg-surface); border-top: 1px solid var(--border-color);">
    <div class="container" style="text-align: center; max-width: 760px;">
        <h2 style="margin-bottom: 1rem;">Let's Build Something Together</h2>
        <p style="margin-bottom: 2rem;">
            Currently open to web developer opportunities, internships, and collaborative software projects.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg">Start Conversation</a>
            <a href="<?= url('about.php') ?>" class="btn btn-secondary btn-lg">Read My Story</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
