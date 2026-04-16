document.documentElement.classList.add('js');

(function () {
    const siteHeader = document.querySelector('.site-header');
    const toggleButton = document.querySelector('.header-menu-toggle');

    if (!siteHeader || !toggleButton) {
        return;
    }

    const syncMenuState = (expanded) => {
        toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        siteHeader.classList.toggle('is-menu-open', expanded);
    };

    toggleButton.addEventListener('click', () => {
        const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
        syncMenuState(!isExpanded);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 720) {
            syncMenuState(false);
        }
    });
}());

(function () {
    if (typeof intranetDashboardBase === 'undefined') {
        return;
    }

    const wrappers = document.querySelectorAll('.em-calendario-wrapper');

    if (!wrappers.length) {
        return;
    }

    const monthNames = intranetDashboardBase.monthNames || [];
    const weekDayNames = intranetDashboardBase.weekDayNames || [];

    wrappers.forEach((calendarWrapper) => {
        let currentDate = new Date();
        const header = calendarWrapper.querySelector('.em-mes-ano');
        const weekDaysContainer = calendarWrapper.querySelector('.em-dias-semana');
        const daysGrid = calendarWrapper.querySelector('.em-dias-grid');
        const calendarView = calendarWrapper.dataset.view || 'widget';

        if (!header || !weekDaysContainer || !daysGrid) {
            return;
        }

        function renderWeekDays() {
            weekDaysContainer.innerHTML = '';

            weekDayNames.forEach((day) => {
                const span = document.createElement('span');
                span.textContent = day;
                weekDaysContainer.appendChild(span);
            });
        }

        function markEvents(events) {
            const byDate = events.reduce((acc, event) => {
                if (!acc[event.date]) {
                    acc[event.date] = [];
                }

                acc[event.date].push(event);
                return acc;
            }, {});

            daysGrid.querySelectorAll('.em-dia-celula[data-date]').forEach((cell) => {
                const date = cell.dataset.date;
                const currentList = cell.querySelector('.em-event-list');

                if (currentList) {
                    currentList.remove();
                }

                cell.classList.remove('has-event');

                if (!byDate[date]) {
                    return;
                }

                cell.classList.add('has-event');

                if (calendarView === 'full') {
                    const list = document.createElement('div');
                    list.className = 'em-event-list';

                    byDate[date].forEach((event) => {
                        const link = document.createElement('a');
                        link.href = event.url;
                        link.className = 'em-event';
                        link.textContent = event.title;
                        list.appendChild(link);
                    });

                    cell.appendChild(list);
                    return;
                }

                const firstEvent = byDate[date][0];
                const existingLink = cell.querySelector('.em-day-link');
                const number = cell.querySelector('.em-day-number');

                if (existingLink) {
                    existingLink.href = firstEvent.url;
                    return;
                }

                if (number) {
                    const link = document.createElement('a');
                    link.href = firstEvent.url;
                    link.className = 'em-day-link';
                    number.replaceWith(link);
                    link.appendChild(number);
                }
            });
        }

        async function loadEvents(year, monthIndex) {
            const body = new URLSearchParams();
            body.set('action', 'intranet_dashboard_base_get_eventos');
            body.set('security', intranetDashboardBase.nonce);
            body.set('year', String(year));
            body.set('month', String(monthIndex + 1));

            try {
                const response = await fetch(intranetDashboardBase.ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    },
                    credentials: 'same-origin',
                    body: body.toString(),
                });

                const payload = await response.json();

                if (payload && payload.success) {
                    markEvents(payload.data || []);
                }
            } catch (error) {
                console.error('Erro ao carregar eventos do calendario.', error);
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const monthIndex = currentDate.getMonth();
            const firstDay = new Date(year, monthIndex, 1).getDay();
            const totalDays = new Date(year, monthIndex + 1, 0).getDate();
            const today = new Date();

            header.textContent = `${monthNames[monthIndex]} ${year}`;
            daysGrid.innerHTML = '';
            renderWeekDays();

            for (let i = 0; i < firstDay; i += 1) {
                const filler = document.createElement('div');
                filler.className = 'em-dia-celula em-other-month';
                daysGrid.appendChild(filler);
            }

            for (let day = 1; day <= totalDays; day += 1) {
                const cell = document.createElement('div');
                const dayNumber = document.createElement('span');

                cell.className = 'em-dia-celula';
                cell.dataset.date = `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                dayNumber.className = 'em-day-number';
                dayNumber.textContent = String(day);
                cell.appendChild(dayNumber);

                if (
                    day === today.getDate()
                    && monthIndex === today.getMonth()
                    && year === today.getFullYear()
                ) {
                    cell.classList.add('today');
                }

                daysGrid.appendChild(cell);
            }

            loadEvents(year, monthIndex);
        }

        calendarWrapper.querySelectorAll('.em-nav-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const action = button.dataset.nav;

                if (action === 'prev') {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                } else if (action === 'next') {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                } else if (action === 'today') {
                    currentDate = new Date();
                }

                renderCalendar();
            });
        });

        const fullscreenToggle = calendarWrapper.querySelector('.em-view-btn[data-view="fullscreen"]');
        if (fullscreenToggle) {
            fullscreenToggle.addEventListener('click', () => {
                calendarWrapper.classList.toggle('em-fullscreen-mode');
                document.body.classList.toggle('em-fullscreen-active');

                if (calendarWrapper.classList.contains('em-fullscreen-mode')) {
                    if (!calendarWrapper.querySelector('.em-close-fullscreen-btn')) {
                        const closeButton = document.createElement('button');
                        closeButton.type = 'button';
                        closeButton.className = 'em-close-fullscreen-btn';
                        closeButton.textContent = 'x';
                        calendarWrapper.appendChild(closeButton);
                    }
                } else {
                    const closeButton = calendarWrapper.querySelector('.em-close-fullscreen-btn');
                    if (closeButton) {
                        closeButton.remove();
                    }
                }
            });

            calendarWrapper.addEventListener('click', (event) => {
                if (!event.target.classList.contains('em-close-fullscreen-btn')) {
                    return;
                }

                calendarWrapper.classList.remove('em-fullscreen-mode');
                document.body.classList.remove('em-fullscreen-active');
                event.target.remove();
            });
        }

        renderCalendar();
    });
}());

(function () {
    const photoInput = document.querySelector('#profile_photo');
    const preview = document.querySelector('.profile-photo-preview');
    const previewMedia = preview ? preview.querySelector('.profile-photo-preview-media') : null;
    const previewText = preview ? preview.querySelector('.profile-photo-preview-text') : null;
    const initialPreviewMarkup = previewMedia ? previewMedia.innerHTML : '';
    const initialPreviewText = previewText ? previewText.textContent : '';
    const passwordInput = document.querySelector('#new_password');
    const confirmInput = document.querySelector('#confirm_password');
    const matchIndicator = document.querySelector('.password-match-indicator');

    if (photoInput && preview && previewMedia && previewText) {
        photoInput.addEventListener('change', () => {
            const [file] = photoInput.files || [];

            if (!file) {
                preview.classList.remove('has-new-image');
                previewMedia.innerHTML = initialPreviewMarkup;
                previewText.textContent = initialPreviewText || preview.dataset.emptyLabel || '';
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            const image = document.createElement('img');
            image.src = objectUrl;
            image.alt = file.name;

            previewMedia.innerHTML = '';
            previewMedia.appendChild(image);
            preview.classList.add('has-new-image');
            previewText.textContent = `Nova foto selecionada: ${file.name}`;
        });
    }

    function updatePasswordIndicator() {
        if (!passwordInput || !confirmInput || !matchIndicator) {
            return;
        }

        const password = passwordInput.value;
        const confirmation = confirmInput.value;

        matchIndicator.className = 'password-match-indicator';
        matchIndicator.textContent = '';

        if (!password && !confirmation) {
            return;
        }

        if (password.length > 0 && password.length < 6) {
            matchIndicator.classList.add('is-error');
            matchIndicator.textContent = 'A nova senha precisa ter pelo menos 6 caracteres.';
            return;
        }

        if (confirmation.length === 0) {
            matchIndicator.classList.add('is-muted');
            matchIndicator.textContent = 'Confirme a nova senha para verificar se os campos coincidem.';
            return;
        }

        if (password === confirmation) {
            matchIndicator.classList.add('is-success');
            matchIndicator.textContent = 'As senhas coincidem.';
            return;
        }

        matchIndicator.classList.add('is-error');
        matchIndicator.textContent = 'As senhas nao coincidem.';
    }

    if (passwordInput && confirmInput && matchIndicator) {
        passwordInput.addEventListener('input', updatePasswordIndicator);
        confirmInput.addEventListener('input', updatePasswordIndicator);
    }
}());
