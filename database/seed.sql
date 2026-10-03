-- Devanandan CS Portfolio Website - Initial Seed Data
-- Aligned strictly with Antigravity Portfolio Build Specification

USE `devanandan_portfolio`;

-- 1. Default Admin User (Password: Admin@Devanandan2026!)
INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`) VALUES
(1, 'admin', 'admin@devanandancs.dev', '$2y$12$5T1B59A.xU7.EA2e4B0HKOkA2lA.0OpesCmZ0ClswwfNTthP879Eq')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`), `password_hash` = VALUES(`password_hash`);

-- 2. Personal Profile
INSERT INTO `profile` (
    `id`, `full_name`, `displayed_name`, `professional_title`, `tagline`, `bio`, 
    `location`, `email`, `phone`, `career_goal`, `availability_status`, `github_url`, `linkedin_url`
) VALUES (
    1,
    'Devanandan CS',
    'Devanandan CS',
    'MCA Student, Full Stack Developer',
    'MCA student & full stack developer passionate about engineering high-performance web applications, clean APIs, and practical software solutions.',
    'I am an MCA student and aspiring web developer based in Ernakulam, Kerala. Dedicated to architecting robust web applications, designing user-centered interfaces, and building reliable database-driven systems. With hands-on experience developing projects using PHP, MySQL, FastAPI, React, and Django, I focus on turning practical problems into clean, maintainable code. My ambition is to continuously grow as a professional web developer and build impactful digital experiences.',
    'Ernakulam, Kerala, India',
    'devanandancs@example.com',
    '+91 98765 43210',
    'Become a web developer',
    'Available for web developer opportunities & internships',
    'https://github.com',
    'https://linkedin.com'
) ON DUPLICATE KEY UPDATE 
    `full_name` = VALUES(`full_name`),
    `displayed_name` = VALUES(`displayed_name`),
    `professional_title` = VALUES(`professional_title`),
    `tagline` = VALUES(`tagline`),
    `bio` = VALUES(`bio`),
    `location` = VALUES(`location`),
    `career_goal` = VALUES(`career_goal`),
    `availability_status` = VALUES(`availability_status`);

-- 3. Skills (Beginner proficiency level as specified in requirements, editable in Admin)
INSERT INTO `skills` (`name`, `category`, `proficiency`, `icon`, `display_order`) VALUES
-- Programming
('C', 'Programming', 'Beginner', 'code', 1),
('C++', 'Programming', 'Beginner', 'code', 2),
('Python', 'Programming', 'Beginner', 'terminal', 3),
('Java', 'Programming', 'Beginner', 'coffee', 4),
('PHP', 'Programming', 'Beginner', 'server', 5),

-- Frontend
('HTML', 'Frontend', 'Beginner', 'layout', 6),
('CSS', 'Frontend', 'Beginner', 'palette', 7),
('JavaScript', 'Frontend', 'Beginner', 'zap', 8),
('Bootstrap', 'Frontend', 'Beginner', 'grid', 9),
('Tailwind CSS', 'Frontend', 'Beginner', 'feather', 10),
('React', 'Frontend', 'Beginner', 'atom', 11),

-- Backend
('Node.js', 'Backend', 'Beginner', 'cpu', 12),
('Laravel', 'Backend', 'Beginner', 'box', 13),
('FastAPI', 'Backend', 'Beginner', 'fast-forward', 14),
('Django', 'Backend', 'Beginner', 'layers', 15),

-- Databases
('MySQL', 'Databases', 'Beginner', 'database', 16),
('MongoDB', 'Databases', 'Beginner', 'hard-drive', 17),
('SQLite', 'Databases', 'Beginner', 'disc', 18),

-- Tools
('Git', 'Tools', 'Beginner', 'git-branch', 19),
('GitHub', 'Tools', 'Beginner', 'github', 20),
('VS Code', 'Tools', 'Beginner', 'edit-3', 21),
('Linux', 'Tools', 'Beginner', 'terminal', 22),

-- AI/ML
('Gemini', 'AI/ML', 'Beginner', 'sparkles', 23),
('OpenAI API', 'AI/ML', 'Beginner', 'cpu', 24),
('CrewAI', 'AI/ML', 'Beginner', 'users', 25)
ON DUPLICATE KEY UPDATE `proficiency` = VALUES(`proficiency`);

-- 4. Projects (Strictly factual based on PDF specs)
INSERT INTO `projects` (
    `id`, `title`, `slug`, `category`, `role`, `status`, `summary`, `description`, 
    `technologies`, `github_url`, `live_url`, `featured`, `display_order`, `seo_title`, `seo_description`
) VALUES 
(
    1,
    'MentorHub',
    'mentorhub',
    'Community Platform',
    'Full Stack Developer',
    'In Development',
    'A community-based mentoring platform connecting mentors and mentees with structured guidance, subscriptions, and interactive sessions.',
    'MentorHub is a dedicated community-driven mentoring platform designed to facilitate meaningful professional and educational mentorship. The platform incorporates multi-role architecture serving Admin, Mentor, and Mentee stakeholders. Key features include community creation and discovery, flexible subscription tiers, scheduled one-on-one and group mentorship sessions, integrated payments processing, direct messaging, user reviews and ratings, comprehensive analytical reporting, and secure OTP verification for account security and authentication.',
    'FastAPI, MongoDB, React, JavaScript, CSS',
    'https://github.com/DevanandanCS/mentorhub',
    '',
    1,
    1,
    'MentorHub - Community Mentoring Platform | Devanandan CS',
    'Explore MentorHub, a community-based mentoring platform built with FastAPI, MongoDB, and React by Devanandan CS.'
),
(
    2,
    'TurfBooking',
    'turfbooking',
    'Booking & Reservation System',
    'Backend & Database Developer',
    'Active Project',
    'A turf slot booking system for browsing athletic grounds, reserving preferred playing slots, and managing schedules.',
    'TurfBooking is a web-based sports venue management and slot reservation system built using PHP and MySQL. Designed to simplify booking sports grounds and recreational turfs, the system provides an intuitive interface for players to view available times, secure booking slots, and track reservations. For administrators, it offers comprehensive oversight of ground availability, booking approvals, and slot scheduling.',
    'PHP, MySQL, HTML, CSS, JavaScript',
    'https://github.com/DevanandanCS/turfbooking',
    '',
    1,
    2,
    'TurfBooking - Turf Slot Booking System | Devanandan CS',
    'Discover TurfBooking, a sports ground reservation and slot booking web application built with PHP and MySQL by Devanandan CS.'
)
ON DUPLICATE KEY UPDATE 
    `title` = VALUES(`title`),
    `category` = VALUES(`category`),
    `summary` = VALUES(`summary`),
    `description` = VALUES(`description`),
    `technologies` = VALUES(`technologies`);

-- 5. Education History
INSERT INTO `education` (
    `id`, `degree`, `field_of_study`, `institution`, `location`, `start_year`, `end_year`, 
    `is_current`, `grade`, `description`, `display_order`
) VALUES
(
    1,
    'Master of Computer Applications (MCA)',
    'Computer Science & Application Development',
    'APJ Abdul Kalam Technological University',
    'Ernakulam, Kerala, India',
    '2024',
    '2026',
    1,
    'Currently Pursuing',
    'Advanced coursework in software engineering, modern database systems, web application development, algorithms, and distributed computing architectures.',
    1
),
(
    2,
    'Bachelor of Computer Applications (BCA)',
    'Computer Applications',
    'Mahatma Gandhi University',
    'Kerala, India',
    '2021',
    '2024',
    0,
    'Successfully Completed',
    'Rigorous undergraduate program with practical foundation in procedural and object-oriented programming (C, C++, Java), database management systems, web fundamentals, and system design.',
    2
)
ON DUPLICATE KEY UPDATE `degree` = VALUES(`degree`);

-- 6. Experience (Sample entry clearly marked as editable)
INSERT INTO `experience` (
    `id`, `title`, `company`, `location`, `start_date`, `end_date`, `is_current`, 
    `description`, `responsibilities`, `display_order`
) VALUES
(
    1,
    'Web Development Intern / Trainee',
    'Academic & Practical Project Lab',
    'Ernakulam, Kerala, India',
    '2024',
    'Present',
    1,
    'Hands-on practical development building database-backed web applications and REST APIs using PHP, MySQL, and FastAPI.',
    'Engineered database schemas and optimized SQL queries;\nImplemented secure user authentication with password hashing and session management;\nDeveloped frontend components with clean responsive HTML, CSS, and modern JavaScript.',
    1
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 7. Certifications (Initial starter template - editable in Admin)
INSERT INTO `certifications` (
    `id`, `title`, `issuer`, `issue_date`, `expiration_date`, `credential_id`, `credential_url`, `display_order`
) VALUES
(
    1,
    'Full Stack Web Development Foundations',
    'Online Learning Platform',
    '2024',
    'No Expiration',
    'CERT-FSW-2024',
    'https://example.com/certificate',
    1
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 8. Achievements (Initial starter template - editable in Admin)
INSERT INTO `achievements` (
    `id`, `title`, `category`, `achievement_date`, `description`, `highlight_badge`, `display_order`
) VALUES
(
    1,
    'Completed BCA Degree with Distinction',
    'Academic',
    '2024',
    'Successfully graduated with high academic standing and strong programming fundamentals in Computer Applications.',
    'Academic Excellence',
    1
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 9. Services
INSERT INTO `services` (
    `id`, `title`, `short_description`, `full_description`, `deliverables`, `icon`, `is_active`, `display_order`
) VALUES
(
    1,
    'Full Stack Web Development',
    'Custom web applications engineered with PHP, MySQL, FastAPI, and modern frontend technologies.',
    'End-to-end web application development from architecture design and database modeling to backend APIs and interactive responsive user interfaces. Focused on maintainability, performance, and clean code.',
    'Custom PHP/FastAPI backend\nNormalized MySQL/MongoDB database\nResponsive modern frontend UI\nSecure authentication and CRUD',
    'globe',
    1,
    1
),
(
    2,
    'Database Architecture & SQL Optimization',
    'Relational database design, query optimization, indexing, and data modeling for MySQL and MongoDB.',
    'Structured schema design tailored for scalability and data integrity. Crafting normalized tables, foreign key constraints, efficient queries, and indexing strategies.',
    'Normalized schema design\nPerformance indexing & query tuning\nStored procedures & transactional integrity\nData migration & seeding scripts',
    'database',
    1,
    2
),
(
    3,
    'RESTful API Development & Integration',
    'Lightweight, high-speed REST APIs developed using FastAPI and PHP with JSON request/response pipelines.',
    'Building robust API endpoints for web and mobile frontends. Implementing token authentication, request validation, structured error responses, and Swagger/OpenAPI documentation.',
    'RESTful endpoint architecture\nAuthentication & authorization\nJSON schemas and validation\nThird-party API integration',
    'cpu',
    1,
    3
),
(
    4,
    'Responsive Frontend UI Engineering',
    'Sleek, accessible, and high-performance user interfaces crafted with clean HTML5, modern CSS, and JavaScript/React.',
    'Creating responsive web designs that deliver smooth experiences across mobile, tablet, and desktop screens. Prioritizing accessibility, fast loading times, and clean semantic structure.',
    'Mobile-first responsive layouts\nModern CSS without heavy dependencies\nAccessible ARIA markup\nInteractive JavaScript components',
    'layout',
    1,
    4
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 10. Social Links
INSERT INTO `social_links` (`id`, `platform`, `url`, `icon`, `display_order`, `is_active`) VALUES
(1, 'GitHub', 'https://github.com', 'github', 1, 1),
(2, 'LinkedIn', 'https://linkedin.com', 'linkedin', 2, 1),
(3, 'Email', 'mailto:devanandancs@example.com', 'mail', 3, 1)
ON DUPLICATE KEY UPDATE `platform` = VALUES(`platform`);

-- 11. Website Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Devanandan CS | Portfolio', 'general'),
('site_author', 'Devanandan CS', 'general'),
('hero_badge', 'MCA Student & Full Stack Developer', 'hero'),
('hero_heading', 'Devanandan CS', 'hero'),
('hero_tagline', 'MCA student & full stack developer passionate about building reliable web apps, robust backend APIs, and database-driven solutions.', 'hero'),
('hero_availability', 'Available for Web Developer Roles', 'hero'),
('footer_tagline', 'Crafting high-quality web applications with PHP, MySQL, FastAPI & modern frontend.', 'footer'),
('footer_copyright', '© 2026 Devanandan CS. All rights reserved.', 'footer'),
('enable_services', '1', 'sections'),
('enable_experience', '1', 'sections'),
('enable_certifications', '1', 'sections'),
('enable_achievements', '1', 'sections'),
('primary_color', '#3b82f6', 'theme'),
('theme_mode', 'system', 'theme')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- 12. SEO Page Metadata
INSERT INTO `seo_pages` (`page_identifier`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`) VALUES
('home', 'Devanandan CS - Full Stack Developer & MCA Student | Portfolio', 'Official portfolio of Devanandan CS, an MCA student and full stack web developer from Ernakulam, Kerala specializing in PHP, MySQL, FastAPI, React, and modern web development.', 'Devanandan CS, full stack developer, MCA student, PHP developer, FastAPI, MySQL, React, Kerala developer, web developer portfolio', '/index.php'),
('about', 'About Devanandan CS - Background, Career Goals & Technical Profile', 'Learn more about Devanandan CS, an MCA student and aspiring web developer from Ernakulam, Kerala with a passion for web engineering and practical problem solving.', 'about Devanandan CS, developer bio, MCA student profile, web developer Kerala', '/about.php'),
('skills', 'Skills & Technical Expertise - Devanandan CS', 'Comprehensive overview of programming languages, frontend tools, backend frameworks, databases, and AI technologies used by Devanandan CS.', 'developer skills, PHP, MySQL, FastAPI, React, Python, web technologies', '/skills.php'),
('projects', 'Projects Portfolio - Web Applications by Devanandan CS', 'Explore featured web development projects built by Devanandan CS, including MentorHub and TurfBooking.', 'Devanandan CS projects, MentorHub, TurfBooking, web development projects, PHP MySQL portfolio', '/projects.php'),
('education', 'Academic Education & Background - Devanandan CS', 'Educational journey of Devanandan CS, including MCA studies and BCA graduation in Computer Applications.', 'Devanandan CS education, MCA student, BCA graduate, academic history', '/education.php'),
('experience', 'Experience & Internships - Devanandan CS', 'Professional experience, technical internships, and hands-on project work completed by Devanandan CS.', 'Devanandan CS experience, developer internship, practical experience', '/experience.php'),
('certifications', 'Certifications & Credentials - Devanandan CS', 'Verified technical credentials, course completions, and certifications earned by Devanandan CS.', 'Devanandan CS certifications, credentials, full stack certificate', '/certifications.php'),
('achievements', 'Achievements & Highlights - Devanandan CS', 'Notable milestones, academic achievements, and coding highlights of Devanandan CS.', 'Devanandan CS achievements, milestones, academic honors', '/achievements.php'),
('services', 'Services & Technical Solutions - Devanandan CS', 'Professional web development services offered by Devanandan CS including full stack web apps, database design, and REST APIs.', 'web development services, PHP developer hire, freelance web developer, API development', '/services.php'),
('contact', 'Contact Devanandan CS - Get in Touch for Opportunities', 'Contact Devanandan CS for web development projects, internships, collaborations, or inquiries. Based in Ernakulam, Kerala.', 'contact Devanandan CS, hire web developer, developer email, Ernakulam Kerala developer', '/contact.php')
ON DUPLICATE KEY UPDATE 
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`);
