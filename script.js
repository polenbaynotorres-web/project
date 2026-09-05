document.addEventListener("DOMContentLoaded", () => {

  /* ---------- Mobile nav toggle ---------- */
  const header = document.getElementById("siteHeader");
  const navToggle = document.getElementById("navToggle");
  if (navToggle && header) {
    navToggle.addEventListener("click", () => {
      const isOpen = header.classList.toggle("nav-open");
      navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }

  /* ---------- Search bar: keep return date >= pickup date ---------- */
  const pickupDate = document.getElementById("pickupDate");
  const returnDate = document.getElementById("returnDate");
  const todayISO = new Date().toISOString().split("T")[0];
  if (pickupDate) pickupDate.min = todayISO;
  if (returnDate) returnDate.min = todayISO;

  if (pickupDate && returnDate) {
    pickupDate.addEventListener("change", () => {
      returnDate.min = pickupDate.value || todayISO;
      if (returnDate.value && returnDate.value < returnDate.min) {
        returnDate.value = returnDate.min;
      }
    });
  }

  const searchForm = document.getElementById("searchForm");
  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      e.preventDefault();
      document.getElementById("fleet").scrollIntoView({ behavior: "smooth" });
    });
  }

  /* ---------- Reservation modal (front-end only for now) ---------- */
  const modal = document.getElementById("reserveModal");
  const modalClose = document.getElementById("modalClose");
  const modalCarName = document.getElementById("modalCarName");
  const reserveForm = document.getElementById("reserveForm");
  const modalSuccess = document.getElementById("modalSuccess");

  function openModal(carName) {
    if (!modal) return;
    modalCarName.textContent = carName
      ? `Reserving: ${carName}. Fill in your details and we'll confirm your booking shortly.`
      : "Complete your details and we'll confirm your booking shortly.";
    reserveForm.hidden = false;
    modalSuccess.hidden = true;
    reserveForm.reset();
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }

  document.querySelectorAll(".rent-btn").forEach(btn => {
    btn.addEventListener("click", () => openModal(btn.dataset.car));
  });

  const reserveTopLinks = document.querySelectorAll('a[href="#reserve"]');
  reserveTopLinks.forEach(link => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      openModal("");
    });
  });

  if (modalClose) modalClose.addEventListener("click", closeModal);
  if (modal) {
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });
  }
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal && modal.classList.contains("open")) closeModal();
  });

  if (reserveForm) {
    reserveForm.addEventListener("submit", (e) => {
      e.preventDefault();
      // Front-end only for now — no server call yet, just a fake
      // success state so the flow can be demoed end to end.
      reserveForm.hidden = true;
      modalSuccess.hidden = false;
      setTimeout(closeModal, 1800);
    });
  }

  /* ---------- Testimonials: simple auto-rotate dots (mobile-friendly) ---------- */
  const track = document.getElementById("reviewsTrack");
  const dotsWrap = document.getElementById("reviewDots");
  if (track && dotsWrap) {
    const cards = Array.from(track.children);
    cards.forEach((_, i) => {
      const dot = document.createElement("button");
      dot.setAttribute("aria-label", `Show review ${i + 1}`);
      if (i === 0) dot.classList.add("active");
      dot.addEventListener("click", () => showReview(i));
      dotsWrap.appendChild(dot);
    });

    function showReview(index) {
      const isMobileLayout = window.matchMedia("(max-width: 720px)").matches;
      if (!isMobileLayout) return;
      cards.forEach((card, i) => { card.style.display = i === index ? "block" : "none"; });
      Array.from(dotsWrap.children).forEach((d, i) => d.classList.toggle("active", i === index));
    }

    function applyLayout() {
      const isMobileLayout = window.matchMedia("(max-width: 720px)").matches;
      dotsWrap.style.display = isMobileLayout ? "flex" : "none";
      if (isMobileLayout) {
        showReview(0);
      } else {
        cards.forEach(card => { card.style.display = ""; });
      }
    }
    applyLayout();
    window.addEventListener("resize", applyLayout);
  }

  /* ---------- Newsletter form (front-end only for now) ---------- */
  const newsletterForm = document.getElementById("newsletterForm");
  const newsletterMsg = document.getElementById("newsletterMsg");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", (e) => {
      e.preventDefault();
      newsletterMsg.textContent = "Thanks for subscribing!";
      newsletterForm.reset();
    });
  }

  /* ---------- Back to top ---------- */
  const backToTop = document.getElementById("backToTop");
  if (backToTop) {
    window.addEventListener("scroll", () => {
      backToTop.classList.toggle("visible", window.scrollY > 500);
    });
    backToTop.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  /* ---------- Reveal-on-scroll for section headers/cards ---------- */
  const revealTargets = document.querySelectorAll(".car-card, .feature, .step, .review-card");
  if ("IntersectionObserver" in window) {
    revealTargets.forEach(el => {
      el.style.opacity = "0";
      el.style.transform = "translateY(16px)";
      el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    });
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealTargets.forEach(el => observer.observe(el));
  }
});