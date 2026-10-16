<?php
/**
 * Configuration & Database Connection
 * HSN SRHAN Portfolio & Strategic Platform
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'u123456789_portfolio');
define('DB_PASS', 'YourSecretPasswordKey123!');
define('DB_NAME', 'u123456789_portfolio_db');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Fallback to SQLite local file for seamless local preview/testing if MySQL is not setup yet
            $sqlitePath = __DIR__ . '/portfolio.sqlite';
            try {
                $pdo = new PDO("sqlite:" . $sqlitePath);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                initializeSQLiteTables($pdo);
            } catch (Exception $ex) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }
    return $pdo;
}

function initializeSQLiteTables($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            id INTEGER PRIMARY KEY DEFAULT 1,
            site_title_ar TEXT,
            site_title_en TEXT,
            hero_badge_ar TEXT,
            hero_badge_en TEXT,
            hero_name_ar TEXT,
            hero_name_en TEXT,
            hero_subtitle_ar TEXT,
            hero_subtitle_en TEXT,
            hero_video_url TEXT,
            kpi_exp TEXT,
            kpi_brands TEXT,
            kpi_certs TEXT,
            kpi_roas TEXT,
            current_role_badge_ar TEXT,
            current_role_badge_en TEXT,
            current_role_title_ar TEXT,
            current_role_title_en TEXT,
            current_role_desc_ar TEXT,
            current_role_desc_en TEXT,
            philosophy_quote_ar TEXT,
            philosophy_quote_en TEXT,
            philosophy_desc_ar TEXT,
            philosophy_desc_en TEXT,
            emergency_phone TEXT,
            emergency_whatsapp TEXT,
            emergency_title_ar TEXT,
            emergency_title_en TEXT,
            script_header TEXT,
            script_body TEXT,
            courses_page_enabled INTEGER DEFAULT 1,
            max_daily_bookings INTEGER DEFAULT 3
        );

        INSERT OR IGNORE INTO site_settings (
            id, site_title_ar, site_title_en,
            hero_badge_ar, hero_badge_en,
            hero_name_ar, hero_name_en,
            hero_subtitle_ar, hero_subtitle_en,
            hero_video_url,
            kpi_exp, kpi_brands, kpi_certs, kpi_roas,
            current_role_badge_ar, current_role_badge_en,
            current_role_title_ar, current_role_title_en,
            current_role_desc_ar, current_role_desc_en,
            philosophy_quote_ar, philosophy_quote_en,
            philosophy_desc_ar, philosophy_desc_en,
            emergency_phone, emergency_whatsapp,
            emergency_title_ar, emergency_title_en,
            courses_page_enabled, max_daily_bookings
        ) VALUES (
            1,
            'حسن سرحان | مدير تسويق استراتيجي', 'HASSAN SARHAN | Marketing Director',
            '💼 مدير تسويق استراتيجي خبرة +8 سنوات', '💼 Strategic Marketing Director (+8 Yrs Exp)',
            'حسن سرحان', 'HASSAN SARHAN',
            'قيادة استراتيجيات النمو والتسويق الرقمي لـ +50 علامة تجارية عالمية ومحلية في الخليج وأوروبا وأمريكا.',
            'Leading growth & digital performance for 50+ global & regional brands across GCC, US & Europe.',
            'assets/videos/hero_video.mp4',
            '+8 سنوات', '+50 براند', '18 شهادة', '+7M SAR',
            '📌 المنصب القيادي الحالي', '📌 Current Executive Role',
            'شركة فلوكس آدز للتسويق', 'FLUX Ads Agency',
            'قيادة عمليات التسويق والنمو لأكثر من 30 عميلاً في السوق السعودي، وإدارة 4 أقسام رئيسية: التواصل الاجتماعي، المحتوى الإبداعي والإنتاج، الحملات الخارجية، وتطوير البرمجيات.',
            'Leading marketing & growth operations for 30+ Saudi clients, managing 4 core divisions: Social Media, Creative Content & Production, Outdoor Ads, and Software Development.',
            '\"مهما تكن الظلمة من حولك، استمتع بالرحلة\"', '\"Whatever darkness follows you, enjoy the journey\"',
            'المزج بين الفهم العميق للاتصال الإنساني والرؤية التحليلية المبنية على البيانات لتحقيق نمو مستدام وقيمة حقيقية للشركات.',
            'Combining human-centered media insight with data-driven analytics to build sustainable growth and long-term business value.',
            '+966506267542', '+966506267542',
            'للطوارئ والظروف العاجلة فقط:', 'For Urgent Matters Only:',
            1, 3
        );

        CREATE TABLE IF NOT EXISTS qualifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            issuer_ar TEXT,
            issuer_en TEXT,
            description_ar TEXT,
            description_en TEXT,
            image_url TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS career_timeline (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            company_ar TEXT,
            company_en TEXT,
            period_ar TEXT,
            period_en TEXT,
            location_ar TEXT,
            location_en TEXT,
            description_ar TEXT,
            description_en TEXT,
            website_url TEXT,
            case_study_url TEXT,
            logo_url TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            slug TEXT UNIQUE,
            client_name TEXT,
            client_logo_url TEXT,
            sector TEXT,
            problem_text_ar TEXT,
            strategy_text_ar TEXT,
            results_json TEXT,
            priority INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            badge_ar TEXT,
            badge_en TEXT,
            subtitle_ar TEXT,
            subtitle_en TEXT,
            features_json TEXT,
            price_text_ar TEXT,
            price_text_en TEXT,
            cta_text_ar TEXT,
            cta_text_en TEXT,
            cta_link TEXT,
            icon_class TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            badge_ar TEXT,
            badge_en TEXT,
            description_ar TEXT,
            description_en TEXT,
            duration_ar TEXT,
            duration_en TEXT,
            price_ar TEXT,
            price_en TEXT,
            features_json TEXT,
            image_url TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS digital_products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            badge_ar TEXT,
            badge_en TEXT,
            category TEXT,
            price_sar REAL,
            original_price_sar REAL,
            download_url TEXT,
            cover_image TEXT,
            description_ar TEXT,
            description_en TEXT,
            features_json TEXT,
            rating TEXT DEFAULT '4.9/5',
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS bank_accounts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            bank_name_ar TEXT,
            bank_name_en TEXT,
            account_name_ar TEXT,
            account_name_en TEXT,
            iban TEXT,
            swift TEXT,
            transfer_note_ar TEXT,
            transfer_note_en TEXT,
            is_active INTEGER DEFAULT 1
        );

        INSERT OR IGNORE INTO bank_accounts (id, bank_name_ar, bank_name_en, account_name_ar, account_name_en, iban, swift, transfer_note_ar, transfer_note_en, is_active)
        VALUES (1, 'D360 بنك', 'D360 Bank', 'حسن فايز حسن سرحان', 'Hassan Fayez Hassan Sarhan', 'SA6936036036047948932598', 'DBAKSARIXXX', 'يرجى اختيار الغرض من التحويل صديق', 'Please select transfer purpose as Friend', 1);

        CREATE TABLE IF NOT EXISTS bank_orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT UNIQUE,
            customer_name TEXT,
            customer_email TEXT,
            customer_phone TEXT,
            product_id INTEGER,
            product_title TEXT,
            amount_sar REAL,
            receipt_url TEXT,
            status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            slug TEXT UNIQUE,
            content_ar TEXT,
            content_en TEXT,
            linkedin_url TEXT,
            og_title TEXT,
            og_description TEXT,
            og_image_url TEXT,
            priority INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS youtube_shorts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            youtube_video_id TEXT,
            views_count TEXT,
            likes_count TEXT,
            priority INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT,
            email TEXT,
            phone TEXT,
            company_name TEXT,
            notes TEXT,
            booking_date TEXT,
            time_slot TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS landing_pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT,
            title_en TEXT,
            slug TEXT UNIQUE,
            meta_description_ar TEXT,
            meta_description_en TEXT,
            blocks_json TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            email TEXT UNIQUE,
            password_hash TEXT,
            role TEXT DEFAULT 'seo',
            status TEXT DEFAULT 'active'
        );
    ");

    // Insert initial default seed users if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $seoPass = password_hash('seo123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password_hash, role, status) VALUES 
            ('admin', 'hsn96srhan@gmail.com', '$adminPass', 'admin', 'active'),
            ('seouser', 'seo@hsn.agency', '$seoPass', 'seo', 'active')
        ");
    }

    // Seed Qualifications (All 18 Certificates)
    $stmtQual = $pdo->query("SELECT COUNT(*) FROM qualifications");
    if ($stmtQual->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO qualifications (title_ar, title_en, issuer_ar, issuer_en, description_ar, description_en, image_url, sort_order) VALUES
        ('بكالوريوس الصحافة والإعلام', 'Bachelor Degree in Journalism & Mass Comm', 'أكاديمية أخبار اليوم', 'Akhbar El Yom Academy', 'المؤهل الأكاديمي الرئيسي والتأسيسي في الصحافة والإعلام.', 'Primary academic degree in Journalism and Mass Communication.', 'assets/images/image66.jpg', 1),
        ('الاعتماد المهني كمدير تسويق', 'Professional Accreditation as Marketing Manager', 'وزارة الموارد البشرية (السعودية)', 'HRSD Ministry (Saudi Arabia)', 'اعتماد حكومي رسمي يثبت الكفاءة المهنية لقيادة وتطوير الاستراتيجيات التسويقية بالسوق السعودي.', 'Official Saudi government accreditation confirming professional competence.', 'assets/images/image65.jpg', 2),
        ('مدخل التسويق الرقمي', 'Introduction to Digital Marketing', 'ميتا', 'Meta / Coursera', 'أسس استهداف الجماهير، بناء الهوية الرقمية، وتصميم استراتيجيات التوسع.', 'Core principles of audience targeting and digital expansion strategies.', 'assets/images/image56.jpg', 3),
        ('إدارة المنصات الرقمية', 'Social Media Management', 'ميتا', 'Meta / Coursera', 'إدارة وتوجيه الحسابات متعددة المنصات والتخطيط للمحتوى الإبداعي.', 'Multi-platform corporate management and performance tracking.', 'assets/images/image59.jpg', 4),
        ('أساسيات الإعلانات المدفوعة', 'Paid Advertising Fundamentals', 'ميتا', 'Meta / Coursera', 'بناء الحملات الموجهة، تقسيم شرائح الجماهير، وإدارة ميزانيات النشر.', 'Targeted campaign structuring and custom audience segmentation.', 'assets/images/image64.jpg', 5),
        ('احتراف إعلانات ميتا', 'Advanced Meta Advertising', 'ميتا', 'Meta / Coursera', 'تخصيص الميزانيات الكبيرة وتطوير الرسائل الإعلانية الإبداعية.', 'Large-scale budget management and conversion scaling.', 'assets/images/image57.jpg', 6),
        ('قياس وتحسين الأداء الإعلاني', 'Measuring & Optimizing Campaigns', 'ميتا', 'Meta / Coursera', 'تحليل بيانات الحملات، تحسين العائد على الإنفاق الإعلاني، وتقليل تكلفة الاكتساب.', 'Data analytics, ROAS optimization, and lowering CPA.', 'assets/images/image60.jpg', 7),
        ('مسار التحدي بالتسويق الرقمي', 'Digital Marketing Challenger Track', 'وزارة الاتصالات & Udacity', 'MCIT & Udacity', 'مبادرة حكومية متقدمة لتطبيق أحدث منهجيات النمو الرقمي.', 'Advanced government initiative applying modern digital growth frameworks.', 'assets/images/image40.png', 8),
        ('تسويق شبكات التواصل المحترف', 'Social Media Marketing Professional', 'Hootsuite & LinkedIn', 'Hootsuite & LinkedIn', 'إستراتيجية التسويق الاحترافي عبر منصات التواصل الاجتماعي.', 'Professional social media strategy and multi-platform management.', 'assets/images/image43.jpg', 9),
        ('استراتيجية التسويق عبر المنصات', 'Social Media Marketing Strategy', 'HubSpot Academy', 'HubSpot Academy', 'تطوير مسارات التحويل وتوجيه العملاء المحتملين.', 'Developing buyer personas and lead generation funnels.', 'assets/images/image46.png', 10),
        ('إدارة الحملات الإعلانية المتقدمة', 'Advanced Campaign Management', 'Google Digital Garage', 'Google Digital Garage', 'تخطيط وتنفيذ حملات محركات البحث والإعلانات المنبثقة.', 'Search engine marketing and programmatic advertising execution.', 'assets/images/image48.png', 11),
        ('التسويق بالمحتوى والسرد القصصي', 'Content Marketing & Storytelling', 'HubSpot Academy', 'HubSpot Academy', 'صياغة المحتوى الجاذب والسرد القصصي للعلامات التجارية.', 'Crafting compelling brand stories and inbound content funnels.', 'assets/images/image50.png', 12),
        ('تحليلات البيانات التسويقية GA4', 'Google Analytics 4 Certification', 'Google Skillshop', 'Google Skillshop', 'تتبع سلوك المستخدمين على المتاجر وتحديد نقاط الانقطاع.', 'Tracking user behavior, funnels, and event-based analytics.', 'assets/images/image52.jpg', 13),
        ('تحسين محركات البحث SEO', 'Search Engine Optimization', 'SEMrush Academy', 'SEMrush Academy', 'تصدر نتائج البحث الأولى وبناء الروابط الخلفية.', 'Search engine rankings optimization and technical SEO audit.', 'assets/images/image54.png', 14),
        ('إعلانات التيك توك للمتاجر', 'TikTok Ads E-Commerce Scaling', 'TikTok Academy', 'TikTok Academy', 'صناعة الإعلانات الفيروسية وقصيرة المدى للجيل الجديد.', 'Creating viral short-form ads and scaling e-commerce sales on TikTok.', 'assets/images/image58.jpg', 15),
        ('إعلانات سناب شات المتقدمة', 'Snapchat Performance Ads Mastery', 'Snap Focus Academy', 'Snap Focus Academy', 'استهداف فئات الشباب والجمهور الخليجي بفعالية عالية.', 'GCC audience targeting and AR lens ad performance scaling.', 'assets/images/image61.png', 16),
        ('إدارة أوتوميشن التسويق', 'Marketing Automation Mastery', 'ActiveCampaign', 'ActiveCampaign', 'أتمتة البريد الإلكتروني والواتساب لاستعادة السلات المتروكة.', 'Email and WhatsApp CRM workflow automation for retention.', 'assets/images/image62.jpg', 17),
        ('قيادة الفرق التسويقية', 'Marketing Leadership & Team Direction', 'LinkedIn Learning', 'LinkedIn Learning', 'إدارة وتوجيه فرق العمل متعددة التخصصات.', 'Managing cross-functional marketing teams and campaign operations.', 'assets/images/image63.jpg', 18)
        ");
    }

    // Seed Career Timeline
    $stmtCareer = $pdo->query("SELECT COUNT(*) FROM career_timeline");
    if ($stmtCareer->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO career_timeline (title_ar, title_en, company_ar, company_en, period_ar, period_en, location_ar, location_en, description_ar, description_en, website_url, case_study_url, logo_url, sort_order) VALUES
        ('مدير التسويق', 'Marketing Director', 'شركة فلوكس آدز للتسويق', 'FLUX Ads Agency', '2025 - الحالي', '2025 - Present', 'الرياض، المملكة العربية السعودية', 'Riyadh, Saudi Arabia', 'قيادة الاستراتيجيات التسويقية والنمو لأكثر من 30 عميلاً بالسوق السعودي، والإشراف المباشر على قيادة 4 قطاعات رئيسية بالوكالة.', 'Leading overall marketing strategies for 30+ enterprise clients across Saudi Arabia, managing 4 core divisions.', 'https://fluxads.sa', 'portfolio.php#flux', 'assets/images/fluxads.png', 1),
        ('مدير التسويق الرقمي والأداء', 'Digital Marketing Manager', 'شركة راكيتبي للتسويق', 'RAKETBE Agency', '2025', '2025', 'الرياض، المملكة العربية السعودية', 'Riyadh, Saudi Arabia', 'إخراج وإدارة الحملات الإعلانية المدفوعة للشركات الطبية والتجارية بالسعودية، إدارة الميزانيات الضخمة وتحقيق عائد إنفاق إعلاني من 5x حتى 25x.', 'Directed performance marketing campaigns for Saudi healthcare & commercial clients achieving ROAS from 5x to 25x.', 'https://raketbeagency.com', 'portfolio.php#raketbe', 'assets/images/raketbe.png', 2),
        ('مدير التسويق', 'Marketing Manager', 'مجموعة أرابيفا', 'Arabiva Group', '2025', '2025', 'الكويت والخليج وأوروبا وأمريكا', 'Kuwait, GCC, EU & US', 'قيادة الاستراتيجيات التسويقية لـ 3 علامات تجارية عالمية للعطور عبر أسواق الكويت والخليج وأوروبا وأمريكا.', 'Led international marketing strategy for 3 distinct luxury fragrance brands across Kuwait, GCC, Europe, and US markets.', 'https://arabiva.com', 'portfolio.php#arabiva', 'assets/images/image17.jpg', 3),
        ('استشاري ومسؤول التسويق الرقمي', 'Digital Marketing Consultant', 'عيادات الكاف للعيون', 'Alkaff Eye Clinics', '2024 - 2025', '2024 - 2025', 'المملكة العربية السعودية', 'Saudi Arabia', 'تصميم وإدارة الحملات الإعلانية المركزة لعمليات العيون وتخفيض تكلفة العميل المحتمل بنسبة 40%.', 'Designed and executed high-precision Snapchat & Meta lead generation campaigns for specialized eye surgeries.', 'https://alkaffclinics.com', 'portfolio.php#alkaff', 'assets/images/image25.jpg', 4),
        ('مدير التسويق والتجارة الرقمية', 'E-Commerce Marketing Director', 'شركة فانتازيا للمراتب', 'Fantazzia Bed', '2021 - 2024', '2021 - 2024', 'مصر والخليج', 'Egypt & GCC', 'تأسيس البنية التحتية للتجارة الرقمية والمبيعات من الصفر ومضاعفة المبيعات لتصل إلى 10 مليون شهرياً.', 'Built e-commerce infrastructure from zero and scaled total monthly revenue to 10M EGP/SAR via integrated digital channels.', 'https://fantazzia.com', 'portfolio.php#fantazzia', 'assets/images/image31.png', 5)
        ");
    }

    // Seed Digital Products
    $stmtProducts = $pdo->query("SELECT COUNT(*) FROM digital_products");
    if ($stmtProducts->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO digital_products (title_ar, title_en, badge_ar, badge_en, category, price_sar, original_price_sar, download_url, cover_image, description_ar, description_en, features_json, sort_order) VALUES
        ('دليل الحملات الإعلانية', 'Performance Ads Playbook', '🥇 الأكثر مبيعاً', '🥇 Bestseller Playbook', 'playbook', 199.00, 499.00, 'assets/files/playbook.pdf', 'assets/images/image40.png', 'دليل هيكلة الحملات الإعلانية المدفوعة بأعلى عائد ROAS عبر سناب شات، ميتا، تيك توك وجوجل.', 'The ultimate guide to structuring high-ROAS paid campaigns across Snapchat, Meta, TikTok, and Google Ads.', '[\"سكريبتات إعلانية جاهزة للتطبيق المباشر\", \"هيكل صفحة الهبوط المثالية لزيادة التحويل\", \"حاسبة الـ ROAS ونقاط التعادل الإعلانية\", \"تحديثات مجانية مستمرة مدى الحياة\"]', 1),
        ('حقيبة المتاجر الإلكترونية', 'E-Commerce Growth Masterkit', '🔥 حزمة المتاجر', '🔥 Complete Bundle', 'masterkit', 299.00, 799.00, 'assets/files/ecom-masterkit.pdf', 'assets/images/image31.png', 'نظام متكامل لمضاعفة مبيعات المتجر الإلكتروني واستعادة السلات المتروكة ورسائل الواتساب الفورية.', 'Complete system for scaling e-commerce monthly sales and recovering abandoned carts.', '[\"مسارات أتمتة الواتساب المباشرة\", \"20 نموذجاً لاستعادة السلات المتروكة\", \"خريطة طريق التوسع في أسواق الخليج\", \"دليل تسعير المنتجات والعروض الترويجية\"]', 2),
        ('دليل تحسين معدل التحويل CRO', 'E-Commerce CRO Playbook', '⚡️ استراتيجية نمو', '⚡️ Growth Strategy', 'cro', 149.00, 399.00, 'assets/files/cro-guide.pdf', 'assets/images/image43.jpg', 'خطط مجربة لرفع معدل تحويل متجرك بنسبة تزيد عن 50% بدون زيادة الميزانية الإعلانية.', 'Proven frameworks to increase e-commerce conversion rates by over 50% without increasing ad spend.', '[\"قائمة مراجعة 50 نقطة لفحص تجربة المستخدم\", \"نماذج اختبارات A/B الناجحة\", \"تحليل سلوك الزائرين ونقاط الخروج\", \"أسرار الدفع عند الاستلام وبوابات الدفع\"]', 3)
        ");
    }

    // Seed Services
    $stmtServ = $pdo->query("SELECT COUNT(*) FROM services");
    if ($stmtServ->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO services (title_ar, title_en, badge_ar, badge_en, subtitle_ar, subtitle_en, features_json, price_text_ar, price_text_en, cta_text_ar, cta_text_en, cta_link, icon_class, sort_order) VALUES
        ('إدارة الإعلانات المدفوعة والأداء', 'Paid Advertising & Performance Marketing', '🎯 خدمة رئيسية', '🎯 Core Service', 'إدارة الميزانيات الضخمة وتصميم حملات عالية التحويل عبر سناب شات، ميتا، تيك توك وجوجل.', 'Managing high-scale ad budgets and executing high-ROAS campaigns on Snapchat, Meta, TikTok & Google.', '[\"إدارة ميزانيات حتى +1M SAR\", \"تحسين العائد على الإعلانات (ROAS) حتى 25x\", \"استهداف دقيق لأسواق الخليج العربي\", \"تقارير أسبوعية وتحليل دقيق للبيانات\"]', 'حسب الميزانية', 'Custom Quote', '📅 احجز جلسة استشارية', '📅 Book Strategy Session', 'contact.php', 'fas fa-chart-line', 1),
        ('بناء وتطوير الاستراتيجيات التسويقية', 'Marketing Strategy & Brand Scaling', '👑 استشارية', '👑 Consulting', 'صياغة الرؤية الشاملة وخطة النمو السنوية وتحديد التموضع التنافسي للشركات.', 'Crafting end-to-end growth strategies, annual marketing plans, and market positioning.', '[\"تحليل المنافسين والسوق المباشر\", \"بناء المسارات التسويقية Full Funnel\", \"تحديد شرائح الجماهير ونقاط الأوراق الرابحة\", \"خطة تنفيذية زُمنية واضحة 12 شهراً\"]', 'حسب نطاق المشروع', 'Project Based', '📅 احجز جلسة استشارية', '📅 Book Strategy Session', 'contact.php', 'fas fa-chess', 2),
        ('تحسين معدلات التحويل CRO', 'Conversion Rate Optimization (CRO)', '⚡️ زيادة المبيعات', '⚡️ Revenue Scaling', 'تحليل واجهات المتاجر وصفحات الهبوط واختبار سلوك المستخدمين لرفع المبيعات.', 'Analyzing store UX, landing page funnels, and user friction points to maximize order volume.', '[\"اختبارات A/B واختبار الرسائل\", \"تحسين تجربة السلة والدفع الشاملة\", \"أتمتة استعادة السلات المتروكة عبر الواتساب\", \"تقليل تكلفة الاكتساب للعميل (CPA)\"]', 'حسب نطاق المشروع', 'Project Based', '📅 احجز جلسة استشارية', '📅 Book Strategy Session', 'contact.php', 'fas fa-bolt', 3)
        ");
    }

    // Seed Courses
    $stmtCourses = $pdo->query("SELECT COUNT(*) FROM courses");
    if ($stmtCourses->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO courses (title_ar, title_en, badge_ar, badge_en, description_ar, description_en, duration_ar, duration_en, price_ar, price_en, features_json, image_url, sort_order) VALUES
        ('ماستر كلاس قيادة التسويق الرقمي', 'Digital Marketing Leadership Masterclass', '🏆 برنامج قيادي', '🏆 Leadership Program', 'برنامج مكثف مخصص لمدراء التسويق ورجال الأعمال لنقل خبرة 8 سنوات ميدانية.', 'Intensive program for marketing managers & business owners transferring 8+ years of real field experience.', '4 أسابيع (16 ساعة)', '4 Weeks (16 Hrs)', '1,499 ﷼', '1,499 SAR', '[\"جلسات تفاعلية مباشرة عبر Zoom\", \"تطبيقات عملية على ميزانيات وحملات حقيقية\", \"نماذج وقوالب عمل جاهزة للاستخدام\", \"شهادة إتمام معتمدة وحضور مخصص\"]', 'assets/images/image40.png', 1),
        ('ورشة احتراف الإعلانات المدفوعة في الخليج', 'GCC Paid Ads Performance Workshop', '🔥 ورشة عمل', '🔥 Practical Workshop', 'تكتيكات الوصول للجمهور الخليجي ورسم مسارات التحويل العالية عبر سناب وتيك توك.', 'Practical tactics to reach GCC audiences and scale Snapchat & TikTok ad performance.', 'يومان (6 ساعات)', '2 Days (6 Hrs)', '799 ﷼', '799 SAR', '[\"تحليل حالات دراسية واقعية بالسعودية والإمارات\", \"إعداد الحملات خطوة بخطوة مباشر\", \"استراتيجيات استهداف الفئات الشرائية العليا\", \"دليل حل مشاكل الحسابات الإعلانية\"]', 'assets/images/image43.jpg', 2)
        ");
    }

    // Seed Projects (All 13 Case Studies)
    $stmtPrj = $pdo->query("SELECT COUNT(*) FROM projects");
    if ($stmtPrj->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO projects (title_ar, title_en, slug, client_name, sector, problem_text_ar, strategy_text_ar, results_json, priority) VALUES 
        ('حملة تعزيز الوعي والتوسع لعطور أرابيفا', 'Arabiva Fragrances Expansion', 'arabiva-fragrances', 'عطور أرابيفا (Arabiva)', 'e-commerce', 'التوسع والدخول في 4 أسوق إقليمية جديدة خلال 6 أشهر وزيادة مبيعات المتجر الإلكتروني.', 'توطين المتجر الإلكتروني لأسواق الخليج، توفير بوابات دفع بالعملات المحلية، وشراكات شحن وتوصيل فائق السرعة وإدارة حملات مؤثرين.', '{\"ROAS\": \"25x\", \"Growth\": \"+65% YoY\", \"AOV\": \"+22%\", \"Reach\": \"+2M Views\"}', 13),
        ('إعلانات سناب شات فائقة الدقة لعيادات الكاف للعيون', 'Alkaff Clinics Snapchat Ads', 'alkaff-clinics', 'عيادات الكاف للعيون', 'medical', 'استهداف المرضى المحتملين للعمليات الدقيقة وتقليل تكلفة العميل المحتمل.', 'استهداف مكاني فائق الدقة، استعراض سريع لإجراءات العيون، عدسات سناب شات تفاعلية وأسئلة وأجوبة مع الأطباء.', '{\"Spend\": \"$2,373\", \"Leads\": \"106 Qualified Leads\", \"LeadCost\": \"$22.39\"}', 12),
        ('بناء Funnel أوتوماتيكي لشركة فلكس للشحن السريع', 'Vlex Shipping Funnel Automation', 'vlex-shipping', 'شركة فلكس للشحن (Vlex)', 'b2b_logistics', 'تخفيض زمن الاستجابة وتوليد عملاء B2B للمتاجر الإلكترونية في القاهرة والجيزة.', 'ربط Google Lead Gen بـ WhatsApp CRM وأتمتة الردود والمتابعات التسويقية الفورية.', '{\"Leads\": \"+1,000 B2B Leads\", \"ResponseTime\": \"-60%\", \"Funnel\": \"Automated\"}', 11),
        ('ترسيخ الاسم والوعي بالعيادة السيف كلينك', 'Alsaif Clinic Brand Awareness', 'alsaif-clinic', 'السيف كلينك (السعودية)', 'medical', 'تعزيز الوعي بالعلامة التجارية وتثبيت اسم العيادة بالسوق السعودي.', 'بناء ركائز محتوى بقيادة الأطباء، إبراز نتائج المرضى الواقعية، السرد القصصي، والحفاظ على هوية بصرية موحدة.', '{\"Brand\": \"Trusted\", \"Name\": \"Remembered\", \"Engagement\": \"+85%\"}', 10),
        ('إدارة السوشيال ميديا وحجوزات عيادات كوديرما', 'Coderma Clinic Management', 'coderma-clinic', 'عيادات كوديرما (التجميل)', 'medical', 'إدارة شاملة لمنصات التواصل المباشر وحجز المواعيد بالعيادة.', 'نشر تجارب الواقع (Before/After)، نصائح سريعة من الخبراء، إعلانات موسمية مستهدفة جغرافياً، وبناء تفاعل يومي.', '{\"Clinics\": \"Fully Booked\", \"Online Buzz\": \"Active\", \"Bookings\": \"+60%\"}', 9),
        ('بناء مسار تحويل متكامل للدكتورة داليا إسماعيل', 'Dr. Dalia Ismail Full Funnel', 'dr-dalia-ismail', 'د. داليا إسماعيل (تجميل)', 'medical', 'بناء مسار تسويقي متكامل (Full Funnel) وتوليد حجوزات مستمرة.', 'تصميم صفحات هبوط مخصصة للعروض، ربط الاستمارات بالنظام وواتساب، وإعادة استهداف المهتمين بالمحتوى.', '{\"Funnel\": \"Full Funnel Flow\", \"Conversions\": \"High CR\", \"Leads\": \"+450 Leads\"}', 8),
        ('بناء العلامة الشخصية للدكتورة مها المعاري', 'Dr. Maha Al Maari Personal Brand', 'dr-maha-al-maari', 'د. مها المعاري (أطفال)', 'medical', 'بناء علامة شخصية موثوقة (Personal Branding) ونشر الوعي الصحي للأمهات.', 'حل مشكلات الأمهات بصيغ تريند هادفة وسرد قصصي حقيقي وتنظيم جدول النشر للوصول المباشر.', '{\"Views\": \"1M+ Views\", \"Authority\": \"Top Expert\", \"Engagement\": \"+95%\"}', 7),
        ('تحديات التفاعل الوطنية لفرش أسنان usmile', 'usmile Saudi National Challenge', 'usmile-saudi', 'usmile السعودية', 'medical', 'تحديات تفاعل ومحتوى متزامن مع المناسبات الوطنية في السعودية.', 'التعاون مع Nano-Influencers، تشجيع المحتوى المنشأ من المستخدمين (UGC)، ومسابقات واستطلاعات يومية.', '{\"National Buzz\": \"Saudi Challenge\", \"Smiles\": \"Engaged\", \"UGC\": \"+1.2K Posts\"}', 6),
        ('بناء مجتمع تقويم الأسنان لـ G.U.M السعودية', 'GUM Ortho Community', 'gum-saudi', 'G.U.M صحة الفم', 'medical', 'بناء مجتمع مخصص لمرتدي تقويم الأسنان وتكريم وتوزيع هدايا.', 'تقديم روتين العناية، نصائح خبراء معتمدة، وتوزيع هدايا لتعزيز الولاء للعلامة التجارية.', '{\"Community\": \"Braces Loyalty\", \"Repeat Customers\": \"+40%\", \"Engagement\": \"High\"}', 5),
        ('التوعية بـ بار العطور في ثارب ثارب بار', 'Tharb Perfume Bar Launch', 'tharb-perfume-bar', 'ثارب بار عطور (الكويت)', 'e-commerce', 'توعية الجمهور بمفهوم \"بار العطور\" ومزج الروائح وتفعيل المتاجر.', 'إنتاج محتوى حسي مرئي، تنظيم فعاليات تفاعلية داخل المتاجر، وشراكات مع مؤثرين متخصصين.', '{\"Scent Bar\": \"Buzz Ignited\", \"Store Traffic\": \"+75%\", \"Curiosity\": \"High\"}', 4),
        ('إطلاق المنتجات في السوق الغربي لـ جينيريك', 'Generic Perfumes Global Reach', 'generic-perfumes', 'جينيريك بيرفيومز (Kuwait/US/EU)', 'e-commerce', 'استهداف الجمهور الغربي (أوروبا وأمريكا) والمزج بين الطابع العربي والتصميم العصري.', 'السرد القصصي لعالم الروائح، إبراز موثوقية الشحن الدولي، والتعاون مع مؤثرين غربيين متخصصين.', '{\"Kuwaiti Essence\": \"Global Appeal\", \"US & EU Sales\": \"+50%\", \"International Reach\": \"High\"}', 3),
        ('تأسيس المتجر الإلكتروني ومضاعفة مبيعات فانتازيا للمراتب إلى 10 مليون شهرياً', 'Fantazzia E-Commerce & 10M Sales', 'fantazzia-mattress', 'فانتازيا للمراتب (Fantazzia Bed)', 'b2b_logistics', 'تأسيس البنية التحتية للتجارة الرقمية والمبيعات من الصفر على مدار 3 سنوات.', 'إدارة كاملة للمحتوى والتسويق الرقمي والحملات المدفوعة وأتمتة المبيعات والتنسيق مع الكول سنتر.', '{\"Store Sales\": \"5M/Mo\", \"Social & CallCenter\": \"5M/Mo\", \"Total Monthly\": \"10M/Mo\", \"ROAS\": \"10x - 20x\"}', 2),
        ('التطبيق الطبي الشامل ميديكا هيلثي', 'Medica Healthy Medical App', 'medica-healthy-app', 'ميديكا هيلثي (Zamalek)', 'b2b_logistics', 'زيادة تحميلات ونسب التفاعل لتطبيق طبي لحجز العيادات والفحوصات والصيدلية.', 'محتوى قصصي موجه، إعلانات دقيقة لعيادات الزمالك والقاهرة، وسلسلة نصائح خبراء.', '{\"App Downloads\": \"+90%\", \"Engagement\": \"+90%\", \"Trust\": \"Earned\"}', 1)
        ");
    }

    $stmtShorts = $pdo->query("SELECT COUNT(*) FROM youtube_shorts");
    if ($stmtShorts->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO youtube_shorts (title, youtube_video_id, views_count, likes_count, priority) VALUES 
        ('استراتيجية مضاعفة العائد على الإعلانات ROAS', 'dQw4w9WgXcQ', '120K+', '8.5K', 3),
        ('كيف ترسم رحلة العميل وتزيل عقبات التحويل', '3JZ_D3ELwOQ', '85K+', '5.2K', 2),
        ('أهم 3 أخطاء في إعلانات سناب شات للعيادات', 'L_LUpnjgPso', '45K+', '3.1K', 1)
        ");
    }

    $stmtArts = $pdo->query("SELECT COUNT(*) FROM articles");
    if ($stmtArts->fetchColumn() < 3) {
        $pdo->exec("INSERT OR IGNORE INTO articles (title_ar, title_en, slug, content_ar, content_en, linkedin_url, og_image_url, og_title, og_description, priority) VALUES 
        ('تحسين معدلات التحويل CRO للمتاجر', 'CRO Optimization for GCC E-Commerce', 'cro-strategies-gcc', 'تعد صياغة مسار تحويل مرن من أهم محركات النمو للتجارة الإلكترونية العابرة للحدود، في هذا المقال نستعرض كيف قمنا برفع معدل التحويل لـ 52%...', 'Structuring high-converting checkout funnels across borders to scale average basket value and overall store revenue.', 'https://linkedin.com/in/hsn96srhan', 'assets/images/image55.jpg', 'تحسين معدلات التحويل CRO للمتاجر | حسن سرحان', 'استراتيجيات مجربة لرفع معدلات التحويل وتعظيم قيمة الطلب AOV.', 3),
        ('خفض تكلفة العميل CPA بالسوق السعودي', 'Lowering CPA in Saudi Arabia', 'lowering-cpa-saudi', 'تحليل علمي لآليات خفض التكلفة مع رفع جودة العميل المحتمل عن طريق دقة استهداف الخوارزميات وتوليد الإعلانات التفاعلية.', 'Data-driven tactics to lower CPA while boosting lead quality using algorithmic precision and creative ad variation.', 'https://linkedin.com/in/hsn96srhan', 'assets/images/image56.jpg', 'خفض تكلفة العميل CPA بالسوق السعودي | حسن سرحان', 'تكتيكات قائمة على البيانات لخفض تكلفة الاستحواذ بالسوق السعودي.', 2),
        ('بناء علامة تجارية ذات أثر مستدام', 'Building Sustainable Brand Authority', 'building-brand-authority', 'خطوات استراتيجية لبناء الانطباع الذهني القوي وترسيخ قيم البراند لدى العملاء المستهدفين في المنطقة.', 'Strategic steps to construct brand positioning and entrench long-term customer loyalty across regional markets.', 'https://linkedin.com/in/hsn96srhan', 'assets/images/image60.jpg', 'بناء علامة تجارية ذات أثر مستدام | حسن سرحان', 'خطوات استراتيجية لبناء تموضع ذهني وترسيخ ولاء العملاء.', 1)
        ");
    }
}

