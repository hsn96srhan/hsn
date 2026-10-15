<?php
/**
 * Helper Functions & Internationalization (100% Strict AR / EN Separation)
 * HSN SRHAN Portfolio & Strategic Platform
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCurrentLang() {
    if (isset($_GET['lang'])) {
        $lang = strtolower($_GET['lang']);
        if (in_array($lang, ['ar', 'en'])) {
            $_SESSION['site_lang'] = $lang;
            setcookie('site_lang', $lang, time() + (86400 * 30), '/');
            return $lang;
        }
    }
    if (isset($_SESSION['site_lang']) && in_array($_SESSION['site_lang'], ['ar', 'en'])) {
        return $_SESSION['site_lang'];
    }
    if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar', 'en'])) {
        return $_COOKIE['site_lang'];
    }

    // Default to browser language if no session/cookie/query param is set
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $accept = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $enPos = strpos($accept, 'en');
        $arPos = strpos($accept, 'ar');
        if ($enPos !== false && ($arPos === false || $enPos < $arPos)) {
            $_SESSION['site_lang'] = 'en';
            setcookie('site_lang', 'en', time() + (86400 * 30), '/');
            return 'en';
        }
    }

    $_SESSION['site_lang'] = 'ar';
    setcookie('site_lang', 'ar', time() + (86400 * 30), '/');
    return 'ar';
}

$lang = getCurrentLang();
if (!defined('SITE_LANG')) define('SITE_LANG', $lang);
if (!defined('IS_RTL')) define('IS_RTL', $lang === 'ar');

// Exhaustive 100% AR / EN Translation Dictionary
$translations = [
    'ar' => [
        'site_title' => 'حسن سرحان | مدير تسويق استراتيجي',
        'nav_qualifications' => 'المؤهلات',
        'nav_career' => 'السيرة المهنية',
        'nav_services' => 'الخدمات',
        'nav_courses' => 'الكورسات',
        'nav_blog' => 'المقالات',
        'nav_contact' => 'التواصل',
        'book_free_meeting' => 'مقابلة أونلاين',
        'other_lang_label' => '🇬🇧 EN',
        'other_lang_code' => 'en',
        'rights_reserved' => 'جميع الحقوق محفوظة © حسن سرحان',
        
        // Homepage
        'hero_badge' => '💼 مدير تسويق استراتيجي خبرة +8 سنوات',
        'hero_name' => 'حسن سرحان',
        'hero_title' => 'مدير تسويق',
        'hero_subtitle' => 'قيادة استراتيجيات النمو والتسويق الرقمي لـ +50 علامة تجارية عالمية ومحلية في الخليج وأوروبا وأمريكا.',
        'btn_consultation' => '📅 مقابلة أونلاين',
        'btn_portfolio' => '📂 معرض الأعمال',
        'kpi_exp' => 'سنوات خبرة قيادية متواصلة (+8)',
        'kpi_brands' => 'علامة تجارية عالمية ومحلية (+50)',
        'kpi_certs' => 'شهادة واعتماد احترافي رسمي (18)',
        'kpi_roas' => 'ميزانية إعلانية للحملات خلال 8 سنوات',
        'current_role_badge' => '📌 المنصب القيادي الحالي',
        'current_role_title' => 'شركة فلوكس آدز للتسويق',
        'current_role_desc' => 'قيادة عمليات التسويق والنمو لأكثر من 30 عميلاً في السوق السعودي، وإدارة 4 أقسام رئيسية: التواصل الاجتماعي، المحتوى الإبداعي والإنتاج، الحملات الخارجية، وتطوير البرمجيات.',
        'philosophy_title' => 'الفلسفة الشخصية والرؤية',
        'philosophy_quote' => '"مهما تكن الظلمة من حولك، استمتع بالرحلة"',
        'philosophy_desc' => 'المزج بين الفهم العميق للاتصال الإنساني والرؤية التحليلية المبنية على البيانات لتحقيق نمو مستدام وقيمة حقيقية للشركات.',
        
        // Shorts Section
        'shorts_badge' => '📱 محتوى مرئي قصير',
        'shorts_title' => '🎬 أحدث الفيديوهات القصيرة',
        'shorts_subtitle' => 'رؤى تسويقية سريعة، نصائح عملية، وتحليلات للحملات الإعلانية في أقل من 60 ثانية',
        'short1_title' => 'كيف ترفع عائد الإعلانات 25x؟ 🚀',
        'short2_title' => 'أسرار تسويق العلامات بالسعودية 🇸🇦',
        'short3_title' => 'استراتيجية نمو متكاملة لشركتك 📈',
        'short4_title' => 'تحليل حملات التسويق وتجنب الأخطاء 🎯',
        
        // Qualifications
        'qual_hero_badge' => '🎓 الاعتمادات الرسمية والشهادات الاحترافية الـ 18',
        'qual_hero_title' => 'المؤهلات والاعتمادات الرسمية (18 شهادة)',
        'qual_hero_subtitle' => 'اضغط على أي شهادة لمعاينتها وتكبيرها بالكامل 100%.',
        
        // Career
        'career_hero_badge' => '💼 المسار الوظيفي والسيرة المهنية',
        'career_hero_title' => 'السيرة المهنية والخبرات القيادية',
        'career_hero_subtitle' => 'أكثر من 8 سنوات من القيادة التسويقية وإدارة الميزانيات وتحقيق قفزات المبيعات بالمنطقة.',
        
        // Portfolio
        'port_hero_badge' => '💼 دراسات الحالة والمشاريع الميدانية (13 دراسة حالة)',
        'port_hero_title' => 'معرض الأعمال والدراسات الناجحة',
        'port_hero_subtitle' => 'عرض كامل لشعارات البراندات والاسكرينات بدون أي اقتطاع مع إمكانية التكبير والتفاعل 100%.',
        
        // Services
        'serv_hero_badge' => '🛠️ الخدمات والحلول الاستراتيجية',
        'serv_hero_title' => 'الخدمات الاستشارية والتنفيذية',
        'serv_hero_subtitle' => 'حلول تسويقية متكاملة تضمن تموضع علامتك التجارية وزيادة المبيعات والعائد الإعلاني.',
        
        // Courses
        'courses_hero_badge' => '🎓 الدورات والورش التدريبية',
        'courses_hero_title' => 'برامج التدريب والماستر كلاس الاحترافي',
        'courses_hero_subtitle' => 'نقل خبرات العمل الميداني والقيادة التسويقية للمحترفين والشركات.',
        
        // Blog
        'blog_hero_badge' => '📝 المقالات والأفكار الاستراتيجية',
        'blog_hero_title' => 'المقالات والأفكار التسويقية',
        'blog_hero_subtitle' => 'رؤى واستراتيجيات متقدمة لمساعدة القادة في زيادة النمو وتطوير المبيعات.',
        
        // Contact
        'contact_hero_badge' => '📅 احجز استشارتك المجانية',
        'contact_hero_title' => 'احجز موعداً استشارياً مع حسن سرحان',
        'contact_hero_subtitle' => 'اختر الموعد المناسب لمناقشة استراتيجية نمو مشروعاتك مباشرة.',
        'contact_emergency_title' => 'للطوارئ والظروف العاجلة فقط:',
        'contact_emergency_btn' => 'اتصال مباشر على الجوال الشخصي (+966 50 626 7542)',
        'form_name' => 'الاسم الكامل',
        'form_email' => 'البريد الإلكتروني',
        'form_phone' => 'رقم الجوال / الواتساب',
        'form_company' => 'اسم الشركة / العلامة التجارية',
        'form_submit' => 'تأكيد الحجز الفوري'
    ],
    'en' => [
        'site_title' => 'HASSAN SARHAN | Marketing Director',
        'nav_qualifications' => 'Qualifications',
        'nav_career' => 'Career Journey',
        'nav_services' => 'Services',
        'nav_courses' => 'Courses',
        'nav_blog' => 'Articles',
        'nav_contact' => 'Contact',
        'book_free_meeting' => 'Online Meeting',
        'other_lang_label' => '🇸🇦 AR',
        'other_lang_code' => 'ar',
        'rights_reserved' => 'All Rights Reserved © HASSAN SARHAN',
        
        // Homepage
        'hero_badge' => '💼 Strategic Marketing Director (+8 Yrs Exp)',
        'hero_name' => 'HASSAN SARHAN',
        'hero_title' => 'Marketing Director',
        'hero_subtitle' => 'Leading growth & digital performance for 50+ global & regional brands across GCC, US & Europe.',
        'btn_consultation' => '📅 Online Meeting',
        'btn_portfolio' => '📂 View Portfolio',
        'kpi_exp' => 'Continuous Leadership Experience (+8 Yrs)',
        'kpi_brands' => 'Global & Regional Brands (+50)',
        'kpi_certs' => 'Official Certifications & Accreditations (18)',
        'kpi_roas' => 'Ad Budget Managed over 8 Years',
        'current_role_badge' => '📌 Current Executive Role',
        'current_role_title' => 'FLUX Ads Agency',
        'current_role_desc' => 'Leading marketing & growth operations for 30+ Saudi clients, managing 4 core divisions: Social Media, Creative Content & Production, Outdoor Ads, and Software Development.',
        'philosophy_title' => 'Leadership Philosophy & Vision',
        'philosophy_quote' => '"Whatever darkness follows you, enjoy the journey"',
        'philosophy_desc' => 'Combining human-centered media insight with data-driven analytics to build sustainable growth and long-term business value.',
        
        // Shorts Section
        'shorts_badge' => '📱 Vertical Video Content',
        'shorts_title' => '🎬 Latest Shorts & Reels',
        'shorts_subtitle' => 'Short marketing insights, practical tips, and campaign analyses in under 60 seconds',
        'short1_title' => 'How to scale Ad ROAS to 25x? 🚀',
        'short2_title' => 'Brand Marketing Secrets in Saudi Arabia 🇸🇦',
        'short3_title' => 'Building an Integrated Growth Strategy 📈',
        'short4_title' => 'Analyzing Digital Campaigns & Avoiding Pitfalls 🎯',
        
        // Qualifications
        'qual_hero_badge' => '🎓 Official Accreditations & 18 Professional Certifications',
        'qual_hero_title' => 'Qualifications & Official Accreditations (18 Certifications)',
        'qual_hero_subtitle' => 'Click any certificate for a 100% full resolution preview.',
        
        // Career
        'career_hero_badge' => '💼 Professional Track Record & Career Journey',
        'career_hero_title' => 'Executive Career Journey & Track Record',
        'career_hero_subtitle' => 'Over 8 years of marketing leadership, budget management, and revenue scaling in the GCC & MENA.',
        
        // Portfolio
        'port_hero_badge' => '💼 Documented Case Studies & Projects (13 Case Studies)',
        'port_hero_title' => 'Portfolio & Proven Case Studies',
        'port_hero_subtitle' => 'Full uncropped display of brand logos and project screenshots with interactive 100% zoom.',
        
        // Services
        'serv_hero_badge' => '🛠️ Strategic Services & Growth Solutions',
        'serv_hero_title' => 'Executive Consulting & Performance Services',
        'serv_hero_subtitle' => 'Integrated marketing solutions to position your brand, optimize conversions, and maximize ad ROAS.',
        
        // Courses
        'courses_hero_badge' => '🎓 Executive Masterclasses & Training Workshops',
        'courses_hero_title' => 'Professional Masterclasses & Training',
        'courses_hero_subtitle' => 'Transferring real-world field experience and marketing leadership to executives & teams.',
        
        // Blog
        'blog_hero_badge' => '📝 Strategic Insights & Articles',
        'blog_hero_title' => 'Marketing Articles & Strategic Insights',
        'blog_hero_subtitle' => 'Advanced insights and strategies empowering business leaders to scale revenue.',
        
        // Contact
        'contact_hero_badge' => '📅 Book Your Free Consultation',
        'contact_hero_title' => 'Schedule a Strategy Session with HASSAN SARHAN',
        'contact_hero_subtitle' => 'Select an available date slot to discuss your brand growth strategy directly.',
        'contact_emergency_title' => 'For Urgent Matters Only:',
        'contact_emergency_btn' => 'Direct Call (+966 50 626 7542)',
        'form_name' => 'Full Name',
        'form_email' => 'Email Address',
        'form_phone' => 'Phone / WhatsApp Number',
        'form_company' => 'Company / Brand Name',
        'form_submit' => 'Confirm Booking'
    ]
];

function t($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $translations['ar'][$key] ?? $key;
}

function getSiteSettings() {
    $db = getDBConnection();
    try {
        $stmt = $db->query("SELECT * FROM site_settings WHERE id = 1");
        $res = $stmt->fetch();
        return $res ?: [
            'site_title_ar' => 'حسن سرحان | مدير التسويق والاستراتيجية والأداء',
            'site_title_en' => 'HASSAN SARHAN | Marketing Director',
            'script_header' => '',
            'script_body' => '',
            'courses_page_enabled' => 1,
        ];
    } catch (Exception $e) {
        return [
            'site_title_ar' => 'حسن سرحان | مدير التسويق والاستراتيجية والأداء',
            'site_title_en' => 'HASSAN SARHAN | Marketing Director',
            'script_header' => '',
            'script_body' => '',
            'courses_page_enabled' => 1,
        ];
    }
}
