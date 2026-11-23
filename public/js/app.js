/* ==========================================================================
   app.js — Ortak küçük etkileşimler (bağımlılık yok)
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {

    /* Kullanıcı açılır menüsü */
    document.querySelectorAll('.user-menu-toggle').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            btn.closest('.user-menu').classList.toggle('open');
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('.user-menu.open').forEach(m => m.classList.remove('open'));
    });

    /* Uyarı kapatma */
    document.querySelectorAll('.alert-close').forEach(b => b.addEventListener('click', () => b.closest('.alert').remove()));

    /* Adet arttır / azalt */
    document.querySelectorAll('.qty').forEach(box => {
        const input = box.querySelector('input');
        const max = parseInt(input.max || 99, 10);
        box.querySelectorAll('button[data-step]').forEach(btn => {
            btn.addEventListener('click', () => {
                const v = Math.min(max, Math.max(parseInt(input.min || 1, 10), (parseInt(input.value, 10) || 1) + parseInt(btn.dataset.step, 10)));
                input.value = v;
                if (box.dataset.autosubmit !== undefined) box.closest('form').submit();
            });
        });
        if (box.dataset.autosubmit !== undefined) {
            input.addEventListener('change', () => box.closest('form').submit());
        }
    });

    /* Sekmeler */
    document.querySelectorAll('[data-tabs]').forEach(tabs => {
        const buttons = tabs.querySelectorAll('.tab-nav button');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            buttons.forEach(button => {
                button.classList.toggle('active', button === btn);
                button.setAttribute('aria-pressed', String(button === btn));
            });
            tabs.querySelectorAll('.tab-panel').forEach(p => p.classList.toggle('active', p.id === btn.dataset.target));
        }));
    });

    /* Ana sayfa kampanyaları */
    const hero = document.querySelector('.hero');
    if (hero) {
        const slides = hero.querySelectorAll('.hero-slide');
        const dots = hero.querySelectorAll('.hero-dots button');
        let i = 0;
        const show = n => {
            i = (n + slides.length) % slides.length;
            slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === i);
                slide.inert = index !== i;
            });
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === i);
                dot.setAttribute('aria-pressed', String(index === i));
            });
        };
        dots.forEach((d, k) => d.addEventListener('click', () => show(k)));
    }

    /* Şifre göster / gizle */
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? 'Göster' : 'Gizle';
        });
    });

    /* Kredi kartı formu: biçimlendirme + canlı önizleme */
    const cardNumber = document.querySelector('#card_number');
    if (cardNumber) {
        const preview = {
            number: document.querySelector('[data-preview=number]'),
            holder: document.querySelector('[data-preview=holder]'),
            expiry: document.querySelector('[data-preview=expiry]'),
        };
        const holder = document.querySelector('#card_holder');
        const month = document.querySelector('#card_month');
        const year = document.querySelector('#card_year');

        const update = () => {
            const digits = cardNumber.value.replace(/\D/g, '').slice(0, 16);
            cardNumber.value = digits.replace(/(.{4})/g, '$1 ').trim();
            preview.number.textContent = (digits + '•'.repeat(16 - digits.length)).replace(/(.{4})/g, '$1 ').trim();
            preview.holder.textContent = holder.value.toLocaleUpperCase('tr-TR') || 'AD SOYAD';
            preview.expiry.textContent = (month.value || 'AA').toString().padStart(2, '0') + '/' + (year.value ? year.value.toString().slice(-2) : 'YY');
        };
        [cardNumber, holder, month, year].forEach(el => el.addEventListener('input', update));
        update();
    }

    /* Silme / iptal onayı */
    document.querySelectorAll('form[data-confirm]').forEach(f => {
        f.addEventListener('submit', e => {
            if (!confirm(f.dataset.confirm)) e.preventDefault();
        });
    });

    /* Admin: kapak rengi seçici ile metin kutusunu eşle */
    document.querySelectorAll('.color-input').forEach(box => {
        const picker = box.querySelector('input[type=color]');
        const text = box.querySelector('input[type=text]');
        picker.addEventListener('input', () => text.value = picker.value);
        text.addEventListener('input', () => { if (/^#[0-9a-f]{6}$/i.test(text.value)) picker.value = text.value; });
    });
});
