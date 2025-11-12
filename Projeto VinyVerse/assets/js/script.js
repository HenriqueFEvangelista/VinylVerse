// Função para soltar confetes
function launchConfetti() {
  const duration = 1.5 * 1000;
  const animationEnd = Date.now() + duration;
  const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 1000 };

  function randomInRange(min, max) {
    return Math.random() * (max - min) + min;
  }

  const interval = setInterval(function () {
    const timeLeft = animationEnd - Date.now();

    if (timeLeft <= 0) {
      return clearInterval(interval);
    }

    const particleCount = 50 * (timeLeft / duration);
    confetti({
      ...defaults,
      particleCount,
      origin: { x: randomInRange(0.1, 0.9), y: Math.random() - 0.2 }
    });
  }, 250);
}

// Mostra a animação de sucesso
function showSuccessAnimation(message) {
  const container = document.querySelector('.container');
  container.innerHTML = `
    <div class="success-animation">
        <div class="checkmark">
            <div class="checkmark_stem"></div>
            <div class="checkmark_kick"></div>
        </div>
        <h2>${message}</h2>
        <a href="home.php" class="btn">Ir para página inicial</a>
    </div>
  `;

  launchConfetti();
}

// Detecta query ?success=login ou ?success=cadastro e mostra animação
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  if (params.has('success')) {
    const type = params.get('success');
    if (type === 'login') showSuccessAnimation('Login realizado com sucesso!');
    if (type === 'cadastro') showSuccessAnimation('Cadastro concluído!');
  }
});
