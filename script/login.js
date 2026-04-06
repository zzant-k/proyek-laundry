const card = document.getElementById('card');
function go(mode) {
  if(mode === 'register') { card.classList.add('reg'); } else { card.classList.remove('reg'); }
}
function tPw(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}

/* ═══ Popup Notification System ═══ */
function showToast(message, type) {
  type = type || 'success';
  var overlay = document.createElement('div');
  overlay.className = 'popup-overlay';
  overlay.innerHTML =
    '<div class="popup-box">' +
      '<div class="popup-bar bar-' + type + '"></div>' +
      '<div class="popup-msg">' + message + '</div>' +
      '<button class="popup-close">✕&nbsp;&nbsp;Tutup</button>' +
    '</div>';
  document.body.appendChild(overlay);

  overlay.querySelector('.popup-close').addEventListener('click', function() {
    dismissPopup(overlay);
  });
  overlay.addEventListener('click', function(e) {
    if (e.target === overlay) dismissPopup(overlay);
  });

  var timeout;
  var remaining = 3000;
  var start = Date.now();

  function startTimer() {
    start = Date.now();
    timeout = setTimeout(function() { dismissPopup(overlay); }, remaining);
  }

  var box = overlay.querySelector('.popup-box');
  box.addEventListener('mouseenter', function() {
    clearTimeout(timeout);
    remaining -= (Date.now() - start);
  });
  box.addEventListener('mouseleave', function() {
    startTimer();
  });

  startTimer();
}

function dismissPopup(overlay) {
  if (overlay.classList.contains('popup-out')) return;
  overlay.classList.add('popup-out');
  overlay.addEventListener('animationend', function() {
    overlay.remove();
  });
}
