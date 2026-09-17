// ============================================================
// Mobile nav toggle sekarang ditangani Alpine.js lewat
// x-data/@click di includes/header.php.
// ============================================================

// ============================================================
// Kartu kain batik interaktif di hero (beranda)
// - Tilt 3D mengikuti posisi kursor / sentuhan
// - Klik dot untuk ganti motif dengan transisi fade
// ============================================================
const kainCard  = document.getElementById('kainCard');
const kainImg   = document.getElementById('kainImg');
const kainLabel = document.getElementById('kainLabel');
const kainDots  = document.getElementById('kainDots');

if (kainCard && kainImg) {
  const MAX_TILT = 10; // derajat

  function applyTilt(clientX, clientY) {
    const rect = kainCard.getBoundingClientRect();
    const x = (clientX - rect.left) / rect.width;  // 0..1
    const y = (clientY - rect.top) / rect.height;  // 0..1
    const rotateY = (x - 0.5) * MAX_TILT * 2;
    const rotateX = (0.5 - y) * MAX_TILT * 2;
    kainCard.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
  }

  function resetTilt() {
    kainCard.style.transform = 'perspective(900px) rotateX(0) rotateY(0) scale(1)';
  }

  kainCard.addEventListener('mousemove', (e) => applyTilt(e.clientX, e.clientY));
  kainCard.addEventListener('mouseleave', resetTilt);

  kainCard.addEventListener('touchmove', (e) => {
    if (e.touches[0]) applyTilt(e.touches[0].clientX, e.touches[0].clientY);
  }, { passive: true });
  kainCard.addEventListener('touchend', resetTilt);

  if (kainDots) {
    kainDots.querySelectorAll('.kain-dot').forEach(dot => {
      dot.style.backgroundImage = `url('${dot.dataset.src}')`;

      dot.addEventListener('click', () => {
        if (dot.classList.contains('is-active')) return;

        kainDots.querySelectorAll('.kain-dot').forEach(d => d.classList.remove('is-active'));
        dot.classList.add('is-active');

        kainImg.classList.add('is-swapping');
        setTimeout(() => {
          kainImg.src = dot.dataset.src;
          kainImg.alt = 'Kain batik motif ' + dot.dataset.motif;
          if (kainLabel) kainLabel.textContent = dot.dataset.motif;
          kainImg.classList.remove('is-swapping');
        }, 200);
      });
    });
  }
}
