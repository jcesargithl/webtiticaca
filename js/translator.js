/**
 * Translator Engine (Estilo Appstorrent.ru)
 * Basado en Google Translate en segundo plano con interfaz personalizada.
 * Compatible tanto en servidores web (http/https) como en local (file://).
 */

(function () {
    const TRANSLATOR_CONFIG = {
        defaultLang: 'es',
        languages: {
            'es': 'Es',
            'en': 'En',
            'fr': 'Fr',
            'de': 'De',
            'pt': 'Pt',
            'it': 'It',
            'zh-CN': 'Zh'
        }
    };

    let googleTranslateLoaded = false;
    const isLocalFile = window.location.protocol === 'file:';

    // Utilidades de almacenamiento (Cookie + LocalStorage como respaldo)
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    function setCookie(name, val) {
        const domain = window.location.hostname;
        document.cookie = `${name}=${encodeURIComponent(val)}; path=/;`;
        if (domain && domain !== 'localhost' && !/^\d+\.\d+\.\d+\.\d+$/.test(domain)) {
            document.cookie = `${name}=${encodeURIComponent(val)}; path=/; domain=${domain};`;
            document.cookie = `${name}=${encodeURIComponent(val)}; path=/; domain=.${domain};`;
        }
    }

    function deleteCookie(name) {
        const domain = window.location.hostname;
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        if (domain && domain !== 'localhost') {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${domain};`;
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.${domain};`;
        }
    }

    // Obtener idioma activo actual (verificando Cookie, LocalStorage y Hash)
    function getCurrentLang() {
        // 1. Intentar cookie primero
        const cookie = getCookie('googtrans');
        if (cookie && cookie !== 'null' && cookie !== '') {
            const parts = cookie.split('/');
            const target = parts[parts.length - 1];
            if (target && target !== TRANSLATOR_CONFIG.defaultLang) return target;
        }

        // 2. Intentar localStorage (especialmente útil en Chrome con file://)
        try {
            const local = localStorage.getItem('user_googtrans');
            if (local && local !== TRANSLATOR_CONFIG.defaultLang && TRANSLATOR_CONFIG.languages[local]) {
                return local;
            }
        } catch (e) {}

        // 3. Intentar URL hash (ej: #googtrans(auto|en))
        if (window.location.hash.includes('googtrans')) {
            const m = window.location.hash.match(/googtrans\([^|]+\|([^)]+)\)/);
            if (m && m[1] && TRANSLATOR_CONFIG.languages[m[1]]) {
                return m[1];
            }
        }

        return TRANSLATOR_CONFIG.defaultLang;
    }

    // Disparador directo sobre el selector interno de Google Translate
    function triggerCombo(targetLang) {
        let attempts = 0;
        const interval = setInterval(() => {
            const combo = document.querySelector('.goog-te-combo');
            if (combo) {
                clearInterval(interval);
                if (combo.value !== targetLang) {
                    combo.value = targetLang;
                    combo.dispatchEvent(new Event('change'));
                }
            }
            if (++attempts > 40) clearInterval(interval);
        }, 150);
    }

    // Cargar Google Translate en segundo plano
    function loadGoogleTranslate(currentLang) {
        if (googleTranslateLoaded) return;
        googleTranslateLoaded = true;

        // Asegurar la cookie en memoria antes de inicializar
        setCookie('googtrans', `/auto/${currentLang}`);

        // Crear contenedor oculto si no existe
        if (!document.getElementById('google_translate_element')) {
            const div = document.createElement('div');
            div.id = 'google_translate_element';
            div.style.display = 'none';
            document.body.appendChild(div);
        }

        window.googleTranslateElementInit = function () {
            new google.translate.TranslateElement({
                pageLanguage: TRANSLATOR_CONFIG.defaultLang,
                includedLanguages: Object.keys(TRANSLATOR_CONFIG.languages).join(','),
                autoDisplay: false
            }, 'google_translate_element');

            // Asegurar disparo en el combo
            triggerCombo(currentLang);
        };

        const script = document.createElement('script');
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        document.head.appendChild(script);
    }

    // Cambiar idioma al seleccionar en el menú
    function changeLanguage(targetLang) {
        if (targetLang === TRANSLATOR_CONFIG.defaultLang) {
            // Restaurar original (Español)
            deleteCookie('googtrans');
            try { localStorage.removeItem('user_googtrans'); } catch (e) {}
            if (window.location.hash.includes('googtrans')) {
                history.replaceState(null, '', window.location.pathname + window.location.search);
            }
        } else {
            // Guardar en cookie y localStorage
            setCookie('googtrans', `/auto/${targetLang}`);
            try { localStorage.setItem('user_googtrans', targetLang); } catch (e) {}
            if (isLocalFile) {
                window.location.hash = `#googtrans(auto|${targetLang})`;
            }
        }
        window.location.reload();
    }

    // Inicializar interfaz y listeners
    function initTranslator() {
        const current = getCurrentLang();
        const label = TRANSLATOR_CONFIG.languages[current] || current.toUpperCase();

        // Actualizar texto del botón (ej. Es, En, De, Fr)
        const langHeader = document.getElementById('lang');
        if (langHeader) {
            langHeader.innerText = label;
        }

        // Marcar el elemento activo en el menú desplegable
        document.querySelectorAll('#langDropdown a[data-google-lang]').forEach(el => {
            const lang = el.getAttribute('data-google-lang');
            if (lang === current) {
                el.classList.add('language__active');
            } else {
                el.classList.remove('language__active');
            }
        });

        // Si el idioma es extranjero, cargar Google Translate
        if (current !== TRANSLATOR_CONFIG.defaultLang) {
            loadGoogleTranslate(current);
        }

        // Control del menú desplegable (abrir / cerrar)
        const container = document.getElementById('lang_min');
        const dropdown = document.getElementById('langDropdown');

        if (container && dropdown) {
            container.addEventListener('click', function (e) {
                if (e.target.closest('a[data-google-lang]')) return;
                e.stopPropagation();
                dropdown.classList.toggle('show');
                container.classList.toggle('open');
            });

            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function (e) {
                if (!container.contains(e.target)) {
                    dropdown.classList.remove('show');
                    container.classList.remove('open');
                }
            });

            // Asignar eventos de clic a cada idioma
            dropdown.querySelectorAll('a[data-google-lang]').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetLang = this.getAttribute('data-google-lang');
                    dropdown.classList.remove('show');
                    container.classList.remove('open');
                    changeLanguage(targetLang);
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTranslator);
    } else {
        initTranslator();
    }
})();
