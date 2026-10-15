/**
 * HSN SRHAN Portfolio - Master Interactive JS & Booking & Language System
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Lightbox Image Modal System (100% Full Uncropped Image View)
    let modalOverlay = document.querySelector('.image-modal-overlay');
    if (!modalOverlay) {
        modalOverlay = document.createElement('div');
        modalOverlay.className = 'image-modal-overlay';
        modalOverlay.innerHTML = `
            <div class="image-modal-container">
                <button class="image-modal-close" aria-label="Close">&times;</button>
                <img src="" alt="Full Resolution View" class="image-modal-img">
                <div class="image-modal-caption"></div>
            </div>
        `;
        document.body.appendChild(modalOverlay);
    }

    const modalImg = modalOverlay.querySelector('.image-modal-img');
    const modalCaption = modalOverlay.querySelector('.image-modal-caption');
    const closeBtn = modalOverlay.querySelector('.image-modal-close');

    const openModal = (src, alt) => {
        modalImg.src = src;
        modalCaption.innerText = alt || 'معاينة كاملة 100%';
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => { modalImg.src = ''; }, 300);
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });

    // Attach click listener to all certificate & project cover images
    const zoomableImages = document.querySelectorAll('.img-card-cover, .zoomable-img');
    zoomableImages.forEach(img => {
        img.title = 'اضغط للعرض والتحقق الكامل 100%';
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => {
            openModal(img.src, img.alt);
        });
    });

    // 2. Interactive Booking Calendar & Time Slots System (Services Page)
    const bookingForm = document.getElementById('consultation_booking_form');
    const dateInput = document.getElementById('booking_date_picker');
    const timeSlotsGrid = document.getElementById('time_slots_grid');
    const selectedSlotInput = document.getElementById('selected_time_slot');
    const bookingAlert = document.getElementById('booking_alert');

    // Storage key for local booked slots
    const BOOKINGS_KEY = 'sarhan_consultation_bookings';

    const getBookings = () => {
        try {
            return JSON.parse(localStorage.getItem(BOOKINGS_KEY)) || {};
        } catch(e) {
            return {};
        }
    };

    const saveBookingLocally = (dateStr, slotStr) => {
        const bookings = getBookings();
        if (!bookings[dateStr]) bookings[dateStr] = [];
        if (!bookings[dateStr].includes(slotStr)) {
            bookings[dateStr].push(slotStr);
        }
        localStorage.setItem(BOOKINGS_KEY, JSON.stringify(bookings));
    };

    // Slot definitions mapping
    const SLOTS = [
        { ar: '6:00 مساءً', en: '6:00 PM', keys: ['6:00 مساءً', '6:00 PM', '6:00pm'] },
        { ar: '7:00 مساءً', en: '7:00 PM', keys: ['7:00 مساءً', '7:00 PM', '7:00pm'] },
        { ar: '8:00 مساءً', en: '8:00 PM', keys: ['8:00 مساءً', '8:00 PM', '8:00pm'] }
    ];

    const isSlotBooked = (bookedList, slotConfig) => {
        if (!bookedList || !bookedList.length) return false;
        return bookedList.some(b => slotConfig.keys.includes(String(b).trim()));
    };

    // Helper to update slots UI according to current date & current language
    const updateSlotsUI = () => {
        if (!timeSlotsGrid || !dateInput) return;
        const currentLang = document.documentElement.getAttribute('lang') || 'ar';
        const dateStr = dateInput.value;
        const slotBtns = timeSlotsGrid.querySelectorAll('.time-slot-btn');

        if (!dateStr) {
            // No date chosen yet, render slots normally
            slotBtns.forEach((btn, index) => {
                const config = SLOTS[index] || { ar: btn.getAttribute('data-slot') || btn.innerText, en: btn.getAttribute('data-en') || btn.innerText };
                btn.innerText = currentLang === 'en' ? config.en : config.ar;
                btn.classList.remove('disabled');
                btn.disabled = false;
            });
            return;
        }

        const chosenDate = new Date(dateStr);
        const dayOfWeek = chosenDate.getUTCDay(); // 5 = Fri, 6 = Sat

        if (dayOfWeek === 5 || dayOfWeek === 6) {
            // Weekend off
            dateInput.style.borderColor = '#DC2626';
            dateInput.style.backgroundColor = '#FEF2F2';
            bookingAlert.innerText = currentLang === 'en' 
                ? '⚠️ Sorry, Friday and Saturday are weekend off days. Please choose a working day (Sun - Thu).'
                : '⚠️ عذراً، الجمعة والسبت إجازة أسبوعية. يرجى اختيار يوم عمل آخر (الأحد - الخميس).';
            bookingAlert.className = 'booking-alert warning';
            bookingAlert.style.display = 'block';

            slotBtns.forEach((btn, index) => {
                const config = SLOTS[index] || { ar: 'الموعد', en: 'Slot' };
                btn.innerText = currentLang === 'en' ? `${config.en} (Off)` : `${config.ar} (إجازة)`;
                btn.classList.add('disabled');
                btn.disabled = false;
            });
            return;
        } else {
            dateInput.style.borderColor = 'var(--border-color)';
            dateInput.style.backgroundColor = '#FFFFFF';
        }

        const bookings = getBookings();
        const bookedSlotsForDate = bookings[dateStr] || [];

        // Check if daily limit (3 bookings) reached
        if (bookedSlotsForDate.length >= 3) {
            bookingAlert.innerText = currentLang === 'en'
                ? '⚠️ Sorry, all 3 available slots for this date are fully booked. Please select another date.'
                : '⚠️ عذراً، تم اكتمال كافة المواعيد المتاحة لهذا اليوم (الحد الأقصى 3 مواعيد يومياً). يرجى اختيار تاريخ آخر.';
            bookingAlert.className = 'booking-alert warning';
            bookingAlert.style.display = 'block';

            slotBtns.forEach((btn, index) => {
                const config = SLOTS[index] || { ar: 'الموعد', en: 'Slot' };
                btn.innerText = currentLang === 'en' ? `${config.en} (Booked)` : `${config.ar} (محجوز)`;
                btn.classList.add('disabled');
                btn.disabled = false;
            });
            return;
        }

        // Check individual slot booked status
        slotBtns.forEach((btn, index) => {
            const config = SLOTS[index] || { ar: btn.getAttribute('data-slot') || btn.innerText, en: btn.getAttribute('data-en') || btn.innerText };
            const booked = isSlotBooked(bookedSlotsForDate, config);

            if (booked) {
                btn.classList.add('disabled');
                btn.disabled = false; // keep clickable to show notification on click
                btn.innerText = currentLang === 'en' ? `${config.en} (Booked)` : `${config.ar} (محجوز)`;
            } else {
                btn.classList.remove('disabled');
                btn.disabled = false;
                btn.innerText = currentLang === 'en' ? config.en : config.ar;
            }
        });
    };

    if (dateInput && timeSlotsGrid) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;

        const datePlaceholder = document.getElementById('date_placeholder_text');
        const toggleDatePlaceholder = () => {
            if (dateInput) {
                if (dateInput.value) {
                    dateInput.classList.remove('is-empty');
                    if (datePlaceholder) datePlaceholder.style.display = 'none';
                } else {
                    dateInput.classList.add('is-empty');
                    if (datePlaceholder) datePlaceholder.style.display = 'flex';
                }
            }
        };

        dateInput.addEventListener('change', toggleDatePlaceholder);
        dateInput.addEventListener('input', toggleDatePlaceholder);
        toggleDatePlaceholder();

        dateInput.addEventListener('change', () => {
            toggleDatePlaceholder();
            bookingAlert.style.display = 'none';
            bookingAlert.className = 'booking-alert';
            selectedSlotInput.value = '';
            const slotBtns = timeSlotsGrid.querySelectorAll('.time-slot-btn');
            slotBtns.forEach(btn => btn.classList.remove('selected'));

            const dateStr = dateInput.value;
            if (dateStr) {
                fetch(`api/booking.php?date=${encodeURIComponent(dateStr)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.status === 'success' && Array.isArray(data.booked_slots)) {
                            const bookings = getBookings();
                            bookings[dateStr] = Array.from(new Set([...(bookings[dateStr] || []), ...data.booked_slots]));
                            localStorage.setItem(BOOKINGS_KEY, JSON.stringify(bookings));
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                        updateSlotsUI();
                    });
            } else {
                updateSlotsUI();
            }
        });

        // Slot selection click listener
        const slotBtns = timeSlotsGrid.querySelectorAll('.time-slot-btn');
        slotBtns.forEach((btn, index) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const currentLang = document.documentElement.getAttribute('lang') || 'ar';
                
                // If slot is disabled / booked
                if (btn.classList.contains('disabled')) {
                    bookingAlert.innerText = currentLang === 'en'
                        ? '⚠️ This slot is booked, please select another time'
                        : '⚠️ الموعد محجوز، اختر موعد آخر';
                    bookingAlert.className = 'booking-alert warning';
                    bookingAlert.style.display = 'block';
                    return;
                }

                // If available slot clicked
                bookingAlert.style.display = 'none';
                bookingAlert.className = 'booking-alert';

                slotBtns.forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');

                const config = SLOTS[index];
                selectedSlotInput.value = config ? config.ar : btn.getAttribute('data-slot');
            });
        });
    }

    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const currentLang = document.documentElement.getAttribute('lang') || 'ar';
            const dateStr = dateInput ? dateInput.value : '';
            const slotStr = selectedSlotInput ? selectedSlotInput.value : '';

            if (!dateStr) {
                bookingAlert.innerText = currentLang === 'en'
                    ? '⚠️ Please select a date for the consultation session.'
                    : '⚠️ يرجى اختيار تاريخ الجلسة الاستشارية.';
                bookingAlert.className = 'booking-alert warning';
                bookingAlert.style.display = 'block';
                return;
            }
            if (!slotStr) {
                bookingAlert.innerText = currentLang === 'en'
                    ? '⚠️ Please select your preferred time slot (6:00 PM, 7:00 PM, or 8:00 PM).'
                    : '⚠️ يرجى اختيار الموعد المفضل (6:00 مساءً، 7:00 مساءً، أو 8:00 مساءً).';
                bookingAlert.className = 'booking-alert warning';
                bookingAlert.style.display = 'block';
                return;
            }

            // Verify if slot is booked
            const bookings = getBookings();
            const bookedSlotsForDate = bookings[dateStr] || [];
            const slotObj = SLOTS.find(s => s.ar === slotStr || s.en === slotStr || s.keys.includes(slotStr));
            if (slotObj && isSlotBooked(bookedSlotsForDate, slotObj)) {
                bookingAlert.innerText = currentLang === 'en'
                    ? '⚠️ This slot is booked, please select another time'
                    : '⚠️ الموعد محجوز، اختر موعد آخر';
                bookingAlert.className = 'booking-alert warning';
                bookingAlert.style.display = 'block';
                updateSlotsUI();
                return;
            }

            if (bookedSlotsForDate.length >= 3) {
                bookingAlert.innerText = currentLang === 'en'
                    ? '⚠️ Sorry, all 3 available slots for this date are fully booked. Please select another date.'
                    : '⚠️ عذراً، تم اكتمال كافة المواعيد المتاحة لهذا اليوم (الحد الأقصى 3 مواعيد يومياً). يرجى اختيار تاريخ آخر.';
                bookingAlert.className = 'booking-alert warning';
                bookingAlert.style.display = 'block';
                updateSlotsUI();
                return;
            }

            // Collect form input values
            const inputs = bookingForm.querySelectorAll('input, select, textarea');
            const formData = {
                booking_date: dateStr,
                time_slot: slotStr,
                full_name: '',
                phone: '',
                email: '',
                company_name: '',
                notes: ''
            };

            inputs.forEach(inp => {
                const ph = (inp.placeholder || '').toLowerCase();
                if (inp.type === 'text' && (ph.includes('اسمك') || ph.includes('name'))) formData.full_name = inp.value;
                if (inp.type === 'tel') formData.phone = inp.value;
                if (inp.type === 'email') formData.email = inp.value;
                if (inp.type === 'text' && (ph.includes('علامتك') || ph.includes('brand') || ph.includes('company'))) formData.company_name = inp.value;
                if (inp.tagName === 'TEXTAREA') formData.notes = inp.value;
            });

            // Save locally
            saveBookingLocally(dateStr, slotStr);
            updateSlotsUI();

            // Save to backend API
            fetch('api/booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            }).catch(() => {});

            // Display success message
            const displaySlotText = currentLang === 'en' && slotObj ? slotObj.en : slotStr;
            bookingAlert.innerText = currentLang === 'en'
                ? `✅ Your appointment has been booked successfully for ${dateStr} at ${displaySlotText}! We will contact you soon to confirm details.`
                : `✅ تم تأكيد حجزك بنجاح يوم ${dateStr} الساعة ${displaySlotText}! سيتم التواصل معك لتأكيد التفاصيل.`;
            bookingAlert.className = 'booking-alert success';
            bookingAlert.style.display = 'block';

            bookingForm.reset();
            selectedSlotInput.value = '';
            if (timeSlotsGrid) {
                const btns = timeSlotsGrid.querySelectorAll('.time-slot-btn');
                btns.forEach(b => b.classList.remove('selected'));
            }
        });
    }

    // Service Booking Buttons Click Handler
    document.querySelectorAll('.service-booking-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const serviceName = this.getAttribute('data-service');
            const serviceSelect = document.getElementById('booking_service_select');
            if (serviceSelect && serviceName) {
                serviceSelect.value = serviceName;
            }
            const bookSection = document.getElementById('book');
            if (bookSection) {
                bookSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Date Picker Container Click Trigger (Opens native calendar dropdown on click anywhere)
    document.querySelectorAll('.date-picker-container').forEach(container => {
        container.addEventListener('click', function(e) {
            const input = container.querySelector('input[type="date"]');
            if (input) {
                try {
                    if (typeof input.showPicker === 'function') {
                        input.showPicker();
                    } else {
                        input.focus();
                        input.click();
                    }
                } catch (err) {
                    input.focus();
                }
            }
        });
    });

    // 3. 100% Arabic / 100% English Dynamic Language Engine
    const langBtns = document.querySelectorAll('.btn-lang, .mobile-lang-btn');
    
    const applyLanguage = (lang) => {
        localStorage.setItem('site_lang', lang);
        if (lang === 'en') {
            document.documentElement.setAttribute('lang', 'en');
            document.documentElement.setAttribute('dir', 'ltr');
            langBtns.forEach(btn => {
                btn.innerHTML = '🇸🇦 AR';
                btn.setAttribute('href', '?lang=ar');
            });
            
            // Translate all text elements with data-en attribute
            document.querySelectorAll('[data-en]').forEach(elem => {
                if (!elem.hasAttribute('data-ar')) {
                    const currentText = (elem.textContent || '').trim();
                    if (currentText) {
                        elem.setAttribute('data-ar', currentText);
                    }
                }
                const enText = elem.getAttribute('data-en');
                if (enText) {
                    elem.textContent = enText;
                }
            });

            // Translate option elements in select dropdowns
            document.querySelectorAll('select option[data-en]').forEach(opt => {
                if (!opt.hasAttribute('data-ar')) {
                    const optText = (opt.textContent || '').trim();
                    if (optText) {
                        opt.setAttribute('data-ar', optText);
                    }
                }
                const enText = opt.getAttribute('data-en');
                if (enText) {
                    opt.textContent = enText;
                }
            });

            // Translate all input & textarea placeholders with data-en-placeholder
            document.querySelectorAll('[data-en-placeholder]').forEach(elem => {
                if (!elem.hasAttribute('data-ar-placeholder')) {
                    elem.setAttribute('data-ar-placeholder', elem.placeholder || '');
                }
                const enPh = elem.getAttribute('data-en-placeholder');
                if (enPh) elem.placeholder = enPh;
            });

            const datePicker = document.getElementById('booking_date_picker');
            const datePlaceholder = document.getElementById('date_placeholder_text');
            if (datePicker) {
                datePicker.setAttribute('lang', 'en');
                datePicker.style.textAlign = 'left';
            }
            if (datePlaceholder) {
                datePlaceholder.textContent = '📅 day / month / year';
            }
        } else {
            document.documentElement.setAttribute('lang', 'ar');
            document.documentElement.setAttribute('dir', 'rtl');
            langBtns.forEach(btn => {
                btn.innerHTML = '🇬🇧 EN';
                btn.setAttribute('href', '?lang=en');
            });

            // Restore Arabic text elements
            document.querySelectorAll('[data-en]').forEach(elem => {
                if (elem.hasAttribute('data-ar')) {
                    const arText = elem.getAttribute('data-ar');
                    if (arText) elem.textContent = arText;
                }
            });

            // Restore Arabic select options
            document.querySelectorAll('select option[data-en]').forEach(opt => {
                if (opt.hasAttribute('data-ar')) {
                    const arText = opt.getAttribute('data-ar');
                    if (arText) opt.textContent = arText;
                }
            });

            // Restore Arabic placeholders
            document.querySelectorAll('[data-en-placeholder]').forEach(elem => {
                if (elem.hasAttribute('data-ar-placeholder')) {
                    elem.placeholder = elem.getAttribute('data-ar-placeholder');
                }
            });

            const datePicker = document.getElementById('booking_date_picker');
            const datePlaceholder = document.getElementById('date_placeholder_text');
            if (datePicker) {
                datePicker.setAttribute('lang', 'ar');
                datePicker.style.textAlign = 'right';
            }
            if (datePlaceholder) {
                datePlaceholder.innerText = '📅 يوم / شهر / سنة';
            }
        }

        // Update time slots UI & any active alert text on language switch
        updateSlotsUI();
    };

    // Determine default language: URL param -> localStorage -> Browser Default Language
    const getInitialLanguage = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const langParam = urlParams.get('lang');
        if (langParam === 'en' || langParam === 'ar') {
            return langParam;
        }
        const savedLang = localStorage.getItem('site_lang');
        if (savedLang === 'en' || savedLang === 'ar') {
            return savedLang;
        }
        const navLang = (navigator.language || navigator.userLanguage || '').toLowerCase();
        if (navLang.startsWith('en')) {
            return 'en';
        }
        return 'ar';
    };

    if (langBtns.length > 0) {
        langBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // If pure HTML page without PHP reload, prevent link jump and toggle JS
                if (window.location.protocol === 'file:' || !window.location.pathname.endsWith('.php')) {
                    e.preventDefault();
                    const currentLang = document.documentElement.getAttribute('lang') || 'ar';
                    const newLang = currentLang === 'ar' ? 'en' : 'ar';
                    applyLanguage(newLang);
                }
            });
        });

        // Initialize site language
        const initialLang = getInitialLanguage();
        applyLanguage(initialLang);
    }

    // 4. Hero YouTube & Local Video Play/Pause & Audio Handler
    const heroVideoCard = document.getElementById('video_hero_card');
    const heroVideo = document.getElementById('hero_video_player');
    const ytIframe = document.getElementById('youtube_hero_player');
    const playBtn = document.getElementById('video_play_btn');
    const clickOverlay = document.getElementById('video_click_overlay');

    let isPlayingWithAudio = false;

    function startVideoWithSound() {
        if (ytIframe) {
            ytIframe.contentWindow.postMessage('{"event":"command","func":"unMute","args":""}', '*');
            ytIframe.contentWindow.postMessage('{"event":"command","func":"setVolume","args":[100]}', '*');
            ytIframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
        }
        if (heroVideo) {
            heroVideo.muted = false;
            heroVideo.volume = 1.0;
            heroVideo.play().catch(() => {});
        }
        isPlayingWithAudio = true;
        if (heroVideoCard) heroVideoCard.classList.add('is-playing');
    }

    function stopMuteVideo() {
        if (ytIframe) {
            ytIframe.contentWindow.postMessage('{"event":"command","func":"mute","args":""}', '*');
            ytIframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
        }
        if (heroVideo) {
            heroVideo.muted = true;
            heroVideo.pause();
        }
        isPlayingWithAudio = false;
        if (heroVideoCard) heroVideoCard.classList.remove('is-playing');
    }

    if (playBtn) {
        playBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            startVideoWithSound();
        });
    }

    if (clickOverlay) {
        clickOverlay.addEventListener('click', () => {
            if (isPlayingWithAudio) {
                stopMuteVideo();
            } else {
                startVideoWithSound();
            }
        });
    }

    // 5. Vertical Shorts / Reels Video Modal Handler
    const shortCards = document.querySelectorAll('.short-card');
    if (shortCards.length > 0) {
        let shortsModal = document.querySelector('.shorts-modal');
        if (!shortsModal) {
            shortsModal = document.createElement('div');
            shortsModal.className = 'shorts-modal';
            shortsModal.innerHTML = `
                <div class="shorts-modal-wrapper">
                    <button class="shorts-modal-close" aria-label="Close">&times;</button>
                    <div class="shorts-modal-content">
                        <video class="shorts-modal-video" controls autoplay playsinline poster="">
                            <source src="" type="video/mp4">
                            المتصفح لا يدعم تشغيل الفيديو.
                        </video>
                    </div>
                </div>
            `;
            document.body.appendChild(shortsModal);
        }

        const shortsVideo = shortsModal.querySelector('.shorts-modal-video');
        const shortsCloseBtn = shortsModal.querySelector('.shorts-modal-close');

        const openShortModal = (videoSrc, posterSrc) => {
            if (shortsVideo) {
                if (videoSrc) {
                    const sourceElem = shortsVideo.querySelector('source');
                    if (sourceElem) sourceElem.src = videoSrc;
                    shortsVideo.src = videoSrc;
                }
                if (posterSrc) {
                    shortsVideo.poster = posterSrc;
                }
                shortsVideo.play().catch(() => {});
            }
            shortsModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeShortModal = () => {
            shortsModal.classList.remove('active');
            document.body.style.overflow = '';
            if (shortsVideo) {
                shortsVideo.pause();
                shortsVideo.currentTime = 0;
            }
        };

        shortCards.forEach(card => {
            card.addEventListener('click', () => {
                const videoSrc = card.getAttribute('data-video') || 'assets/videos/hero_video.mp4';
                const posterImg = card.querySelector('.short-card-poster');
                const posterSrc = posterImg ? posterImg.src : '';
                openShortModal(videoSrc, posterSrc);
            });
        });

        if (shortsCloseBtn) shortsCloseBtn.addEventListener('click', closeShortModal);
        shortsModal.addEventListener('click', (e) => {
            if (e.target === shortsModal) closeShortModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && shortsModal.classList.contains('active')) {
                closeShortModal();
            }
        });
    }

    // 4. Mobile Navigation Drawer Architecture (Sliding Glassmorphism Overlay)
    const navWrapper = document.querySelector('.nav-wrapper');
    const navLinks = document.querySelector('.nav-links');
    const navActions = document.querySelector('.nav-actions');

    if (navWrapper && navLinks) {
        // Inject Mobile Header Controls (Lang Button + Hamburger Toggle) if not already in markup
        let headerControls = navWrapper.querySelector('.mobile-header-controls');
        if (!headerControls) {
            headerControls = document.createElement('div');
            headerControls.className = 'mobile-header-controls';
            headerControls.innerHTML = `
                <a href="?lang=en" class="mobile-lang-btn">🇬🇧 EN</a>
                <button class="mobile-nav-toggle" type="button" aria-label="Toggle Navigation Menu">
                    <span></span><span></span><span></span>
                </button>
            `;
            navWrapper.appendChild(headerControls);
        }
        let toggleBtn = headerControls.querySelector('.mobile-nav-toggle');

        // Inject Drawer Actions (CTA + Language switcher) into mobile drawer menu
        let drawerActions = navLinks.querySelector('.mobile-drawer-actions-item');
        if (!drawerActions && navActions) {
            drawerActions = document.createElement('li');
            drawerActions.className = 'mobile-drawer-actions-item';
            drawerActions.innerHTML = `
                <div class="mobile-drawer-actions">
                    ${navActions.innerHTML}
                </div>
            `;
            navLinks.appendChild(drawerActions);
        }

        const lockScroll = () => {
            document.documentElement.classList.add('mobile-menu-open');
            document.body.classList.add('mobile-menu-open');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        };

        const unlockScroll = () => {
            document.documentElement.classList.remove('mobile-menu-open');
            document.body.classList.remove('mobile-menu-open');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        };

        const toggleMenu = () => {
            const isOpen = navLinks.classList.contains('active');
            if (isOpen) {
                navLinks.classList.remove('active');
                toggleBtn.classList.remove('active');
                unlockScroll();
            } else {
                navLinks.classList.add('active');
                toggleBtn.classList.add('active');
                lockScroll();
            }
        };

        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        // Close drawer automatically when any link inside drawer is clicked
        const allDrawerLinks = navLinks.querySelectorAll('a');
        allDrawerLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    unlockScroll();
                }
            });
        });

        // Close drawer when clicking outside
        document.addEventListener('click', (e) => {
            if (navLinks.classList.contains('active') && !navLinks.contains(e.target) && !toggleBtn.contains(e.target)) {
                navLinks.classList.remove('active');
                toggleBtn.classList.remove('active');
                unlockScroll();
            }
        });

        // Close drawer when pressing Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                toggleBtn.classList.remove('active');
                unlockScroll();
            }
        });

        // Lock background touch scrolling when mobile menu is open (allow scrolling only inside drawer)
        document.addEventListener('touchmove', (e) => {
            if (document.body.classList.contains('mobile-menu-open')) {
                if (!e.target.closest('.nav-links')) {
                    e.preventDefault();
                }
            }
        }, { passive: false });
    }

    // 5. Non-Intrusive Floating Weather & Live Clock Pop-Up Widget
    (function initFloatingWeatherWidget() {
        // Exclude services page (services.php or services.html)
        const currentPath = window.location.pathname.toLowerCase();
        if (currentPath.includes('services')) {
            return;
        }

        // Show ONLY ONCE per session across all page navigations
        if (sessionStorage.getItem('weather_widget_shown') === 'true') {
            return;
        }
        sessionStorage.setItem('weather_widget_shown', 'true');

        const widget = document.createElement('div');
        widget.className = 'floating-weather-widget';
        widget.id = 'floating_weather_widget';

        const currentLang = document.documentElement.getAttribute('lang') || 'ar';
        const initialCity = currentLang === 'en' ? 'Riyadh' : 'الرياض';

        widget.innerHTML = `
            <div class="fw-icon-box" id="fw_weather_icon">☀️</div>
            <div class="fw-info-group">
                <div class="fw-top-row">
                    <span class="fw-temp" id="fw_temp">32°C</span>
                    <span class="fw-divider">•</span>
                    <span class="fw-city" data-en="Riyadh">${initialCity}</span>
                </div>
                <div class="fw-bottom-row">
                    <span class="fw-clock" id="fw_clock">00:00:00</span>
                    <span class="fw-divider">•</span>
                    <span class="fw-date" id="fw_date">--</span>
                </div>
            </div>
            <button class="fw-close-btn" id="fw_close_btn" title="إغلاق / Close" aria-label="Dismiss Weather & Clock Widget">&times;</button>
        `;

        document.body.appendChild(widget);

        const tempEl = widget.querySelector('#fw_temp');
        const iconEl = widget.querySelector('#fw_weather_icon');
        const clockEl = widget.querySelector('#fw_clock');
        const dateEl = widget.querySelector('#fw_date');
        const cityEl = widget.querySelector('.fw-city');
        const closeBtn = widget.querySelector('#fw_close_btn');

        let autoDismissTimer = null;

        const dismissWidget = () => {
            if (autoDismissTimer) clearTimeout(autoDismissTimer);
            widget.classList.add('dismissed');
            setTimeout(() => {
                if (widget.parentNode) {
                    widget.remove();
                }
            }, 600);
        };

        // Manual Close button listener
        closeBtn.addEventListener('click', dismissWidget);

        // Mandatory Auto-Dismissal after exactly 5 seconds (5000ms)
        autoDismissTimer = setTimeout(dismissWidget, 5000);

        const updateClock = () => {
            const now = new Date();
            const lang = document.documentElement.getAttribute('lang') || 'ar';

            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            let ampm = '';

            if (lang === 'ar') {
                ampm = hours >= 12 ? 'م' : 'ص';
                hours = hours % 12 || 12;
                clockEl.innerText = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
            } else {
                ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                clockEl.innerText = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
            }

            const monthsAr = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
            const daysAr = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            const monthsEn = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const daysEn = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            if (lang === 'ar') {
                dateEl.innerText = `${daysAr[now.getDay()]} ${now.getDate()} ${monthsAr[now.getMonth()]}`;
            } else {
                dateEl.innerText = `${daysEn[now.getDay()]}, ${monthsEn[now.getMonth()]} ${now.getDate()}`;
            }

            if (cityEl) {
                cityEl.innerText = lang === 'en' ? 'Riyadh' : 'الرياض';
            }
        };

        updateClock();
        setInterval(updateClock, 1000);

        const fetchWeather = async () => {
            try {
                const res = await fetch('https://api.open-meteo.com/v1/forecast?latitude=24.7136&longitude=46.6753&current_weather=true');
                if (!res.ok) throw new Error('Weather fetch failed');
                const data = await res.json();
                if (data && data.current_weather) {
                    const temp = Math.round(data.current_weather.temperature);
                    const isDay = data.current_weather.is_day;
                    const code = data.current_weather.weathercode;

                    tempEl.innerText = `${temp}°C`;

                    if (code === 0) {
                        iconEl.innerText = isDay ? '☀️' : '🌙';
                    } else if (code >= 1 && code <= 3) {
                        iconEl.innerText = isDay ? '🌤️' : '☁️';
                    } else if (code >= 51 && code <= 82) {
                        iconEl.innerText = '🌧️';
                    } else if (code >= 95) {
                        iconEl.innerText = '🌩️';
                    } else {
                        iconEl.innerText = isDay ? '☀️' : '🌙';
                    }
                }
            } catch (err) {
                const hour = new Date().getHours();
                const isDay = hour >= 6 && hour < 18;
                tempEl.innerText = '32°C';
                iconEl.innerText = isDay ? '☀️' : '🌙';
            }
        };

        fetchWeather();
        setInterval(fetchWeather, 600000);
    })();

    // 6. Smooth Scroll Reveal & Micro-Animations System
    (function initScrollReveal() {
        const revealElements = document.querySelectorAll('.card, .company-card, .img-card, .service-card, .portfolio-card, .qualification-card, .contact-info-card');

        if (!revealElements.length) return;

        revealElements.forEach(el => {
            el.classList.add('reveal-on-scroll');
        });

        if ('IntersectionObserver' in window) {
            const observerOptions = {
                root: null,
                threshold: 0.08,
                rootMargin: '0px 0px -40px 0px'
            };

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        obs.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            revealElements.forEach(el => observer.observe(el));
        } else {
            revealElements.forEach(el => el.classList.add('is-revealed'));
        }
    })();

    // 7. Standalone Digital Products Checkout Modal & Bank Transfer Handler
    const digitalModal = document.getElementById('digital_order_modal');
    const openDigitalBtns = document.querySelectorAll('.open-digital-modal-btn');
    const closeDigitalBtn = document.getElementById('close_digital_modal_btn');
    const selectedProdInput = document.getElementById('selected_digital_product');
    const selectedPriceInput = document.getElementById('selected_digital_price');
    const prodNameText = document.getElementById('modal_product_name_text');
    const digitalForm = document.getElementById('digital_order_form');

    if (digitalModal) {
        const openDigitalModal = (prodName, prodPrice) => {
            if (selectedProdInput) selectedProdInput.value = prodName;
            if (selectedPriceInput) selectedPriceInput.value = prodPrice || '';
            if (prodNameText) prodNameText.innerText = `${prodName} ${prodPrice ? '(' + prodPrice + ')' : ''}`;
            digitalModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeDigitalModal = () => {
            digitalModal.classList.remove('active');
            document.body.style.overflow = '';
        };

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.open-digital-modal-btn');
            if (btn) {
                e.preventDefault();
                const prod = btn.getAttribute('data-product') || 'منتج رقمي / دورة تدريبية';
                const price = btn.getAttribute('data-price') || '';
                openDigitalModal(prod, price);
            }
        });

        if (closeDigitalBtn) {
            closeDigitalBtn.addEventListener('click', closeDigitalModal);
        }

        digitalModal.addEventListener('click', (e) => {
            if (e.target === digitalModal) closeDigitalModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && digitalModal.classList.contains('active')) {
                closeDigitalModal();
            }
        });

        if (digitalForm) {
            digitalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(digitalForm);
                const prod = formData.get('digital_product') || 'المنتج الرقمي';
                const price = formData.get('digital_price') || '';
                const name = formData.get('client_name') || '';
                const email = formData.get('client_email') || '';
                const phone = formData.get('client_phone') || '';
                const notes = formData.get('client_notes') || '';
                const receiptInput = document.getElementById('receipt_file_input');
                const hasReceipt = receiptInput && receiptInput.files && receiptInput.files.length > 0;
                const fileName = hasReceipt ? receiptInput.files[0].name : 'تم الترفيق';

                const currentLang = document.documentElement.getAttribute('lang') || 'ar';

                let msg = currentLang === 'en'
                    ? `Hello Hassan, I completed the bank transfer for:\n📌 Product: ${prod} (${price})\n\nName: ${name}\nEmail: ${email}\nPhone: ${phone}\nBank: D360 Bank (Transfer Purpose: Friend)\nAttached Receipt: ${fileName}`
                    : `مرحباً أستاذ حسن، قمت بالتحويل البنكي المباشر لحساب D360 لشراء المنتج الرقمي:\n📌 ${prod} (${price})\n\nالاسم: ${name}\nالبريد الإلكتروني: ${email}\nرقم الجوال/الواتساب: ${phone}\nاسم البنك: D360 بنك (الغرض: صديق)\nإيصال التحويل: ${fileName}`;

                if (notes && notes.trim()) {
                    msg += currentLang === 'en' ? `\nNotes: ${notes.trim()}` : `\nالملاحظات: ${notes.trim()}`;
                }

                msg += currentLang === 'en'
                    ? `\n\nPlease verify transfer receipt and send the product files to my Email & WhatsApp.`
                    : `\n\nيرجى مراجعة إيصال التحويل وإرسال رابط تنزيل الملفات على البريد والواتساب فوراً.`;

                const waUrl = `https://wa.me/966570800663?text=${encodeURIComponent(msg)}`;
                window.open(waUrl, '_blank');

                closeDigitalModal();
                alert(currentLang === 'en' 
                    ? '🚀 Thank you! Your transfer receipt details have been recorded. Redirecting to WhatsApp for manual receipt verification & product delivery.'
                    : '🚀 شكراً لك! تم تسجيل طلبك وإرفاق الإيصال بنجاح. جاري توجيهك للواتساب لمراجعة التحويل وإرسال المنتجات فوراً.');
            });
        }
    }

    // 8. One-Click Copy Buttons Handler (IBAN & SWIFT) - Icon Button Edition
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-copy-code');
        if (btn) {
            e.preventDefault();
            const val = btn.getAttribute('data-copy') || btn.previousElementSibling?.innerText || '';
            if (val) {
                const doCopySuccess = () => {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '✅';
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                    }, 1500);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(val.trim()).then(doCopySuccess).catch(() => {
                        const el = document.createElement('textarea');
                        el.value = val.trim();
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                        doCopySuccess();
                    });
                } else {
                    const el = document.createElement('textarea');
                    el.value = val.trim();
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    doCopySuccess();
                }
            }
        }
    });

    // 9. 10 Customer Reviews Carousel Slider System
    (function initReviewsCarousel() {
        const track = document.getElementById('reviews_carousel_track');
        const prevBtn = document.getElementById('reviews_prev_btn');
        const nextBtn = document.getElementById('reviews_next_btn');
        const dotsContainer = document.getElementById('reviews_carousel_dots');

        if (!track) return;

        const cards = track.querySelectorAll('.review-card-item');
        if (!cards.length) return;

        let currentIndex = 0;

        const getCardsPerPage = () => {
            if (window.innerWidth <= 640) return 1;
            if (window.innerWidth <= 992) return 2;
            return 3;
        };

        const getMaxIndex = () => {
            const perPage = getCardsPerPage();
            return Math.max(0, cards.length - perPage);
        };

        const renderDots = () => {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = '';
            const maxIdx = getMaxIndex();
            const totalDots = maxIdx + 1;

            for (let i = 0; i < totalDots; i++) {
                const dot = document.createElement('div');
                dot.className = `carousel-dot ${i === currentIndex ? 'active' : ''}`;
                dot.addEventListener('click', () => {
                    currentIndex = i;
                    updateCarousel();
                });
                dotsContainer.appendChild(dot);
            }
        };

        const updateCarousel = () => {
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = 24; // 1.5rem
            const offset = currentIndex * (cardWidth + gap);

            if (isRTL) {
                track.style.transform = `translateX(${offset}px)`;
            } else {
                track.style.transform = `translateX(-${offset}px)`;
            }

            renderDots();
        };

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const maxIdx = getMaxIndex();
                if (currentIndex < maxIdx) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateCarousel();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const maxIdx = getMaxIndex();
                if (currentIndex > 0) {
                    currentIndex--;
                } else {
                    currentIndex = maxIdx;
                }
                updateCarousel();
            });
        }

        window.addEventListener('resize', () => {
            if (currentIndex > getMaxIndex()) {
                currentIndex = getMaxIndex();
            }
            updateCarousel();
        });

        updateCarousel();

        // Optional Autoplay every 5 seconds
        setInterval(() => {
            const maxIdx = getMaxIndex();
            if (currentIndex < maxIdx) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 6000);
    })();

});





