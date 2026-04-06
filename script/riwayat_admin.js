/* ── Sidebar & profile toggle (fallback jika dashboard.js tidak ada) ── */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar      = document.getElementById('sidebar');
    const sidebarToggle= document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const overlay      = document.getElementById('overlay');
    const isMobile     = () => window.innerWidth <= 768;

    if (sidebarToggle) sidebarToggle.addEventListener('click', () => {
        if (isMobile()) { sidebar.classList.toggle('mobile-open'); overlay.classList.toggle('show'); }
        else { sidebar.classList.toggle('collapsed'); }
    });
    if (sidebarClose) sidebarClose.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open'); overlay.classList.remove('show');
    });
    if (overlay) overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open'); overlay.classList.remove('show');
    });

    /* Profile dropdown */
    const profileToggle = document.getElementById('profileToggle');
    if (profileToggle) {
        profileToggle.addEventListener('click', e => { e.stopPropagation(); profileToggle.classList.toggle('open'); });
        document.addEventListener('click', () => profileToggle.classList.remove('open'));
    }

    /* Scroll animations */
    document.querySelectorAll('[data-animate]').forEach((el, i) => {
        setTimeout(() => el.classList.add('animated'), i * 80);
    });

    /* ── Checkbox multi-select ── */
    const selectAll = document.getElementById('selectAll');
    const toolbar   = document.getElementById('bulkToolbar');
    const countEl   = document.getElementById('selectedCount');

    function getChecked() { return [...document.querySelectorAll('.row-cb:checked')]; }

    function updateToolbar() {
        const checked = getChecked();
        if (checked.length > 0) {
            toolbar.style.display = 'flex';
            requestAnimationFrame(() => toolbar.classList.add('visible'));
            countEl.textContent = checked.length;
        } else {
            toolbar.classList.remove('visible');
            setTimeout(() => toolbar.style.display = 'none', 260);
        }
        const allCbs = document.querySelectorAll('.row-cb');
        selectAll.indeterminate = checked.length > 0 && checked.length < allCbs.length;
        selectAll.checked = checked.length === allCbs.length && checked.length > 0;
    }

    selectAll.addEventListener('change', () => {
        document.querySelectorAll('.row-cb').forEach(cb => cb.checked = selectAll.checked);
        updateToolbar();
    });
    document.querySelectorAll('.row-cb').forEach(cb => cb.addEventListener('change', updateToolbar));
});

/* ── Toggle select mode ── */
window.toggleSelectMode = function() {
    const table = document.getElementById('riwayatTable');
    const btn   = document.getElementById('toggleSelectBtn');
    const isOn  = table.classList.toggle('select-mode');
    btn.classList.toggle('active', isOn);
    btn.innerHTML = isOn
        ? '<i class="fas fa-times"></i> Batal Pilih'
        : '<i class="fas fa-check-square"></i> Pilih';
    if (!isOn) clearSelection();
};

window.clearSelection = function() {
    document.querySelectorAll('.row-cb').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    const toolbar = document.getElementById('bulkToolbar');
    toolbar.classList.remove('visible');
    setTimeout(() => toolbar.style.display = 'none', 260);
};

window.confirmBulkDelete = function() {
    const checked = [...document.querySelectorAll('.row-cb:checked')];
    if (checked.length === 0) return;
    if (!confirm(`Yakin ingin menghapus ${checked.length} riwayat yang dipilih? Tindakan ini tidak bisa dibatalkan.`)) return;
    const container = document.getElementById('bulkInputs');
    container.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    document.getElementById('bulkForm').submit();
};
