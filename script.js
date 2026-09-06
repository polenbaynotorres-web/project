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

  /* ---------- Auth state (login/register/logout) ---------- */
  const headerAuthArea = document.getElementById("headerAuthArea");
  let currentUser = null; // null = logged out, otherwise {name, email}

  function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
  }

  function renderAuthArea() {
    if (!headerAuthArea) return;
    if (currentUser) {
      headerAuthArea.innerHTML = `
        <span class="user-greeting">Hi, ${escapeHtml(currentUser.name.split(" ")[0])}</span>
        <button type="button" class="btn btn-outline-navy btn-sm" id="logoutBtn">Log Out</button>
      `;
      document.getElementById("logoutBtn").addEventListener("click", handleLogout);
    } else {
      headerAuthArea.innerHTML = `
        <button type="button" class="btn btn-outline-navy btn-sm" id="loginTrigger">Log In</button>
        <button type="button" class="btn btn-primary btn-sm" id="registerTrigger">Register</button>
      `;
      document.getElementById("loginTrigger").addEventListener("click", () => openAuthModal("login"));
      document.getElementById("registerTrigger").addEventListener("click", () => openAuthModal("register"));
    }
  }

  async function checkSession() {
    try {
      const response = await fetch("session_check.php");
      const result = await response.json();
      currentUser = result.loggedIn ? result.user : null;
    } catch (err) {
      currentUser = null;
    }
    renderAuthArea();
  }

  async function handleLogout() {
    try {
      await fetch("logout.php", { method: "POST" });
    } catch (err) {
      // Treat as logged out locally even if the request itself failed.
    }
    currentUser = null;
    renderAuthArea();
  }

  /* ---------- Login / Register modals ---------- */
  const loginModal = document.getElementById("loginModal");
  const loginModalClose = document.getElementById("loginModalClose");
  const loginForm = document.getElementById("loginForm");
  const loginError = document.getElementById("loginError");
  const loginModalMsg = document.getElementById("loginModalMsg");

  const registerModal = document.getElementById("registerModal");
  const registerModalClose = document.getElementById("registerModalClose");
  const registerForm = document.getElementById("registerForm");
  const registerError = document.getElementById("registerError");

  // What to do once login/register succeeds — "Rent Now" sets this
  // to reopen the reservation modal afterward.
  let afterAuthAction = null;

  function openModalEl(el) {
    if (!el) return;
    el.classList.add("open");
    el.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  }
  function closeModalEl(el) {
    if (!el) return;
    el.classList.remove("open");
    el.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }
  function closeAllModals() {
    [reserveModal, loginModal, registerModal].forEach(closeModalEl);
  }

  function openAuthModal(which, message) {
    closeAllModals();
    if (which === "login") {
      loginError.hidden = true;
      loginModalMsg.textContent = message || "Log in to book a vehicle.";
      loginForm.reset();
      openModalEl(loginModal);
    } else {
      registerError.hidden = true;
      registerForm.reset();
      openModalEl(registerModal);
    }
  }

  if (loginModalClose) loginModalClose.addEventListener("click", () => closeModalEl(loginModal));
  if (registerModalClose) registerModalClose.addEventListener("click", () => closeModalEl(registerModal));

  const switchToRegister = document.getElementById("switchToRegister");
  const switchToLogin = document.getElementById("switchToLogin");
  if (switchToRegister) {
    switchToRegister.addEventListener("click", (e) => { e.preventDefault(); openAuthModal("register"); });
  }
  if (switchToLogin) {
    switchToLogin.addEventListener("click", (e) => { e.preventDefault(); openAuthModal("login"); });
  }

  [loginModal, registerModal].forEach(m => {
    if (!m) return;
    m.addEventListener("click", (e) => { if (e.target === m) closeModalEl(m); });
  });

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      loginError.hidden = true;
      const submitBtn = loginForm.querySelector('button[type="submit"]');
      const originalLabel = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Logging in...";

      try {
        const formData = new FormData(loginForm);
        const response = await fetch("login.php", { method: "POST", body: formData });
        const result = await response.json();

        if (result.ok) {
          currentUser = result.user;
          renderAuthArea();
          closeModalEl(loginModal);
          if (afterAuthAction) { afterAuthAction(); afterAuthAction = null; }
        } else {
          loginError.textContent = result.error || "Could not log in. Please try again.";
          loginError.hidden = false;
        }
      } catch (err) {
        loginError.textContent = "Could not reach the server. Please try again.";
        loginError.hidden = false;
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;
      }
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      registerError.hidden = true;
      const submitBtn = registerForm.querySelector('button[type="submit"]');
      const originalLabel = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Creating account...";

      try {
        const formData = new FormData(registerForm);
        const response = await fetch("register.php", { method: "POST", body: formData });
        const result = await response.json();

        if (result.ok) {
          currentUser = result.user;
          renderAuthArea();
          closeModalEl(registerModal);
          if (afterAuthAction) { afterAuthAction(); afterAuthAction = null; }
        } else {
          registerError.textContent = result.error || "Could not create your account. Please try again.";
          registerError.hidden = false;
        }
      } catch (err) {
        registerError.textContent = "Could not reach the server. Please try again.";
        registerError.hidden = false;
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;
      }
    });
  }

  /* ---------- Reservation modal — connected to reserve.php, requires login ---------- */
  const reserveModal = document.getElementById("reserveModal");
  const modalClose = document.getElementById("modalClose");
  const modalCarName = document.getElementById("modalCarName");
  const reserveForm = document.getElementById("reserveForm");
  const reserveCarField = document.getElementById("reserveCarField");
  const modalSuccess = document.getElementById("modalSuccess");
  const modalError = document.getElementById("modalError");

  function openModal(carName) {
    if (!reserveModal) return;

    // Must be logged in to reserve — prompt login instead, and
    // reopen the reservation modal automatically once signed in.
    if (!currentUser) {
      afterAuthAction = () => openModal(carName);
      openAuthModal("login", "Please log in to reserve a vehicle.");
      return;
    }

    closeAllModals();
    modalCarName.textContent = carName
      ? `Reserving: ${carName}. Confirm your mobile number below.`
      : "Confirm your mobile number below and we'll get in touch.";
    if (reserveCarField) reserveCarField.value = carName || "";
    reserveForm.hidden = false;
    modalSuccess.hidden = true;
    modalError.hidden = true;
    reserveForm.reset();
    if (reserveCarField) reserveCarField.value = carName || "";
    openModalEl(reserveModal);
  }

  function closeModal() {
    closeModalEl(reserveModal);
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
  if (reserveModal) {
    reserveModal.addEventListener("click", (e) => {
      if (e.target === reserveModal) closeModal();
    });
  }
  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    [reserveModal, loginModal, registerModal].forEach(m => {
      if (m && m.classList.contains("open")) closeModalEl(m);
    });
  });

  if (reserveForm) {
    reserveForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      modalError.hidden = true;

      const submitBtn = reserveForm.querySelector('button[type="submit"]');
      const originalLabel = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending...";

      try {
        const formData = new FormData(reserveForm);
        const response = await fetch("reserve.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.ok) {
          reserveForm.hidden = true;
          modalSuccess.textContent = result.message || "Thanks! Your reservation request has been received.";
          modalSuccess.hidden = false;
          setTimeout(closeModal, 2200);
        } else if (result.requiresLogin) {
          // Session must have expired mid-form.
          const carName = reserveCarField ? reserveCarField.value : "";
          currentUser = null;
          renderAuthArea();
          closeModal();
          afterAuthAction = () => openModal(carName);
          openAuthModal("login", "Your session expired — please log in again to finish reserving.");
        } else {
          modalError.textContent = result.error || "Something went wrong. Please try again.";
          modalError.hidden = false;
        }
      } catch (err) {
        modalError.textContent = "Could not reach the server. Please check your connection and try again.";
        modalError.hidden = false;
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;
      }
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

  /* ---------- Newsletter form — now connected to subscribe.php ---------- */
  const newsletterForm = document.getElementById("newsletterForm");
  const newsletterMsg = document.getElementById("newsletterMsg");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      newsletterMsg.style.color = "";
      newsletterMsg.textContent = "";

      const submitBtn = newsletterForm.querySelector('button[type="submit"]');
      const originalLabel = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "...";

      try {
        const formData = new FormData(newsletterForm);
        const response = await fetch("subscribe.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.ok) {
          newsletterMsg.textContent = result.message || "Thanks for subscribing!";
          newsletterForm.reset();
        } else {
          newsletterMsg.style.color = "#E4574C";
          newsletterMsg.textContent = result.error || "Something went wrong. Please try again.";
        }
      } catch (err) {
        newsletterMsg.style.color = "#E4574C";
        newsletterMsg.textContent = "Could not reach the server. Please try again.";
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;
      }
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

  checkSession();
});