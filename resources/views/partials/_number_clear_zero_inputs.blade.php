{{-- Clear default 0 (or typed 05→5) on number inputs marked with class js-clear-zero. Optional data-zero-restore="5" for blur when empty. --}}
<script>
(function () {
    function isEffectivelyZero(val) {
        if (val === '' || val === null || val === undefined) return false;
        const n = parseFloat(String(val).trim().replace(',', '.'));
        return !isNaN(n) && n === 0;
    }

    function restoreValue(el) {
        if (el.hasAttribute('data-zero-restore')) {
            return el.getAttribute('data-zero-restore');
        }
        return '0';
    }

    function dispatchValueEvents(el) {
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    document.addEventListener('focusin', function (e) {
        var el = e.target;
        if (!el.matches || !el.matches('input[type="number"].js-clear-zero')) return;
        var v = String(el.value).trim();
        if (isEffectivelyZero(v)) {
            el.value = '';
        }
    }, true);

    document.addEventListener('focusout', function (e) {
        var el = e.target;
        if (!el.matches || !el.matches('input[type="number"].js-clear-zero')) return;
        var v = String(el.value).trim();
        if (v === '') {
            el.value = restoreValue(el);
            dispatchValueEvents(el);
        }
    }, true);

    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!el.matches || !el.matches('input[type="number"].js-clear-zero')) return;
        var v = el.value;
        // "05" → "5"; keep "0." / "0.5"
        if (/^0[0-9]/.test(v)) {
            var next = v.replace(/^0+/, '');
            el.value = next === '' ? '0' : next;
        }
    }, true);
})();
</script>
