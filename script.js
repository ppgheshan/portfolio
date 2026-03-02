const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');
const navLinks = document.querySelectorAll('.nav-link');
const backToTop = document.querySelector('.back-to-top');

hamburger?.addEventListener('click', () => navMenu?.classList.toggle('active'));

navLinks.forEach(link => {
  link.addEventListener('click', () => {
    navMenu?.classList.remove('active');
    navLinks.forEach(item => item.classList.remove('active'));
    link.classList.add('active');
  });
});

const skillBars = document.querySelectorAll('.skill-progress');
const skillsSection = document.querySelector('.skills-section');

if (skillsSection) {
  new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        skillBars.forEach(bar => {
          bar.style.width = `${bar.dataset.width || 0}%`;
        });
      }
    });
  }, { threshold: 0.35 }).observe(skillsSection);
}

const typingText = document.querySelector('.typing-text');
const roles = ['ICT Undergraduate', 'Web Developer', 'Software Engineering Learner', 'UI-focused Creator'];
let roleIndex = 0;
let charIndex = 0;
let deleting = false;

function typeEffect() {
  if (!typingText) return;
  const current = roles[roleIndex];
  typingText.textContent = deleting
    ? current.slice(0, --charIndex)
    : current.slice(0, ++charIndex);

  if (!deleting && charIndex === current.length) {
    deleting = true;
    return setTimeout(typeEffect, 1200);
  }
  if (deleting && charIndex === 0) {
    deleting = false;
    roleIndex = (roleIndex + 1) % roles.length;
  }
  setTimeout(typeEffect, deleting ? 45 : 90);
}
setTimeout(typeEffect, 700);

window.addEventListener('scroll', () => {
  if (window.scrollY > 450) backToTop?.classList.add('visible');
  else backToTop?.classList.remove('visible');
});

backToTop?.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

const sections = document.querySelectorAll('section[id]');
window.addEventListener('scroll', () => {
  const pos = window.scrollY + 120;
  sections.forEach(section => {
    const link = document.querySelector(`.nav-link[href="#${section.id}"]`);
    if (!link) return;
    if (pos >= section.offsetTop && pos < section.offsetTop + section.offsetHeight) {
      navLinks.forEach(item => item.classList.remove('active'));
      link.classList.add('active');
    }
  });
});

const contactForm = document.getElementById('contactForm');
const submitBtn = document.getElementById('submitBtn');
const formMessage = document.getElementById('formMessage');

if (contactForm && submitBtn && formMessage) {
  contactForm.addEventListener('submit', async e => {
    e.preventDefault();
    const original = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
      const response = await fetch(contactForm.action, {
        method: 'POST',
        body: new FormData(contactForm),
        headers: { Accept: 'application/json' }
      });

      if (!response.ok) throw new Error('Request failed');
      formMessage.className = 'form-message success';
      formMessage.innerHTML = '<i class="fas fa-check-circle"></i> Message sent successfully. I will reply soon!';
      contactForm.reset();
    } catch (err) {
      formMessage.className = 'form-message error';
      formMessage.innerHTML = '<i class="fas fa-triangle-exclamation"></i> Could not send message. Please try again later.';
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = original;
      setTimeout(() => formMessage.className = 'form-message', 5000);
    }
  });
}
