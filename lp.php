<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    $slug = 'enterprise-growth'; // Default fallback landing page
}

$lp = null;
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM landing_pages WHERE slug = ? AND is_published = 1");
    $stmt->execute([$slug]);
    $lp = $stmt->fetch();
} catch (Exception $e) {
    $lp = null;
}

// Fallback to default template if DB record not found
if (!$lp) {
    $lp = [
        'id' => 1,
        'title_ar' => 'منظومة النمو الشاملة واستراتيجيات الشراء الإعلاني',
        'title_en' => 'Enterprise Growth & Scaled Media Buying',
        'slug' => $slug,
        'meta_description_ar' => 'صفحة هبوط مخصصة لمضاعفة عائد الإعلانات وهندسة مسارات التحويل للمتاجر والشركات الكبرى.',
        'blocks_json' => json_encode([
            [
                "type" => "hero",
                "badge_ar" => "🔥 برنامج استشاري قيادي حصري",
                "title_ar" => "ضاعف عائد إعلاناتك واستحوذ على حصة السوق السعودي والخليجي",
                "subtitle_ar" => "استراتيجيات الشراء الإعلاني المتقدمة وهندسة مسارات التحويل للمتاجر والشركات الكبرى لتحقيق أعلى عائد استثماري مثبت.",
                "primary_cta_text_ar" => "🚀 احجز جلسة الاستراتيجية مجاناً",
                "primary_cta_link" => "#pricing_section",
                "secondary_cta_text_ar" => "💬 استفسر عبر الواتساب",
                "secondary_cta_link" => "https://wa.me/966500000000",
                "trust_note_ar" => "⚡ نتائج ملموسة وضمان ذهبي على الأداء والنمو"
            ],
            [
                "type" => "problems",
                "title_ar" => "هل تعاني من هذه التحديات في حملاتك التسويقية الحالية؟",
                "subtitle_ar" => "أبرز العوائق التي تمنع المتاجر والشركات من الوصول لربحية عالية وتستنزف الميزانيات الإعلانية.",
                "items" => [
                    ["icon" => "⚠️", "title_ar" => "ارتفاع تكلفة الاستحواذ (CAC)", "desc_ar" => "تضخم تكلفة العميل وضياع الميزانيات بدون تحقيق عائد مجدٍ على الإعلانات."],
                    ["icon" => "📉", "title_ar" => "ضعف نسبة التحويل (CR)", "desc_ar" => "زيارات ضخمة للمتجر دون وجود مسار تحويل مدروس يحفز الزائر على الشراء الفوري."],
                    ["icon" => "🔄", "title_ar" => "تخبط الخوارزميات وتراجع النتائج", "desc_ar" => "التأثر السلبي بالتحديثات المستمرة لإعلانات تيك توك وميتا وجوجل بدون استراتيجية بديلة."]
                ]
            ],
            [
                "type" => "solution",
                "title_ar" => "الحل الاستراتيجي: منظومة النمو والتحويل المباشر",
                "subtitle_ar" => "منهجية متكاملة تعتمد على البيانات الحية والتحليل العميق لتجاوز عقبات الخوارزميات وتحقيق قفزات نوعية في المبيعات.",
                "items" => [
                    ["icon" => "🎯", "title_ar" => "استهداف دقيق للمشتري الجاهز", "desc_ar" => "توجيه الحملات للشرائح ذات القدرة الشرائية العالية والمهتمة بالمنتج فعلياً."],
                    ["icon" => "🛠️", "title_ar" => "هندسة صفحة الهبوط والـ Funnel", "desc_ar" => "تأسيس صفحات هبوط عالية السلاسة تزيد من معدلات إغلاق الصفقة والتأكيد."],
                    ["icon" => "📈", "title_ar" => "تحسين مستمر مستند إلى ROI", "desc_ar" => "إدارة الميزانيات بدقة متناهية وإعادة توزيع الإنفاق على الحملات الأكثر ربحية."]
                ]
            ],
            [
                "type" => "features",
                "title_ar" => "المميزات التنافسية التي تضمن نجاح حملتك",
                "subtitle_ar" => "نقدم لك حلولاً سريعة ومثبتة تضع عملك في صدارة المنافسين.",
                "items" => [
                    ["icon" => "⚡", "title_ar" => "إطلاق سريع وتجهيز شامل", "desc_ar" => "تجهيز الحملات وصفحات الهبوط خلال أيام معدودة لبدء جني النتائج."],
                    ["icon" => "🛡️", "title_ar" => "إدارة مخاطر وميزانيات محكمة", "desc_ar" => "عدم إهدار أي ريال إعلاني بدون اختبار علمي وتتبع دقيق للعائد."],
                    ["icon" => "📱", "title_ar" => "توافق كامل مع الجوال والواتساب", "desc_ar" => "تجربة مستخدم سريعة تتيح إتمام الطلب والتحويل بضغطة زر واحدة."],
                    ["icon" => "📊", "title_ar" => "تقارير أداء ومتابعة قيادية", "desc_ar" => "لوحة تحكم شفافة تظهر لك كافة المؤشرات ومعدلات النمو لحظة بلحظة."]
                ]
            ],
            [
                "type" => "process",
                "title_ar" => "آلية العمل في 4 خطوات بسيطة ومنظمة",
                "subtitle_ar" => "مسار واضح يضمن الانتقال من مرحلة الدراسة إلى مرحلة الأرباح العالية.",
                "steps" => [
                    ["num" => "01", "title_ar" => "التحليل والتشخيص الشامل", "desc_ar" => "دراسة الوضع الحالي وحساب تكلفة الاستحواذ وتحديد الفجوات."],
                    ["num" => "02", "title_ar" => "بناء الاستراتيجية والـ Funnel", "desc_ar" => "صياغة الرسائل الإعلانية وتجهيز صفحات الهبوط عالية التحويل."],
                    ["num" => "03", "title_ar" => "إطلاق الحملات والاختبار المكثف", "desc_ar" => "توزيع الميزانيات واختبار الزوايا الإعلانية الأكثر تأثيراً."],
                    ["num" => "04", "title_ar" => "التوسع والنمو المضاعف (Scaling)", "desc_ar" => "زيادة الإنفاق على الأقسام الرابحة ومضاعفة العائد الاستثماري."]
                ]
            ],
            [
                "type" => "case_studies",
                "title_ar" => "أرقام وإنجازات مثبتة على أرض الواقع",
                "subtitle_ar" => "نتائج حقيقية تم تحقيقها في حملات الشراء الإعلاني واستشارات النمو.",
                "stats" => [
                    ["number" => "+350%", "label_ar" => "نمو في معدل العائد على الإعلانات (ROAS)"],
                    ["number" => "+14M SAR", "label_ar" => "إجمالي ميزانيات إعلانية تم إدارتها بنجاح"],
                    ["number" => "98%", "label_ar" => "نسبة رضا واستمرار المتاجر والعملاء"],
                    ["number" => "+25x", "label_ar" => "أعلى عائد محقق على حملة خاصة"]
                ]
            ],
            [
                "type" => "testimonials",
                "title_ar" => "ماذا يقول رؤساء ومدراء التسويق عن خدماتنا؟",
                "subtitle_ar" => "شهادات وثقة قيادات الأعمال والمتاجر الإلكترونية في السعودية والخليج.",
                "items" => [
                    ["name_ar" => "عبد الله الشمري", "role_ar" => "رئيس مجلس إدارة متجر الكتروني", "quote_ar" => "التحول الذي شهدناه في نتائج الحملات كان استثنائياً، ارتفع عائد الإعلانات من 1.8x إلى أكثر من 4.2x في أقل من شهرين.", "stars" => 5],
                    ["name_ar" => "فهد السبيعي", "role_ar" => "مدير تسويق شركة تجارة مكملات", "quote_ar" => "الاحترافية في صياغة الزوايا الإعلانية وتصميم صفحات الهبوط جعلت معدل التحويل يرتفع بشكل ملحوظ جداً.", "stars" => 5],
                    ["name_ar" => "سارة الخالدي", "role_ar" => "مؤسس براند عطور سعودي", "quote_ar" => "دقة الاستهداف والتوجيه المستمر حول الميزانيات وفرت علينا مبالغ ضخمة وحققت مبيعات قياسية.", "stars" => 5]
                ]
            ],
            [
                "type" => "guarantee",
                "title_ar" => "🛡️ الضمان الذهبي لتقليل المخاطر 100%",
                "subtitle_ar" => "نحن واثقون من جودة المنظومة والنتائج، لذلك نضمن لك الالتزام الكامل بالتنفيذ وفق أعلى معايير الجودة والمتابعة المستمرة حتى تحقيق الأهداف المتفق عليها."
            ],
            [
                "type" => "pricing",
                "title_ar" => "عرض باقة النمو والاستثمار الاستراتيجي",
                "subtitle_ar" => "باقة متكاملة تغطي كافة متطلبات التحليلات والحملات وصفحات الهبوط.",
                "package_name_ar" => "باقة التوسع القيادي (Scale Package)",
                "price_ar" => "4,999 ﷼",
                "original_price_ar" => "9,999 ﷼",
                "discount_badge_ar" => "🔥 خصم 50% لفترة محدودة",
                "features" => [
                    "إدارة شاملة لحملات الشراء الإعلاني (TikTok, Meta, Google)",
                    "تأسيس وتجهيز صفحات هبوط سريعة عالية التحويل",
                    "صياغة 5 زوايا إعلانية ابتكارية للمنتج",
                    "لوحة تحكم وتتبع لحظي لمعدل ROI",
                    "جلسات استشارية أسبوعية وتطوير مستمر"
                ],
                "cta_text_ar" => "💳 اشترك الآن واستفد من العرض",
                "cta_link" => "#faq_cta_section"
            ],
            [
                "type" => "faq_cta",
                "title_ar" => "الأسئلة الشائعة واحجز استشارتك الآن",
                "subtitle_ar" => "إجابات على أهم الاستفسارات قبل البدء في رحلة النمو.",
                "faqs" => [
                    ["q_ar" => "كم يستغرق تجهيز صفحة الهبوط والحملات؟", "a_ar" => "يتم تجهيز كافة الأصول وإطلاق الحملات خلال 3 إلى 5 أيام عمل فقط."],
                    ["q_ar" => "هل الباقة مناسبة للمتاجر الجديدة أم القائمة؟", "a_ar" => "مناسبة للمتاجر والشركات القائمة التي ترغب في مضاعفة مبيعاتها وتقليل تكلفة الاستحواذ."],
                    ["q_ar" => "كيف يتم متابعة النتائج والأداء؟", "a_ar" => "من خلال لوحة تحكم مخصصة وتقارير أسبوعية تجمع كافة المؤشرات والتحليلات."]
                ],
                "final_cta_title_ar" => "🚀 هل أنت جاهز لمضاعفة مبيعات متجرك الآن؟",
                "final_cta_btn_ar" => "📲 احجز موعدك وتواصل مباشرة عبر الواتساب",
                "whatsapp_url" => "https://wa.me/966500000000"
            ]
        ])
    ];
}

$title = (SITE_LANG === 'en' && !empty($lp['title_en'])) ? $lp['title_en'] : $lp['title_ar'];
$blocks = json_decode($lp['blocks_json'] ?? '[]', true) ?: [];
?>

<style>
/* Landing Page Specific High-Converting Theme */
.lp-page-wrapper {
    background-color: #070D1E;
    color: #F8FAFC;
    font-family: 'Cairo', 'Readex Pro', sans-serif;
    line-height: 1.8;
}

.lp-hero-section {
    background: linear-gradient(135deg, #0B132B 0%, #162447 60%, #070D1E 100%);
    padding: 5rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    position: relative;
    overflow: hidden;
}

.lp-hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, rgba(0,0,0,0) 70%);
    pointer-events: none;
}

.lp-badge {
    display: inline-block;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.4);
    color: #D4AF37;
    padding: 6px 18px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
}

.lp-hero-title {
    font-size: 2.6rem !important;
    font-weight: 900 !important;
    color: #FFFFFF !important;
    line-height: 1.3 !important;
    margin-bottom: 1.2rem;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.lp-hero-subtitle {
    font-size: 1.15rem;
    color: #CBD5E1;
    max-width: 780px;
    margin: 0 auto 2rem auto;
}

.lp-cta-group {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.lp-btn-primary {
    background: linear-gradient(135deg, #D4AF37 0%, #B48811 100%);
    color: #0B132B !important;
    font-weight: 900;
    font-size: 1.05rem;
    padding: 14px 32px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
}

.lp-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 30px rgba(212, 175, 55, 0.45);
}

.lp-btn-secondary {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #FFFFFF !important;
    font-weight: 800;
    font-size: 1rem;
    padding: 14px 28px;
    border-radius: 12px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.lp-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: #D4AF37;
}

.lp-trust-note {
    font-size: 0.88rem;
    color: #94A3B8;
    font-weight: 700;
}

/* Sections Styling */
.lp-section {
    padding: 4.5rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.lp-section-title {
    font-size: 2rem !important;
    font-weight: 900 !important;
    color: #FFFFFF !important;
    text-align: center;
    margin-bottom: 0.6rem;
}

.lp-section-subtitle {
    font-size: 1rem;
    color: #94A3B8;
    text-align: center;
    max-width: 680px;
    margin: 0 auto 3rem auto;
}

.lp-grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.lp-grid-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}

.lp-card {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.8rem;
    transition: all 0.3s ease;
}

.lp-card:hover {
    border-color: rgba(212, 175, 55, 0.4);
    transform: translateY(-4px);
}

.lp-card-problem {
    background: rgba(239, 68, 68, 0.05);
    border-color: rgba(239, 68, 68, 0.2);
}

.lp-card-solution {
    background: rgba(34, 197, 94, 0.05);
    border-color: rgba(34, 197, 94, 0.2);
}

.lp-icon-box {
    font-size: 2.2rem;
    margin-bottom: 1rem;
}

.lp-card-title {
    font-size: 1.2rem !important;
    font-weight: 800 !important;
    color: #FFFFFF !important;
    margin-bottom: 0.6rem;
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
}

.lp-card-desc {
    font-size: 0.95rem;
    color: #94A3B8;
    line-height: 1.6;
}

/* Step Timeline */
.lp-step-num {
    display: inline-block;
    background: linear-gradient(135deg, #D4AF37, #B48811);
    color: #0B132B;
    font-weight: 900;
    font-size: 1rem;
    padding: 4px 12px;
    border-radius: 8px;
    margin-bottom: 0.8rem;
}

/* Stats Counter */
.lp-stat-box {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 16px;
    padding: 2rem 1rem;
    text-align: center;
}

.lp-stat-number {
    font-size: 2.4rem;
    font-weight: 900;
    color: #D4AF37;
    margin-bottom: 4px;
}

.lp-stat-label {
    font-size: 0.92rem;
    color: #CBD5E1;
    font-weight: 700;
}

/* Gold Guarantee Card */
.lp-guarantee-box {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.12) 0%, rgba(15, 23, 42, 0.9) 100%);
    border: 2px solid #D4AF37;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    max-width: 850px;
    margin: 0 auto;
}

/* Pricing Card */
.lp-pricing-card {
    background: rgba(15, 23, 42, 0.9);
    border: 2px solid #D4AF37;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    box-shadow: 0 15px 40px rgba(212, 175, 55, 0.15);
}

.lp-discount-badge {
    position: absolute;
    top: -15px;
    right: 50%;
    transform: translateX(50%);
    background: #EF4444;
    color: #FFF;
    padding: 4px 16px;
    border-radius: 20px;
    font-weight: 800;
    font-size: 0.85rem;
}

.lp-price-amount {
    font-size: 2.8rem;
    font-weight: 900;
    color: #D4AF37;
    margin: 1rem 0;
}

.lp-price-original {
    font-size: 1.2rem;
    color: #64748B;
    text-decoration: line-through;
    margin-left: 8px;
}

.lp-feature-list {
    text-align: right;
    list-style: none;
    margin: 1.8rem 0;
    padding: 0;
}

.lp-feature-list li {
    font-size: 0.98rem;
    color: #E2E8F0;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.lp-feature-list li::before {
    content: "✓";
    color: #4ADE80;
    font-weight: 900;
    font-size: 1.1rem;
}

/* FAQ Box */
.lp-faq-item {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 1.2rem 1.5rem;
    margin-bottom: 1rem;
}

.lp-faq-q {
    font-weight: 800;
    font-size: 1.05rem;
    color: #FFF;
    margin-bottom: 6px;
}

.lp-faq-a {
    font-size: 0.95rem;
    color: #94A3B8;
}

.lp-final-cta-banner {
    background: linear-gradient(135deg, #0B132B, #1E293B);
    border: 1px solid #D4AF37;
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    margin-top: 3rem;
}

@media (max-width: 768px) {
    .lp-hero-title { font-size: 1.8rem !important; }
    .lp-section-title { font-size: 1.5rem !important; }
    .lp-cta-group { flex-direction: column; width: 100%; }
    .lp-btn-primary, .lp-btn-secondary { width: 100%; justify-content: center; }
}
</style>

<div class="lp-page-wrapper">
    <div class="container" style="padding-top: 1rem;">
        <!-- Dynamically render all 10 blocks -->
        <?php foreach ($blocks as $blk): ?>
            <?php $type = $blk['type'] ?? 'hero'; ?>

            <?php if ($type === 'hero'): ?>
                <header class="lp-hero-section">
                    <?php if (!empty($blk['badge_ar'])): ?>
                        <div class="lp-badge"><?php echo htmlspecialchars($blk['badge_ar']); ?></div>
                    <?php endif; ?>

                    <h1 class="lp-hero-title"><?php echo htmlspecialchars($blk['title_ar'] ?? $title); ?></h1>

                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-hero-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-cta-group">
                        <?php if (!empty($blk['primary_cta_text_ar'])): ?>
                            <a href="<?php echo htmlspecialchars($blk['primary_cta_link'] ?? '#pricing_section'); ?>" class="lp-btn-primary">
                                <?php echo htmlspecialchars($blk['primary_cta_text_ar']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($blk['secondary_cta_text_ar'])): ?>
                            <a href="<?php echo htmlspecialchars($blk['secondary_cta_link'] ?? 'https://wa.me/966500000000'); ?>" target="_blank" class="lp-btn-secondary">
                                <?php echo htmlspecialchars($blk['secondary_cta_text_ar']); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($blk['trust_note_ar'])): ?>
                        <div class="lp-trust-note"><?php echo htmlspecialchars($blk['trust_note_ar']); ?></div>
                    <?php endif; ?>
                </header>

            <?php elseif ($type === 'problems'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'تحديات النشاط التسويقي'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-3">
                        <?php if (isset($blk['items']) && is_array($blk['items'])): ?>
                            <?php foreach ($blk['items'] as $item): ?>
                                <div class="lp-card lp-card-problem">
                                    <div class="lp-icon-box"><?php echo htmlspecialchars($item['icon'] ?? '⚠️'); ?></div>
                                    <h3 class="lp-card-title"><?php echo htmlspecialchars($item['title_ar'] ?? ''); ?></h3>
                                    <p class="lp-card-desc"><?php echo htmlspecialchars($item['desc_ar'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'solution'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'الحل الاستراتيجي'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-3">
                        <?php if (isset($blk['items']) && is_array($blk['items'])): ?>
                            <?php foreach ($blk['items'] as $item): ?>
                                <div class="lp-card lp-card-solution">
                                    <div class="lp-icon-box"><?php echo htmlspecialchars($item['icon'] ?? '🎯'); ?></div>
                                    <h3 class="lp-card-title"><?php echo htmlspecialchars($item['title_ar'] ?? ''); ?></h3>
                                    <p class="lp-card-desc"><?php echo htmlspecialchars($item['desc_ar'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'features'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'المميزات التنافسية'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-4">
                        <?php if (isset($blk['items']) && is_array($blk['items'])): ?>
                            <?php foreach ($blk['items'] as $item): ?>
                                <div class="lp-card">
                                    <div class="lp-icon-box"><?php echo htmlspecialchars($item['icon'] ?? '⚡'); ?></div>
                                    <h3 class="lp-card-title"><?php echo htmlspecialchars($item['title_ar'] ?? ''); ?></h3>
                                    <p class="lp-card-desc"><?php echo htmlspecialchars($item['desc_ar'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'process'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'خطوات آلية العمل'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-4">
                        <?php if (isset($blk['steps']) && is_array($blk['steps'])): ?>
                            <?php foreach ($blk['steps'] as $step): ?>
                                <div class="lp-card">
                                    <span class="lp-step-num" data-en="Step <?php echo htmlspecialchars($step['num'] ?? '01'); ?>">الخطوة <?php echo htmlspecialchars($step['num'] ?? '01'); ?></span>
                                    <h3 class="lp-card-title"><?php echo htmlspecialchars($step['title_ar'] ?? ''); ?></h3>
                                    <p class="lp-card-desc"><?php echo htmlspecialchars($step['desc_ar'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'case_studies'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'أرقام وإنجازات مثبتة'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-4">
                        <?php if (isset($blk['stats']) && is_array($blk['stats'])): ?>
                            <?php foreach ($blk['stats'] as $st): ?>
                                <div class="lp-stat-box">
                                    <div class="lp-stat-number"><?php echo htmlspecialchars($st['number'] ?? '0'); ?></div>
                                    <div class="lp-stat-label"><?php echo htmlspecialchars($st['label_ar'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'testimonials'): ?>
                <section class="lp-section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'آراء وشهادات العملاء'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-grid-3">
                        <?php if (isset($blk['items']) && is_array($blk['items'])): ?>
                            <?php foreach ($blk['items'] as $tm): ?>
                                <div class="lp-card">
                                    <div style="color:#D4AF37; margin-bottom:10px; font-size:1.1rem;">
                                        <?php echo str_repeat('⭐', (int)($tm['stars'] ?? 5)); ?>
                                    </div>
                                    <p class="lp-card-desc" style="font-style:italic; margin-bottom:1rem;">"<?php echo htmlspecialchars($tm['quote_ar'] ?? ''); ?>"</p>
                                    <div style="font-weight:800; color:#FFF; font-size:0.95rem;"><?php echo htmlspecialchars($tm['name_ar'] ?? ''); ?></div>
                                    <div style="font-size:0.82rem; color:#94A3B8;"><?php echo htmlspecialchars($tm['role_ar'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($type === 'guarantee'): ?>
                <section class="lp-section">
                    <div class="lp-guarantee-box">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛡️</div>
                        <h2 class="lp-section-title" style="color:#D4AF37 !important; margin-bottom:0.8rem;"><?php echo htmlspecialchars($blk['title_ar'] ?? 'الضمان الذهبي'); ?></h2>
                        <p class="lp-card-desc" style="font-size:1.05rem; color:#CBD5E1; max-width:700px; margin:0 auto;"><?php echo htmlspecialchars($blk['subtitle_ar'] ?? ''); ?></p>
                    </div>
                </section>

            <?php elseif ($type === 'pricing'): ?>
                <section class="lp-section" id="pricing_section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'باقة الاستثمار'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div class="lp-pricing-card">
                        <?php if (!empty($blk['discount_badge_ar'])): ?>
                            <div class="lp-discount-badge"><?php echo htmlspecialchars($blk['discount_badge_ar']); ?></div>
                        <?php endif; ?>

                        <h3 style="color:#FFF !important; font-size:1.4rem !important; font-weight:800 !important;"><?php echo htmlspecialchars($blk['package_name_ar'] ?? 'الباقة القيادية'); ?></h3>

                        <div class="lp-price-amount">
                            <?php echo htmlspecialchars($blk['price_ar'] ?? ''); ?>
                            <?php if (!empty($blk['original_price_ar'])): ?>
                                <span class="lp-price-original"><?php echo htmlspecialchars($blk['original_price_ar']); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($blk['features']) && is_array($blk['features'])): ?>
                            <ul class="lp-feature-list">
                                <?php foreach ($blk['features'] as $ft): ?>
                                    <li><?php echo htmlspecialchars($ft); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <a href="<?php echo htmlspecialchars($blk['cta_link'] ?? '#faq_cta_section'); ?>" class="lp-btn-primary" style="width:100%; justify-content:center;">
                            <?php echo htmlspecialchars($blk['cta_text_ar'] ?? '💳 اشترك الآن'); ?>
                        </a>
                    </div>
                </section>

            <?php elseif ($type === 'faq_cta'): ?>
                <section class="lp-section" id="faq_cta_section">
                    <h2 class="lp-section-title"><?php echo htmlspecialchars($blk['title_ar'] ?? 'الأسئلة الشائعة'); ?></h2>
                    <?php if (!empty($blk['subtitle_ar'])): ?>
                        <p class="lp-section-subtitle"><?php echo htmlspecialchars($blk['subtitle_ar']); ?></p>
                    <?php endif; ?>

                    <div style="max-width:750px; margin:0 auto 3rem auto;">
                        <?php if (isset($blk['faqs']) && is_array($blk['faqs'])): ?>
                            <?php foreach ($blk['faqs'] as $faq): ?>
                                <div class="lp-faq-item">
                                    <div class="lp-faq-q">❓ <?php echo htmlspecialchars($faq['q_ar'] ?? ''); ?></div>
                                    <div class="lp-faq-a"><?php echo htmlspecialchars($faq['a_ar'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($blk['final_cta_title_ar'])): ?>
                        <div class="lp-final-cta-banner">
                            <h2 style="color:#FFF !important; font-size:1.8rem !important; margin-bottom:1.2rem;"><?php echo htmlspecialchars($blk['final_cta_title_ar']); ?></h2>
                            <a href="<?php echo htmlspecialchars($blk['whatsapp_url'] ?? 'https://wa.me/966500000000'); ?>" target="_blank" class="lp-btn-primary" style="font-size:1.1rem; padding:16px 36px;">
                                <?php echo htmlspecialchars($blk['final_cta_btn_ar'] ?? '📲 تواصل معنا الآن'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
