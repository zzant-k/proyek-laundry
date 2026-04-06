/* ══ Modal Helpers ══ */
window.openModal = function(id) {
    var m = document.getElementById(id);
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeModal = function(id) {
    var m = document.getElementById(id);
    m.style.display = 'none';
    document.body.style.overflow = '';
};
// Close modal when clicking backdrop
['modalTambah','modalEdit'].forEach(function(id){
    document.getElementById(id).addEventListener('click', function(e){
        if(e.target === this) closeModal(id);
    });
});

window.openEditModal = function(id, kode, nama, hp, pencucian, layanan, tanggal, jam, alamat, status) {
    document.getElementById('eId').value        = id;
    document.getElementById('editKode').textContent = '#' + kode;
    document.getElementById('eNama').value     = nama;
    document.getElementById('eHP').value       = hp;
    document.getElementById('eTanggal').value  = tanggal;
    document.getElementById('eJam').value      = jam;
    document.getElementById('eAlamat').value   = alamat;
    // selects
    setSelect('ePencucian', pencucian);
    setSelect('eLayanan',   layanan);
    setSelect('eStatus',    status);
    openModal('modalEdit');
};
function setSelect(id, val) {
    var s = document.getElementById(id);
    for(var i=0;i<s.options.length;i++){
        if(s.options[i].value === val){ s.selectedIndex=i; break; }
    }
}

/* UI Interaktif saja — sidebar toggle, overlay, animations */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const overlay = document.getElementById('overlay');
    const isMobile = () => window.innerWidth <= 768;

    sidebarToggle.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });
    sidebarClose.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    });

    /* Scroll animations */
    document.querySelectorAll('[data-animate]').forEach((el, i) => {
        setTimeout(() => el.classList.add('animated'), (parseInt(el.dataset.delay || 0)) + (i * 80));
    });

    /* Profile dropdown */
    const profileToggle = document.getElementById('profileToggle');
    profileToggle.addEventListener('click', e => {
        e.stopPropagation();
        profileToggle.classList.toggle('open');
    });
    document.addEventListener('click', () => profileToggle.classList.remove('open'));

    /* ── Multi-select logic ── */
    const selectAll  = document.getElementById('selectAll');
    const toolbar    = document.getElementById('bulkToolbar');
    const countEl    = document.getElementById('selectedCount');

    function getChecked() {
        return [...document.querySelectorAll('.row-cb:checked')];
    }

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
        selectAll.indeterminate = checked.length > 0 && checked.length < document.querySelectorAll('.row-cb').length;
        selectAll.checked = checked.length === document.querySelectorAll('.row-cb').length && checked.length > 0;
    }

    selectAll.addEventListener('change', () => {
        document.querySelectorAll('.row-cb').forEach(cb => cb.checked = selectAll.checked);
        updateToolbar();
    });

    document.querySelectorAll('.row-cb').forEach(cb => {
        cb.addEventListener('change', updateToolbar);
    });
});

window.toggleSelectMode = function() {
    const table = document.getElementById('mainTable');
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
    document.getElementById('bulkToolbar').style.display = 'none';
};

window.confirmBulkDelete = function() {
    const checked = [...document.querySelectorAll('.row-cb:checked')];
    if (checked.length === 0) return;
    if (!confirm(`Yakin ingin menghapus ${checked.length} data yang dipilih?`)) return;
    const container = document.getElementById('bulkInputs');
    container.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    document.getElementById('bulkForm').submit();
};
