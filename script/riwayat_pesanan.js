// Cancel modal
function showCancelModal(id, kode) {
  document.getElementById('cancelId').value = id;
  document.getElementById('cancelKode').textContent = '#' + kode;
  document.getElementById('cancelOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
  document.getElementById('cancelOverlay').classList.remove('active');
  document.body.style.overflow = '';
}
document.getElementById('cancelOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeCancelModal();
});

// Filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('#orderBody tr[data-group]').forEach(row => {
      if (filter === 'all' || row.dataset.group === filter) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});
