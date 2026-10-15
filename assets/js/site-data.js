/**
 * HSN SRHAN Strategic Portfolio & Admin Master Data Engine
 * Synchronizes default live site data across LocalStorage & PHP/DB backend
 * EXACT ACCURATE DATA MATCHING STATIC HTML FRONTEND
 */

const DEFAULT_SITE_DATA = {
    "site_settings": {
        "site_title_ar": "حسن سرحان | مدير تسويق استراتيجي",
        "site_title_en": "HASSAN SARHAN | Marketing Director",
        "hero_badge_ar": "💼 مدير تسويق استراتيجي خبرة +8 سنوات",
        "hero_badge_en": "💼 Strategic Marketing Director (+8 Yrs Exp)",
        "hero_name_ar": "حسن سرحان",
        "hero_name_en": "HASSAN SARHAN",
        "hero_subtitle_ar": "قيادة استراتيجيات النمو والتسويق الرقمي لـ +50 علامة تجارية عالمية ومحلية في الخليج وأوروبا وأمريكا.",
        "hero_subtitle_en": "Leading growth & digital performance for 50+ global & regional brands across GCC, US & Europe.",
        "hero_video_url": "https://www.youtube.com/embed/dQw4w9WgXcQ",
        "kpi_exp": "+8 سنوات",
        "kpi_brands": "+50 براند",
        "kpi_certs": "18 شهادة",
        "kpi_roas": "+7M SAR",
        "current_role_badge_ar": "📌 المنصب القيادي الحالي",
        "current_role_badge_en": "📌 Current Executive Role",
        "current_role_title_ar": "شركة فلوكس آدز للتسويق",
        "current_role_title_en": "FLUX Ads Agency",
        "current_role_desc_ar": "قيادة عمليات التسويق والنمو لأكثر من 30 عميلاً في السوق السعودي، وإدارة 4 أقسام رئيسية: التواصل الاجتماعي، المحتوى الإبداعي والإنتاج، الحملات الخارجية، وتطوير البرمجيات.",
        "current_role_desc_en": "Leading marketing & growth operations for 30+ Saudi clients, managing 4 core divisions: Social Media, Creative Content & Production, Outdoor Ads, and Software Development.",
        "philosophy_quote_ar": "\"مهما تكن الظلمة من حولك، استمتع بالرحلة\"",
        "philosophy_quote_en": "\"Whatever darkness follows you, enjoy the journey\"",
        "philosophy_desc_ar": "المزج بين الفهم العميق للاتصال الإنساني والرؤية التحليلية المبنية على البيانات لتحقيق نمو مستدام وقيمة حقيقية للشركات.",
        "philosophy_desc_en": "Combining human-centered media insight with data-driven analytics to build sustainable growth and long-term business value.",
        "emergency_phone": "+966506267542",
        "emergency_whatsapp": "+966506267542",
        "emergency_title_ar": "للطوارئ والظروف العاجلة فقط:",
        "emergency_title_en": "For Urgent Matters Only:",
        "courses_page_enabled": true,
        "max_daily_bookings": 3
    },
    "bank_accounts": {
        "id": 1,
        "bank_name_ar": "D360 بنك",
        "bank_name_en": "D360 Bank",
        "account_name_ar": "حسن فايز حسن سرحان",
        "account_name_en": "Hassan Fayez Hassan Sarhan",
        "iban": "SA6936036036047948932598",
        "swift": "DBAKSARIXXX",
        "transfer_note_ar": "يرجى اختيار الغرض من التحويل صديق عند إجراء التحويل البنكي.",
        "transfer_note_en": "Please select transfer purpose as Friend when making the bank transfer."
    },
    "qualifications": [
        {
            "id": 1,
            "sort_order": 1,
            "title_ar": "1. بكالوريوس الصحافة والإعلام",
            "title_en": "Bachelor of Journalism & Mass Comm",
            "issuer_ar": "أكاديمية أخبار اليوم",
            "issuer_en": "Akhbar El Yom Academy",
            "description_ar": "المؤهل الأكاديمي الرئيسي والتأسيسي في الصحافة والإعلام.",
            "description_en": "",
            "image_url": "assets/images/image66.jpg",
            "is_published": true
        },
        {
            "id": 2,
            "sort_order": 2,
            "title_ar": "2. الاعتماد المهني كمدير تسويق",
            "title_en": "Professional Accreditation as Marketing Manager",
            "issuer_ar": "وزارة الموارد البشرية (السعودية)",
            "issuer_en": "HRSD Ministry",
            "description_ar": "اعتماد حكومي رسمي يثبت الكفاءة المهنية لقيادة وتطوير الاستراتيجيات التسويقية بالسوق السعودي.",
            "description_en": "",
            "image_url": "assets/images/image65.jpg",
            "is_published": true
        },
        {
            "id": 3,
            "sort_order": 3,
            "title_ar": "3. مدخل التسويق الرقمي",
            "title_en": "Introduction to Digital Marketing",
            "issuer_ar": "ميتا",
            "issuer_en": "Meta / Coursera",
            "description_ar": "أسس استهداف الجماهير، بناء الهوية الرقمية، وتصميم استراتيجيات التوسع وتعزيز انتشار العلامة.",
            "description_en": "",
            "image_url": "assets/images/image56.jpg",
            "is_published": true
        },
        {
            "id": 4,
            "sort_order": 4,
            "title_ar": "4. إدارة المنصات الرقمية",
            "title_en": "Social Media Management",
            "issuer_ar": "ميتا",
            "issuer_en": "Meta / Coursera",
            "description_ar": "إدارة وتوجيه الحسابات متعددة المنصات، التخطيط للمحتوى الإبداعي، ومتابعة مؤشرات الأداء.",
            "description_en": "",
            "image_url": "assets/images/image59.jpg",
            "is_published": true
        },
        {
            "id": 5,
            "sort_order": 5,
            "title_ar": "5. أساسيات الإعلانات المدفوعة",
            "title_en": "Paid Advertising Fundamentals",
            "issuer_ar": "ميتا",
            "issuer_en": "Meta / Coursera",
            "description_ar": "بناء الحملات الموجهة، تقسيم شرائح الجماهير، وإدارة ميزانيات النشر.",
            "description_en": "",
            "image_url": "assets/images/image64.jpg",
            "is_published": true
        },
        {
            "id": 6,
            "sort_order": 6,
            "title_ar": "6. احتراف إعلانات ميتا",
            "title_en": "Advanced Meta Advertising",
            "issuer_ar": "ميتا",
            "issuer_en": "Meta / Coursera",
            "description_ar": "تخصيص الميزانيات الكبيرة وتطوير الرسائل الإعلانية الإبداعية.",
            "description_en": "",
            "image_url": "assets/images/image57.jpg",
            "is_published": true
        },
        {
            "id": 7,
            "sort_order": 7,
            "title_ar": "7. قياس وتحسين الأداء الإعلاني",
            "title_en": "Measuring & Optimizing Campaigns",
            "issuer_ar": "ميتا",
            "issuer_en": "Meta / Coursera",
            "description_ar": "تحليل بيانات الحملات، تحسين العائد على الإنفاق الإعلاني، وتقليل تكلفة الاكتساب.",
            "description_en": "",
            "image_url": "assets/images/image60.jpg",
            "is_published": true
        },
        {
            "id": 8,
            "sort_order": 8,
            "title_ar": "8. مسار التحدي بالتسويق الرقمي",
            "title_en": "Digital Marketing Challenger Track",
            "issuer_ar": "وزارة الاتصالات & Udacity",
            "issuer_en": "MCIT & Udacity",
            "description_ar": "مبادرة حكومية متقدمة لتطبيق أحدث منهجيات النمو الرقمي.",
            "description_en": "",
            "image_url": "assets/images/image40.png",
            "is_published": true
        },
        {
            "id": 9,
            "sort_order": 9,
            "title_ar": "9. محترف إدارة المجتمعات الرقمية",
            "title_en": "Social Media Marketing Professional",
            "issuer_ar": "Hootsuite & LinkedIn",
            "issuer_en": "Hootsuite & LinkedIn",
            "description_ar": "إستراتيجية التسويق الاحترافي عبر منصات التواصل الاجتماعي.",
            "description_en": "",
            "image_url": "assets/images/image43.jpg",
            "is_published": true
        },
        {
            "id": 10,
            "sort_order": 10,
            "title_ar": "10. إعلانات قوقل المتقدمة",
            "title_en": "Google Ads Certification",
            "issuer_ar": "Google Digital Garage",
            "issuer_en": "Google Digital Garage",
            "description_ar": "تخطيط وتنفيذ حملات محركات البحث والإعلانات المنبثقة.",
            "description_en": "",
            "image_url": "assets/images/image39.jpg",
            "is_published": true
        },
        {
            "id": 11,
            "sort_order": 11,
            "title_ar": "11. التخطيط التسويقي المتكامل",
            "title_en": "Integrated Marketing Planning",
            "issuer_ar": "HubSpot Academy",
            "issuer_en": "HubSpot Academy",
            "description_ar": "تطوير مسارات التحويل وتوجيه العملاء المحتملين.",
            "description_en": "",
            "image_url": "assets/images/image55.jpg",
            "is_published": true
        },
        {
            "id": 12,
            "sort_order": 12,
            "title_ar": "12. هندسة رحلة العميل",
            "title_en": "Customer Journey Engineering",
            "issuer_ar": "HubSpot Academy",
            "issuer_en": "HubSpot Academy",
            "description_ar": "صياغة المحتوى الجاذب والسرد القصصي للعلامات التجارية.",
            "description_en": "",
            "image_url": "assets/images/image49.jpg",
            "is_published": true
        },
        {
            "id": 13,
            "sort_order": 13,
            "title_ar": "13. الذكاء الاصطناعي للمسوقين",
            "title_en": "AI for Marketers",
            "issuer_ar": "Google Skillshop",
            "issuer_en": "Google Skillshop",
            "description_ar": "تتبع سلوك المستخدمين على المتاجر وتحديد نقاط الانقطاع.",
            "description_en": "",
            "image_url": "assets/images/image45.jpg",
            "is_published": true
        },
        {
            "id": 14,
            "sort_order": 14,
            "title_ar": "14. استراتيجيات محركات البحث",
            "title_en": "SEO Strategies",
            "issuer_ar": "SEMrush Academy",
            "issuer_en": "SEMrush Academy",
            "description_ar": "تصدر نتائج البحث الأولى وبناء الروابط الخلفية.",
            "description_en": "",
            "image_url": "assets/images/image50.jpg",
            "is_published": true
        },
        {
            "id": 15,
            "sort_order": 15,
            "title_ar": "15. إدارة المشاريع والفرق التسويقية",
            "title_en": "Marketing Team Leadership",
            "issuer_ar": "TikTok Academy",
            "issuer_en": "TikTok Academy",
            "description_ar": "صناعة الإعلانات الفيروسية وقصيرة المدى للجيل الجديد.",
            "description_en": "",
            "image_url": "assets/images/image58.jpg",
            "is_published": true
        },
        {
            "id": 16,
            "sort_order": 16,
            "title_ar": "16. أساسيات التسويق الرقمي",
            "title_en": "Digital Marketing Fundamentals",
            "issuer_ar": "Snap Focus Academy",
            "issuer_en": "Snap Focus Academy",
            "description_ar": "استهداف فئات الشباب والجمهور الخليجي بفعالية عالية.",
            "description_en": "",
            "image_url": "assets/images/image46.jpg",
            "is_published": true
        },
        {
            "id": 17,
            "sort_order": 17,
            "title_ar": "17. استراتيجيات نمو الشركات الناشئة",
            "title_en": "Startup Growth Strategies",
            "issuer_ar": "ActiveCampaign",
            "issuer_en": "ActiveCampaign",
            "description_ar": "أتمتة البريد الإلكتروني والواتساب لاستعادة السلات المتروكة.",
            "description_en": "",
            "image_url": "assets/images/image48.jpg",
            "is_published": true
        },
        {
            "id": 18,
            "sort_order": 18,
            "title_ar": "18. تحسين الأداء وعائد الاستثمار",
            "title_en": "ROAS & Performance Optimization",
            "issuer_ar": "LinkedIn Learning",
            "issuer_en": "LinkedIn Learning",
            "description_ar": "إدارة وتوجيه فرق العمل متعددة التخصصات.",
            "description_en": "",
            "image_url": "assets/images/image51.jpg",
            "is_published": true
        }
    ],
    "career_timeline": [
        {
            "id": 1,
            "sort_order": 1,
            "title_ar": "مدير التسويق",
            "title_en": "Marketing Director",
            "company_ar": "شركة فلوكس آدز للتسويق",
            "company_en": "FLUX Ads Agency",
            "period_ar": "2025 - الحالي",
            "period_en": "2025 - Present",
            "location_ar": "الرياض، المملكة العربية السعودية",
            "location_en": "Riyadh, Saudi Arabia",
            "description_ar": "قيادة الاستراتيجيات التسويقية والنمو لأكثر من 30 عميلاً بالسوق السعودي، والإشراف المباشر على قيادة 4 قطاعات رئيسية بالوكالة.",
            "description_en": "",
            "website_url": "https://fluxads.sa",
            "case_study_url": "portfolio.html#flux",
            "logo_url": "assets/images/fluxads.png",
            "is_published": true
        },
        {
            "id": 2,
            "sort_order": 2,
            "title_ar": "مدير التسويق الرقمي والأداء",
            "title_en": "Digital Marketing Manager",
            "company_ar": "شركة راكيتبي للتسويق",
            "company_en": "RAKETBE Agency",
            "period_ar": "2025",
            "period_en": "2025",
            "location_ar": "الرياض، المملكة العربية السعودية",
            "location_en": "Riyadh, Saudi Arabia",
            "description_ar": "إخراج وإدارة الحملات الإعلانية المدفوعة للشركات الطبية والتجارية بالسعودية، إدارة الميزانيات الضخمة وتحقيق عائد إنفاق إعلاني من 5x حتى 25x.",
            "description_en": "",
            "website_url": "https://raketbeagency.com",
            "case_study_url": "portfolio.html#raketbe",
            "logo_url": "assets/images/raketbe.png",
            "is_published": true
        },
        {
            "id": 3,
            "sort_order": 3,
            "title_ar": "مدير التسويق",
            "title_en": "Marketing Manager",
            "company_ar": "مجموعة أرابيفا",
            "company_en": "Arabiva Group",
            "period_ar": "2025",
            "period_en": "2025",
            "location_ar": "الكويت والخليج وأوروبا وأمريكا",
            "location_en": "Kuwait, GCC, EU & US",
            "description_ar": "قيادة الاستراتيجيات التسويقية لـ 3 علامات تجارية عالمية للعطور عبر أسواق الكويت والخليج وأوروبا وأمريكا.",
            "description_en": "",
            "website_url": "https://arabiva.com",
            "case_study_url": "portfolio.html#arabiva",
            "logo_url": "assets/images/image17.jpg",
            "is_published": true
        },
        {
            "id": 4,
            "sort_order": 4,
            "title_ar": "مدير التجارة الرقمية",
            "title_en": "E-Commerce Director",
            "company_ar": "شركة فانتازيا بيد",
            "company_en": "Fantazzia Bed",
            "period_ar": "2022 - 2024",
            "period_en": "2022 - 2024",
            "location_ar": "مصر والخليج",
            "location_en": "Egypt & GCC",
            "description_ar": "تأسيس البنية التحتية للتجارة الرقمية والمبيعات من الصفر ومضاعفة المبيعات لتصل إلى 10 مليون شهرياً.",
            "description_en": "",
            "website_url": "https://fantazzia.com",
            "case_study_url": "portfolio.html#fantazzia",
            "logo_url": "assets/images/image31.png",
            "is_published": true
        },
        {
            "id": 5,
            "sort_order": 5,
            "title_ar": "شريك مؤسس ومدير تسويق",
            "title_en": "Co-Founder & Marketing Director",
            "company_ar": "شركة فليكس للشحن",
            "company_en": "VLEX Shipping",
            "period_ar": "2021",
            "period_en": "2021",
            "location_ar": "القاهرة والجيزة",
            "location_en": "Cairo & Giza",
            "description_ar": "تأسيس وإدارة المنظومة التسويقية لخدمات الشحن السريع للمتاجر الإلكترونية.",
            "description_en": "",
            "website_url": "#",
            "case_study_url": "portfolio.html#vlex",
            "logo_url": "assets/images/VLEX.jpg",
            "is_published": true
        },
        {
            "id": 6,
            "sort_order": 6,
            "title_ar": "أخصائي سوشيال ميديا",
            "title_en": "Social Media Specialist",
            "company_ar": "تطبيق ميديكا هيلثي",
            "company_en": "Medica Healthy App",
            "period_ar": "2018 - 2020",
            "period_en": "2018 - 2020",
            "location_ar": "القاهرة",
            "location_en": "Cairo",
            "description_ar": "إدارة منصات التواصل والمحتوى التوعوي للتطبيق الطبي الشامل.",
            "description_en": "",
            "website_url": "#",
            "case_study_url": "portfolio.html#medica",
            "logo_url": "assets/images/medicahealthy.png",
            "is_published": true
        }
    ],
    "projects": [
        {
            "id": 1,
            "priority": 13,
            "title_ar": "ترسيخ الاسم والوعي بالعيادة السيف كلينك",
            "title_en": "Alsaif Clinic Brand Awareness",
            "slug": "alsaif-clinic",
            "client_name": "عيادات السيف الطبي",
            "client_logo_url": "assets/images/image7_square.png",
            "sector": "medical",
            "problem_text_ar": "تعزيز الوعي بالعلامة التجارية وتثبيت اسم العيادة بالسوق السعودي.",
            "strategy_text_ar": "بناء ركائز محتوى بقيادة الأطباء، إبراز نتائج المرضى الواقعية، السرد القصصي، والحفاظ على هوية بصرية موحدة.",
            "results_json": "{\"Brand\": \"Trusted\", \"Name\": \"Remembered\", \"Engagement\": \"+85%\"}",
            "is_published": true
        },
        {
            "id": 2,
            "priority": 12,
            "title_ar": "إدارة السوشيال ميديا وحجوزات عيادات كوديرما",
            "title_en": "Coderma Clinic Management",
            "slug": "coderma-clinic",
            "client_name": "عيادات كوديرما",
            "client_logo_url": "assets/images/image8.jpg",
            "sector": "medical",
            "problem_text_ar": "إدارة شاملة لمنصات التواصل المباشر وحجز المواعيد بالعيادة.",
            "strategy_text_ar": "نشر تجارب الواقع (Before/After)، نصائح سريعة من الخبراء، إعلانات موسمية مستهدفة جغرافياً، وبناء تفاعل يومي.",
            "results_json": "{\"Clinics\": \"Fully Booked\", \"Online Buzz\": \"Active\", \"Bookings\": \"+60%\"}",
            "is_published": true
        },
        {
            "id": 3,
            "priority": 11,
            "title_ar": "إعلانات سناب شات فائقة الدقة لعيادات الكاف للعيون",
            "title_en": "Alkaff Clinics Snapchat Ads",
            "slug": "alkaff-clinics",
            "client_name": "عيادات الكاف",
            "client_logo_url": "assets/images/image11.png",
            "sector": "medical",
            "problem_text_ar": "استهداف المرضى المحتملين للعمليات الدقيقة وتقليل تكلفة العميل المحتمل.",
            "strategy_text_ar": "استهداف مكاني فائق الدقة، استعراض سريع لإجراءات العيون، عدسات سناب شات تفاعلية وأسئلة وأجوبة مع الأطباء.",
            "results_json": "{\"Spend\": \"$2,373\", \"Leads\": \"106 Qualified Leads\", \"LeadCost\": \"$22.39\"}",
            "is_published": true
        },
        {
            "id": 4,
            "priority": 10,
            "title_ar": "بناء مسار تحويل متكامل للدكتورة داليا إسماعيل",
            "title_en": "Dr. Dalia Ismail Full Funnel",
            "slug": "dr-dalia-ismail",
            "client_name": "د. داليا إسماعيل",
            "client_logo_url": "assets/images/image38.png",
            "sector": "medical",
            "problem_text_ar": "بناء مسار تسويقي متكامل (Full Funnel) وتوليد حجوزات مستمرة.",
            "strategy_text_ar": "تصميم صفحات هبوط مخصصة للعروض، ربط الاستمارات بالنظام وواتساب، وإعادة استهداف المهتمين بالمحتوى.",
            "results_json": "{\"Funnel\": \"Full Funnel Flow\", \"Conversions\": \"High CR\", \"Leads\": \"+450 Leads\"}",
            "is_published": true
        },
        {
            "id": 5,
            "priority": 9,
            "title_ar": "بناء العلامة الشخصية للدكتورة مها المعاري",
            "title_en": "Dr. Maha Al Maari Personal Brand",
            "slug": "dr-maha-al-maari",
            "client_name": "د. مها المعاري",
            "client_logo_url": "assets/images/image22.jpg",
            "sector": "medical",
            "problem_text_ar": "بناء علامة شخصية موثوقة (Personal Branding) ونشر الوعي الصحي للأمهات.",
            "strategy_text_ar": "حل مشكلات الأمهات بصيغ تريند هادفة وسرد قصصي حقيقي وتنظيم جدول النشر للوصول المباشر.",
            "results_json": "{\"Views\": \"1M+ Views\", \"Authority\": \"Top Expert\", \"Engagement\": \"+95%\"}",
            "is_published": true
        },
        {
            "id": 6,
            "priority": 8,
            "title_ar": "تحديات التفاعل الوطنية لفرش أسنان usmile",
            "title_en": "usmile Saudi National Challenge",
            "slug": "usmile-saudi",
            "client_name": "علامة U-Smile للأسنان",
            "client_logo_url": "assets/images/image15.jpg",
            "sector": "medical",
            "problem_text_ar": "تحديات تفاعل ومحتوى متزامن مع المناسبات الوطنية في السعودية.",
            "strategy_text_ar": "التعاون مع Nano-Influencers، تشجيع المحتوى المنشأ من المستخدمين (UGC)، ومسابقات واستطلاعات يومية.",
            "results_json": "{\"National Buzz\": \"Saudi Challenge\", \"Smiles\": \"Engaged\", \"UGC\": \"+1.2K Posts\"}",
            "is_published": true
        },
        {
            "id": 7,
            "priority": 7,
            "title_ar": "بناء مجتمع تقويم الأسنان لـ G.U.M السعودية",
            "title_en": "GUM Ortho Community",
            "slug": "gum-saudi",
            "client_name": "علامة GUM للعناية بالفم",
            "client_logo_url": "assets/images/image13.jpg",
            "sector": "medical",
            "problem_text_ar": "بناء مجتمع مخصص لمرتدي تقويم الأسنان وتكريم وتوزيع هدايا.",
            "strategy_text_ar": "تقديم روتين العناية، نصائح خبراء معتمدة، وتوزيع هدايا لتعزيز الولاء للعلامة التجارية.",
            "results_json": "{\"Community\": \"Braces Loyalty\", \"Repeat Customers\": \"+40%\", \"Engagement\": \"High\"}",
            "is_published": true
        },
        {
            "id": 8,
            "priority": 6,
            "title_ar": "حملة تعزيز الوعي والتوسع لعطور أرابيفا",
            "title_en": "Arabiva Fragrances Expansion",
            "slug": "arabiva-fragrances",
            "client_name": "عطور أرابيفا",
            "client_logo_url": "assets/images/image17.jpg",
            "sector": "e-commerce",
            "problem_text_ar": "التوسع والدخول في 4 أسواق إقليمية جديدة خلال 6 أشهر وزيادة مبيعات المتجر الإلكتروني.",
            "strategy_text_ar": "توطين المتجر الإلكتروني لأسواق الخليج، توفير بوابات دفع بالعملات المحلية، وشراكات شحن وتوصيل فائق السرعة وإدارة حملات مؤثرين.",
            "results_json": "{\"ROAS\": \"25x\", \"Growth\": \"+65% YoY\", \"AOV\": \"+22%\", \"Reach\": \"+2M Views\"}",
            "is_published": true
        },
        {
            "id": 9,
            "priority": 5,
            "title_ar": "التوعية بـ بار العطور في ثارب بار",
            "title_en": "Tharb Perfume Bar Launch",
            "slug": "tharb-perfume-bar",
            "client_name": "عطور ذرب",
            "client_logo_url": "assets/images/image20.jpg",
            "sector": "e-commerce",
            "problem_text_ar": "توعية الجمهور بمفهوم \"بار العطور\" ومزج الروائح وتفعيل المتاجر.",
            "strategy_text_ar": "إنتاج محتوى حسي مرئي، تنظيم فعاليات تفاعلية داخل المتاجر، وشراكات مع مؤثرين متخصصين.",
            "results_json": "{\"Scent Bar\": \"Buzz Ignited\", \"Store Traffic\": \"+75%\", \"Curiosity\": \"High\"}",
            "is_published": true
        },
        {
            "id": 10,
            "priority": 4,
            "title_ar": "إطلاق المنتجات في السوق الغربي لـ جينيريك",
            "title_en": "Generic Perfumes Global Reach",
            "slug": "generic-perfumes",
            "client_name": "عطور Generic العالمية",
            "client_logo_url": "assets/images/image19.jpg",
            "sector": "e-commerce",
            "problem_text_ar": "استهداف الجمهور الغربي (أوروبا وأمريكا) والمزج بين الطابع العربي والتصميم العصري.",
            "strategy_text_ar": "السرد القصصي لعالم الروائح، إبراز موثوقية الشحن الدولي، والتعاون مع مؤثرين غربيين متخصصين.",
            "results_json": "{\"Kuwaiti Essence\": \"Global Appeal\", \"US & EU Sales\": \"+50%\", \"International Reach\": \"High\"}",
            "is_published": true
        },
        {
            "id": 11,
            "priority": 3,
            "title_ar": "تأسيس المتجر الإلكتروني ومضاعفة مبيعات فانتازيا للمراتب إلى 10 مليون شهرياً",
            "title_en": "Fantazzia E-Commerce & 10M Sales",
            "slug": "fantazzia-mattress",
            "client_name": "متجر فانتازيا بيد",
            "client_logo_url": "assets/images/image31.png",
            "sector": "b2b_logistics",
            "problem_text_ar": "تأسيس البنية التحتية للتجارة الرقمية والمبيعات من الصفر على مدار 3 سنوات.",
            "strategy_text_ar": "إدارة كاملة للمحتوى والتسويق الرقمي والحملات المدفوعة وأتمتة المبيعات والتنسيق مع الكول سنتر.",
            "results_json": "{\"Store Sales\": \"5M/Mo\", \"Social & CallCenter\": \"5M/Mo\", \"Total Monthly\": \"10M/Mo\", \"ROAS\": \"10x - 20x\"}",
            "is_published": true
        },
        {
            "id": 12,
            "priority": 2,
            "title_ar": "بناء Funnel أوتوماتيكي لشركة VLEX للشحن السريع",
            "title_en": "Vlex Shipping Funnel Automation",
            "slug": "vlex-shipping",
            "client_name": "شركة VLEX للشحن",
            "client_logo_url": "assets/images/VLEX.jpg",
            "sector": "b2b_logistics",
            "problem_text_ar": "تخفيض زمن الاستجابة وتوليد عملاء B2B للمتاجر الإلكترونية في القاهرة والجيزة.",
            "strategy_text_ar": "ربط Google Lead Gen بـ WhatsApp CRM وأتمتة الردود والمتابعات التسويقية الفورية.",
            "results_json": "{\"Leads\": \"+1,000 B2B Leads\", \"ResponseTime\": \"-60%\", \"Funnel\": \"Automated\"}",
            "is_published": true
        },
        {
            "id": 13,
            "priority": 1,
            "title_ar": "التطبيق الطبي الشامل ميديكا هيلثي",
            "title_en": "Medica Healthy Medical App",
            "slug": "medica-healthy-app",
            "client_name": "تطبيق Medica الطبي",
            "client_logo_url": "assets/images/medicahealthy.png",
            "sector": "b2b_logistics",
            "problem_text_ar": "زيادة تحميلات ونسب التفاعل لتطبيق طبي لحجز العيادات والفحوصات والصيدلية.",
            "strategy_text_ar": "محتوى قصصي موجه، إعلانات دقيقة لعيادات الزمالك والقاهرة، وسلسلة نصائح خبراء.",
            "results_json": "{\"App Downloads\": \"+90%\", \"Engagement\": \"+90%\", \"Trust\": \"Earned\"}",
            "is_published": true
        }
    ],
    "digital_products": [
        {
            "id": 1,
            "sort_order": 1,
            "title_ar": "دليل الحملات الإعلانية",
            "title_en": "Performance Ads Playbook",
            "badge_ar": "🥇 الأكثر مبيعاً",
            "badge_en": "🥇 Bestseller Playbook",
            "category": "playbook",
            "price_sar": 199.0,
            "original_price_sar": 499.0,
            "download_url": "assets/files/playbook.pdf",
            "cover_image": "assets/images/image40.png",
            "description_ar": "دليل هيكلة الحملات الإعلانية المدفوعة بأعلى عائد ROAS عبر سناب شات، ميتا، تيك توك وجوجل.",
            "description_en": "The ultimate guide to structuring high-ROAS paid campaigns across Snapchat, Meta, TikTok, and Google Ads.",
            "features": [
                "سكريبتات إعلانية جاهزة للتطبيق المباشر",
                "هيكل صفحة الهبوط المثالية لزيادة التحويل",
                "حاسبة الـ ROAS ونقاط التعادل الإعلانية",
                "تحديثات مجانية مستمرة مدى الحياة"
            ],
            "rating": "4.9/5",
            "is_published": true
        },
        {
            "id": 2,
            "sort_order": 2,
            "title_ar": "حقيبة المتاجر الإلكترونية",
            "title_en": "E-Commerce Growth Masterkit",
            "badge_ar": "🔥 حزمة المتاجر",
            "badge_en": "🔥 Complete Bundle",
            "category": "masterkit",
            "price_sar": 299.0,
            "original_price_sar": 799.0,
            "download_url": "assets/files/ecom-masterkit.pdf",
            "cover_image": "assets/images/image31.png",
            "description_ar": "نظام متكامل لمضاعفة مبيعات المتجر الإلكتروني واستعادة السلات المتروكة ورسائل الواتساب الفورية.",
            "description_en": "Complete system for scaling e-commerce monthly sales and recovering abandoned carts.",
            "features": [
                "مسارات أتمتة الواتساب المباشرة",
                "20 نموذجاً لاستعادة السلات المتروكة",
                "خريطة طريق التوسع في أسوق الخليج",
                "دليل تسعير المنتجات والعروض الترويجية"
            ],
            "rating": "4.9/5",
            "is_published": true
        },
        {
            "id": 3,
            "sort_order": 3,
            "title_ar": "دليل صياغة النصوص الإعلانية",
            "title_en": "Copywriting Frameworks",
            "badge_ar": "⚡️ استراتيجية نمو",
            "badge_en": "⚡️ Growth Strategy",
            "category": "copywriting",
            "price_sar": 149.0,
            "original_price_sar": 399.0,
            "download_url": "assets/files/copywriting.pdf",
            "cover_image": "assets/images/medicahealthy.png",
            "description_ar": "نماذج وسكريبتات مجربة لصياغة الإعلانات عالية التحويل.",
            "description_en": "Proven ad copy frameworks and conversion scripts.",
            "features": [
                "قائمة مراجعة 50 نقطة لفحص تجربة المستخدم",
                "نماذج اختبارات A/B الناجحة",
                "تحليل سلوك الزائرين ونقاط الخروج",
                "أسرار الدفع عند الاستلام وبوابات الدفع"
            ],
            "rating": "4.8/5",
            "is_published": true
        }
    ],
    "services": [
        {
            "id": 1,
            "sort_order": 1,
            "title_ar": "تقييم النشاط التسويقي بالشركات",
            "title_en": "Marketing Audit & Assessment",
            "badge_ar": "🎯 خدمة رئيسية",
            "badge_en": "🎯 Core Service",
            "subtitle_ar": "فحص شامل وإعادة هيكلة للأداء التسويقي والتنظيمي للشركات.",
            "subtitle_en": "Comprehensive marketing audit and organizational scaling.",
            "price_text_ar": "حسب نطاق التقييم",
            "price_text_en": "Custom Quote",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_audit.jpg",
            "is_published": true
        },
        {
            "id": 2,
            "sort_order": 2,
            "title_ar": "التخطيط الاستراتيجي للتسويق",
            "title_en": "Marketing Strategy & Brand Scaling",
            "badge_ar": "👑 استشارية",
            "badge_en": "👑 Consulting",
            "subtitle_ar": "صياغة الرؤية الشاملة وخطة النمو السنوية وتحديد التموضع التنافسي للشركات.",
            "subtitle_en": "Crafting end-to-end growth strategies, annual marketing plans, and market positioning.",
            "price_text_ar": "حسب نطاق المشروع",
            "price_text_en": "Project Based",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_strategy.jpg",
            "is_published": true
        },
        {
            "id": 3,
            "sort_order": 3,
            "title_ar": "تخطيط المحتوى الرقمي",
            "title_en": "Content Strategy & Planning",
            "badge_ar": "⚡️ صناعة محتوى",
            "badge_en": "⚡️ Content Creation",
            "subtitle_ar": "رسم خطط المحتوى الإبداعي والسرد القصصي للعلامات التجارية.",
            "subtitle_en": "Creative content planning and storytelling.",
            "price_text_ar": "شهري / مشروع",
            "price_text_en": "Monthly / Project",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_content_planning.jpg",
            "is_published": true
        },
        {
            "id": 4,
            "sort_order": 4,
            "title_ar": "تصوير وانتاج المحتوى الرقمي",
            "title_en": "Creative Production & Video",
            "badge_ar": "🎬 إنتاج إعلامي",
            "badge_en": "🎬 Production",
            "subtitle_ar": "إخراج وتصوير الإعلانات المرئية والمحتوى الإبداعي لوسائل التواصل.",
            "subtitle_en": "Commercial video production and creative ad shoot.",
            "price_text_ar": "حسب نطاق الإنتاج",
            "price_text_en": "Custom Production",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_production.jpg",
            "is_published": true
        },
        {
            "id": 5,
            "sort_order": 5,
            "title_ar": "إدارة الحملات الإعلانية",
            "title_en": "Performance Advertising & Media Buying",
            "badge_ar": "🎯 أداء وإعلانات",
            "badge_en": "🎯 Performance",
            "subtitle_ar": "إدارة الحملات المدفوعة وتحقيق أعلى عائد على الإنفاق الإعلاني ROAS.",
            "subtitle_en": "Scaling paid campaigns across Meta, Snapchat, TikTok & Google.",
            "price_text_ar": "نسبة / ميزانية",
            "price_text_en": "Budget %",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_campaigns.jpg",
            "is_published": true
        },
        {
            "id": 6,
            "sort_order": 6,
            "title_ar": "تطوير تطبيقات الويب",
            "title_en": "Web Development & E-Commerce",
            "badge_ar": "💻 تطوير برمجي",
            "badge_en": "💻 Web & Tech",
            "subtitle_ar": "بناء وتطوير المتاجر الإلكترونية وصفحات الهبوط فائقة السرعة والتحويل.",
            "subtitle_en": "Building e-commerce platforms and high-converting landing pages.",
            "price_text_ar": "حسب حجم التطبيق",
            "price_text_en": "Project Based",
            "cta_text_ar": "📅 احجز جلسة استشارية",
            "cta_text_en": "📅 Book Strategy Session",
            "cta_link": "contact.html",
            "image_url": "assets/images/service_web_dev.jpg",
            "is_published": true
        }
    ],
    "courses": [
        {
                "id": 1,
                "sort_order": 1,
                "title_ar": "ماستر كلاس الشراء الإعلاني",
                "title_en": "Media Buying Masterclass",
                "badge_ar": "🔥 برنامج قيادي متقدم",
                "badge_en": "🔥 Executive Program",
                "description_ar": "استراتيجيات قيادة الحملات الضخمة وتجاوز عقبات الخوارزميات وتحقيق أرقام مضاعفة حتى 25x.",
                "description_en": "Managing massive ad budgets, navigating algorithm updates, and scaling return on ad spend up to 25x.",
                "duration_ar": "4 أسابيع (16 ساعة)",
                "duration_en": "4 Weeks (16 Hrs)",
                "original_price_ar": "2,999 ﷼",
                "original_price_en": "2,999 SAR",
                "price_ar": "1,499 ﷼",
                "price_en": "1,499 SAR",
                "image_url": "assets/images/image45.jpg",
                "is_published": true
        },
        {
                "id": 2,
                "sort_order": 2,
                "title_ar": "توطين العلامات بالسوق السعودي",
                "title_en": "Brand Localization in Saudi Market",
                "badge_ar": "⚡ استراتيجيات الخليج",
                "badge_en": "⚡ GCC Market Entry",
                "description_ar": "آليات الدخول السلس والتكيف الثقافي والديموغرافي لرفع نسبة الاستحواذ على حصة السوق.",
                "description_en": "Seamless entry frameworks, cultural adaptation, and demographic targeting for maximum market share.",
                "duration_ar": "يومان (6 ساعات)",
                "duration_en": "2 Days (6 Hrs)",
                "original_price_ar": "1,599 ﷼",
                "original_price_en": "1,599 SAR",
                "price_ar": "799 ﷼",
                "price_en": "799 SAR",
                "image_url": "assets/images/image50.jpg",
                "is_published": true
        },
        {
                "id": 3,
                "sort_order": 3,
                "title_ar": "هندسة مسارات التحويل",
                "title_en": "Conversion Funnel Engineering",
                "badge_ar": "🛠️ التجارة الإلكترونية",
                "badge_en": "🛠️ E-Commerce Optimization",
                "description_ar": "تحليل سلوك المستهلك وتصميم المتاجر عالية التحويل باستخدام التحليلات المتقدمة.",
                "description_en": "Consumer behavior analysis, high-converting checkout flows, and analytics optimization.",
                "duration_ar": "أسبوعان (8 ساعات)",
                "duration_en": "2 Weeks (8 Hrs)",
                "original_price_ar": "1,999 ﷼",
                "original_price_en": "1,999 SAR",
                "price_ar": "999 ﷼",
                "price_en": "999 SAR",
                "image_url": "assets/images/image52.jpg",
                "is_published": true
        }
],
    "youtube_shorts": [
        {
            "id": 1,
            "priority": 3,
            "title": "استراتيجية مضاعفة العائد على الإعلانات ROAS",
            "youtube_video_id": "dQw4w9WgXcQ",
            "views_count": "120K+",
            "likes_count": "8.5K",
            "is_published": true
        },
        {
            "id": 2,
            "priority": 2,
            "title": "كيف ترسم رحلة العميل وتزيل عقبات التحويل",
            "youtube_video_id": "3JZ_D3ELwOQ",
            "views_count": "85K+",
            "likes_count": "5.2K",
            "is_published": true
        },
        {
            "id": 3,
            "priority": 1,
            "title": "أهم 3 أخطاء في إعلانات سناب شات للعيادات",
            "youtube_video_id": "L_LUpnjgPso",
            "views_count": "45K+",
            "likes_count": "3.1K",
            "is_published": true
        }
    ],
    "articles": [
        {
            "id": 1,
            "priority": 2,
            "title_ar": "منهجية تحسين معدلات التحويل CRO للمتاجر الإلكترونية في الخليج",
            "title_en": "E-commerce CRO Strategies in GCC",
            "slug": "cro-strategies-gcc",
            "content_ar": "تعد صياغة مسار تحويل مرن من أهم محركات النمو للتجارة الإلكترونية العابرة للحدود في سوق الخليج، في هذا المقال نستعرض تجارب واقعية لرفع معدل التحويل لـ 52%...",
            "linkedin_url": "https://linkedin.com/in/hsn96srhan",
            "created_at": "2026-09-27",
            "is_published": true
        }
    ],
    "bank_orders": [],
    "bookings": [],
    "users": [
        {
            "id": 1,
            "username": "admin",
            "email": "hsn96srhan@gmail.com",
            "role": "admin",
            "status": "active"
        },
        {
            "id": 2,
            "username": "seouser",
            "email": "seo@hsn.agency",
            "role": "seo",
            "status": "active"
        }
    ],
    "landing_pages": [
        {
            "id": 1,
            "title_ar": "منظومة النمو الشاملة واستراتيجيات الشراء الإعلاني",
            "title_en": "Enterprise Growth & Scaled Media Buying",
            "slug": "enterprise-growth",
            "meta_description_ar": "صفحة هبوط مخصصة لمضاعفة عائد الإعلانات وهندسة مسارات التحويل للمتاجر والشركات الكبرى.",
            "is_published": true,
            "blocks_json": JSON.stringify([
                {
                    "type": "hero",
                    "badge_ar": "🔥 برنامج استشاري قيادي حصري",
                    "badge_en": "🔥 Exclusive Growth Program",
                    "title_ar": "ضاعف عائد إعلاناتك واستحوذ على حصة السوق السعودي والخليجي",
                    "title_en": "Scale Your ROAS & Master GCC Market Acquisition",
                    "subtitle_ar": "استراتيجيات الشراء الإعلاني المتقدمة وهندسة مسارات التحويل للمتاجر والشركات الكبرى لتحقيق أعلى عائد استثماري مثبت.",
                    "subtitle_en": "Advanced media buying frameworks and high-converting funnel engineering for enterprise scaling.",
                    "primary_cta_text_ar": "🚀 احجز جلسة الاستراتيجية مجاناً",
                    "primary_cta_link": "#book",
                    "secondary_cta_text_ar": "💬 استفسر عبر الواتساب",
                    "secondary_cta_link": "https://wa.me/966500000000",
                    "trust_note_ar": "⚡ نتائج ملموسة وضمان ذهبي على الأداء والنمو"
                },
                {
                    "type": "problems",
                    "title_ar": "هل تعاني من هذه التحديات في حملاتك التسويقية الحالية؟",
                    "subtitle_ar": "أبرز العوائق التي تمنع المتاجر والشركات من الوصول لربحية عالية وتستنزف الميزانيات الإعلانية.",
                    "items": [
                        { "icon": "⚠️", "title_ar": "ارتفاع تكلفة الاستحواذ (CAC)", "desc_ar": "تضخم تكلفة العميل وضياع الميزانيات بدون تحقيق عائد مجدٍ على الإعلانات." },
                        { "icon": "📉", "title_ar": "ضعف نسبة التحويل (CR)", "desc_ar": "زيارات ضخمة للمتجر دون وجود مسار تحويل مدروس يحفز الزائر على الشراء الفوري." },
                        { "icon": "🔄", "title_ar": "تخبط الخوارزميات وتراجع النتائج", "desc_ar": "التأثر السلبي بالتحديثات المستمرة لإعلانات تيك توك وميتا وجوجل بدون استراتيجية بديلة." }
                    ]
                },
                {
                    "type": "solution",
                    "title_ar": "الحل الاستراتيجي: منظومة النمو والتحويل المباشر",
                    "subtitle_ar": "منهجية متكاملة تعتمد على البيانات الحية والتحليل العميق لتجاوز عقبات الخوارزميات وتحقيق قفزات نوعية في المبيعات.",
                    "items": [
                        { "icon": "🎯", "title_ar": "استهداف دقيق للمشتري الجاهز", "desc_ar": "توجيه الحملات للشرائح ذات القدرة الشرائية العالية والمهتمة بالمنتج فعلياً." },
                        { "icon": "🛠️", "title_ar": "هندسة صفحة الهبوط والـ Funnel", "desc_ar": "تأسيس صفحات هبوط عالية السلاسة تزيد من معدلات إغلاق الصفقة والتأكيد." },
                        { "icon": "📈", "title_ar": "تحسين مستمر مستند إلى ROI", "desc_ar": "إدارة الميزانيات بدقة متناهية وإعادة توزيع الإنفاق على الحملات الأكثر ربحية." }
                    ]
                },
                {
                    "type": "features",
                    "title_ar": "المميزات التنافسية التي تضمن نجاح حملتك",
                    "subtitle_ar": "نقدم لك حلولاً سريعة ومثبتة تضع عملك في صدارة المنافسين.",
                    "items": [
                        { "icon": "⚡", "title_ar": "إطلاق سريع وتجهيز شامل", "desc_ar": "تجهيز الحملات وصفحات الهبوط خلال أيام معدودة لبدء جني النتائج." },
                        { "icon": "🛡️", "title_ar": "إدارة مخاطر وميزانيات محكمة", "desc_ar": "عدم إهدار أي ريال إعلاني بدون اختبار علمي وتتبع دقيق للعائد." },
                        { "icon": "📱", "title_ar": "توافق كامل مع الجوال والواتساب", "desc_ar": "تجربة مستخدم سريعة تتيح إتمام الطلب والتحويل بضغطة زر واحدة." },
                        { "icon": "📊", "title_ar": "تقارير أداء ومتابعة قيادية", "desc_ar": "لوحة تحكم شفافة تظهر لك كافة المؤشرات ومعدلات النمو لحظة بلحظة." }
                    ]
                },
                {
                    "type": "process",
                    "title_ar": "آلية العمل في 4 خطوات بسيطة ومنظمة",
                    "subtitle_ar": "مسار واضح يضمن الانتقال من مرحلة الدراسة إلى مرحلة الأرباح العالية.",
                    "steps": [
                        { "num": "01", "title_ar": "التحليل والتشخيص الشامل", "desc_ar": "دراسة الوضع الحالي وحساب تكلفة الاستحواذ وتحديد الفجوات." },
                        { "num": "02", "title_ar": "بناء الاستراتيجية والـ Funnel", "desc_ar": "صياغة الرسائل الإعلانية وتجهيز صفحات الهبوط عالية التحويل." },
                        { "num": "03", "title_ar": "إطلاق الحملات والاختبار المكثف", "desc_ar": "توزيع الميزانيات واختبار الزوايا الإعلانية الأكثر تأثيراً." },
                        { "num": "04", "title_ar": "التوسع والنمو المضاعف (Scaling)", "desc_ar": "زيادة الإنفاق على الأقسام الرابحة ومضاعفة العائد الاستثماري." }
                    ]
                },
                {
                    "type": "case_studies",
                    "title_ar": "أرقام وإنجازات مثبتة على أرض الواقع",
                    "subtitle_ar": "نتائج حقيقية تم تحقيقها في حملات الشراء الإعلاني واستشارات النمو.",
                    "stats": [
                        { "number": "+350%", "label_ar": "نمو في معدل العائد على الإعلانات (ROAS)" },
                        { "number": "+14M SAR", "label_ar": "إجمالي ميزانيات إعلانية تم إدارتها بنجاح" },
                        { "number": "98%", "label_ar": "نسبة رضا واستمرار المتاجر والعملاء" },
                        { "number": "+25x", "label_ar": "أعلى عائد محقق على حملة خاصة" }
                    ]
                },
                {
                    "type": "testimonials",
                    "title_ar": "ماذا يقول رؤساء ومدراء التسويق عن خدماتنا؟",
                    "subtitle_ar": "شهادات وثقة قيادات الأعمال والمتاجر الإلكترونية في السعودية والخليج.",
                    "items": [
                        { "name_ar": "عبد الله الشمري", "role_ar": "رئيس مجلس إدارة متجر الكتروني", "quote_ar": "التحول الذي شهدناه في نتائج الحملات كان استثنائياً، ارتفع عائد الإعلانات من 1.8x إلى أكثر من 4.2x في أقل من شهرين.", "stars": 5 },
                        { "name_ar": "فهد السبيعي", "role_ar": "مدير تسويق شركة تجارة مكملات", "quote_ar": "الاحترافية في صياغة الزوايا الإعلانية وتصميم صفحات الهبوط جعلت معدل التحويل يرتفع بشكل ملحوظ جداً.", "stars": 5 },
                        { "name_ar": "سارة الخالدي", "role_ar": "مؤسس براند عطور سعودي", "quote_ar": "دقة الاستهداف والتوجيه المستمر حول الميزانيات وفرت علينا مبالغ ضخمة وحققت مبيعات قياسية.", "stars": 5 }
                    ]
                },
                {
                    "type": "guarantee",
                    "title_ar": "🛡️ الضمان الذهبي لتقليل المخاطر 100%",
                    "subtitle_ar": "نحن واثقون من جودة المنظومة والنتائج، لذلك نضمن لك الالتزام الكامل بالتنفيذ وفق أعلى معايير الجودة والمتابعة المستمرة حتى تحقيق الأهداف المتفق عليها."
                },
                {
                    "type": "pricing",
                    "title_ar": "عرض باقة النمو والاستثمار الاستراتيجي",
                    "subtitle_ar": "باقة متكاملة تغطي كافة متطلبات التحليلات والحملات وصفحات الهبوط.",
                    "package_name_ar": "باقة التوسع القيادي (Scale Package)",
                    "price_ar": "4,999 ﷼",
                    "original_price_ar": "9,999 ﷼",
                    "discount_badge_ar": "🔥 خصم 50% لفترة محدودة",
                    "features": [
                        "إدارة شاملة لحملات الشراء الإعلاني (TikTok, Meta, Google)",
                        "تأسيس وتجهيز صفحات هبوط سريعة عالية التحويل",
                        "صياغة 5 زوايا إعلانية ابتكارية للمنتج",
                        "لوحة تحكم وتتبع لحظي لمعدل ROI",
                        "جلسات استشارية أسبوعية وتطوير مستمر"
                    ],
                    "cta_text_ar": "💳 اشترك الآن واستفد من العرض"
                },
                {
                    "type": "faq_cta",
                    "title_ar": "الأسئلة الشائعة واحجز استشارتك الآن",
                    "subtitle_ar": "إجابات على أهم الاستفسارات قبل البدء في رحلة النمو.",
                    "faqs": [
                        { "q_ar": "كم يستغرق تجهيز صفحة الهبوط والحملات؟", "a_ar": "يتم تجهيز كافة الأصول وإطلاق الحملات خلال 3 إلى 5 أيام عمل فقط." },
                        { "q_ar": "هل الباقة مناسبة للمتاجر الجديدة أم القائمة؟", "a_ar": "مناسبة للمتاجر والشركات القائمة التي ترغب في مضاعفة مبيعاتها وتقليل تكلفة الاستحواذ." },
                        { "q_ar": "كيف يتم متابعة النتائج والأداء؟", "a_ar": "من خلال لوحة تحكم مخصصة وتقارير أسبوعية تجمع كافة المؤشرات والتحليلات." }
                    ],
                    "final_cta_title_ar": "🚀 هل أنت جاهز لمضاعفة مبيعات متجرك الآن؟",
                    "final_cta_btn_ar": "📲 احجز موعدك وتواصل مباشرة عبر الواتساب"
                }
            ])
        }
    ]
};

// Data Helper Functions for JS engine
function getMasterData(key) {
    try {
        const stored = localStorage.getItem("HSN_MASTER_DATA_" + key);
        if (stored) {
            return JSON.parse(stored);
        }
    } catch (e) {}
    return DEFAULT_SITE_DATA[key] || null;
}

function setMasterData(key, data) {
    try {
        localStorage.setItem("HSN_MASTER_DATA_" + key, JSON.stringify(data));
    } catch (e) {}
}

function initMasterData() {
    const DATA_VERSION = "2026.10.03.v102_LANDING_PAGES_BUILDER";
    if (localStorage.getItem("HSN_DATA_VERSION") !== DATA_VERSION) {
        Object.keys(DEFAULT_SITE_DATA).forEach(key => {
            localStorage.setItem("HSN_MASTER_DATA_" + key, JSON.stringify(DEFAULT_SITE_DATA[key]));
        });
        localStorage.setItem("HSN_DATA_VERSION", DATA_VERSION);
    } else {
        Object.keys(DEFAULT_SITE_DATA).forEach(key => {
            if (!localStorage.getItem("HSN_MASTER_DATA_" + key)) {
                localStorage.setItem("HSN_MASTER_DATA_" + key, JSON.stringify(DEFAULT_SITE_DATA[key]));
            }
        });
    }
}

// Auto init on script load
initMasterData();
