/**
 * script.js — shared front-end behavior for every page (included by
 * footer.php). Talks to the PHP AJAX endpoints (session.php, login.php,
 * register.php, logout.php, reserve.php, contact_function.php,
 * subscribe.php) and keeps the header auth area / modals in sync.
 */

let currentUser = null;      // { name, email } or null
let pendingReserveCar = null; // car name the visitor tried to rent before logging in

// ===================== Element refs (not every page has every one) =====================
const headerAuthArea = document.getElementById("headerAuthArea");
const siteHeader = document.getElementById("siteHeader");
const navToggle = document.getElementById("navToggle");
const mainNav = document.getElementById("mainNav");
const backToTop = document.getElementById("backToTop");

const loginModal = document.getElementById("loginModal");
const registerModal = document.getElementById("registerModal");
const reserveModal = document.getElementById("reserveModal");

const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const reserveForm = document.getElementById("reserveForm");
const contactForm = document.getElementById("contactForm");
const newsletterForm = document.getElementById("newsletterForm");

// ===================== Utilities =====================
function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
}

function setMessage(el, text, hidden) {
    if (!el) return;
    el.textContent = text || "";
    el.hidden = hidden === undefined ? !text : hidden;
}

async function postForm(url, formData) {
    try {
        const res = await fetch(url, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });
        return await res.json();
    } catch (err) {
        return { ok: false, error: "Something went wrong. Please try again." };
    }
}

// ===================== Modals =====================
function openModal(modal) {
    if (!modal) return;
    closeAllModals();
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
}

function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
}

function closeAllModals() {
    [loginModal, registerModal, reserveModal].forEach(closeModal);
}

function openAuthModal(mode) {
    if (mode === "register") {
        openModal(registerModal);
    } else {
        openModal(loginModal);
    }
}

// Close on backdrop click or any .modal-close button.
document.querySelectorAll(".modal").forEach((modal) => {
    modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal(modal);
    });
});
document.querySelectorAll(".modal-close").forEach((btn) => {
    btn.addEventListener("click", () => closeModal(btn.closest(".modal")));
});
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAllModals();
});

const switchToRegister = document.getElementById("switchToRegister");
if (switchToRegister) {
    switchToRegister.addEventListener("click", (e) => {
        e.preventDefault();
        openAuthModal("register");
    });
}

const switchToLogin = document.getElementById("switchToLogin");
if (switchToLogin) {
    switchToLogin.addEventListener("click", (e) => {
        e.preventDefault();
        openAuthModal("login");
    });
}

// ===================== Auth area (header) =====================
function renderAuthArea() {
    if (!headerAuthArea) return;

    if (currentUser) {
        headerAuthArea.innerHTML = `
      <span class="user-greeting">
        Hi, ${escapeHtml(currentUser.name.split(" ")[0])}
      </span>
      <button
        type="button"
        class="btn btn-outline-navy btn-sm"
        id="logoutBtn">
        Log Out
      </button>
    `;

        document
            .getElementById("logoutBtn")
            .addEventListener("click", handleLogout);
    } else {
        headerAuthArea.innerHTML = `
      <button
        type="button"
        class="btn btn-outline-navy btn-sm"
        id="loginTrigger">
        Log In
      </button>

      <button
        type="button"
        class="btn btn-primary btn-sm"
        id="registerTrigger">
        Register
      </button>
    `;

        document
            .getElementById("loginTrigger")
            .addEventListener("click", () => openAuthModal("login"));

        document
            .getElementById("registerTrigger")
            .addEventListener("click", () => openAuthModal("register"));
    }
}

// Ask the server whether we're logged in (session cookie), then draw the
// header accordingly. Runs once on every page load.
async function checkSession() {
    try {
        const res = await fetch("session.php", { credentials: "same-origin" });
        const data = await res.json();
        currentUser = data.ok && data.loggedIn ? data.user : null;
    } catch (err) {
        currentUser = null;
    }
    renderAuthArea();
}

async function handleLogout() {
    const confirmed = window.confirm("Are you sure you want to log out?");
    if (!confirmed) return;

    await postForm("logout.php", new FormData());
    currentUser = null;
    renderAuthArea();
}

// ===================== Login / Register forms =====================
if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        setMessage(document.getElementById("loginError"), "");

        const data = await postForm("login.php", new FormData(loginForm));

        if (data.ok) {
            currentUser = data.user;
            renderAuthArea();
            loginForm.reset();
            closeModal(loginModal);

            if (pendingReserveCar) {
                const car = pendingReserveCar;
                pendingReserveCar = null;
                openReserveModal(car);
            }
        } else {
            setMessage(document.getElementById("loginError"), data.error || "Could not log in.");
        }
    });
}

if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        setMessage(document.getElementById("registerError"), "");

        const data = await postForm("register.php", new FormData(registerForm));

        if (data.ok) {
            currentUser = data.user;
            renderAuthArea();
            registerForm.reset();
            closeModal(registerModal);

            if (pendingReserveCar) {
                const car = pendingReserveCar;
                pendingReserveCar = null;
                openReserveModal(car);
            }
        } else {
            setMessage(document.getElementById("registerError"), data.error || "Could not create your account.");
        }
    });
}

// ===================== Reserve modal =====================
function todayIso() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

// Fills the reservation modal's color dropdown with the colors that
// are actually in stock for the chosen car (window.CAR_COLORS is
// rendered server-side in footer.php from the same stock data the
// fleet page uses).
function populateColorOptions(car) {
    const colorField = document.getElementById("reserveColor");
    if (!colorField) return;

    colorField.innerHTML = '<option value="">Select color</option>';
    const colors = (window.CAR_COLORS && window.CAR_COLORS[car]) || [];
    colors.forEach((color) => {
        const opt = document.createElement("option");
        opt.value = color;
        opt.textContent = color;
        colorField.appendChild(opt);
    });
}

function openReserveModal(car) {
    if (!reserveModal) return;

    const carField = document.getElementById("reserveCarField");
    const carNameEl = document.getElementById("modalCarName");
    const titleEl = document.getElementById("modalTitle");
    const successEl = document.getElementById("modalSuccess");
    const errorEl = document.getElementById("modalError");
    const pickupDateField = document.getElementById("reservePickupDate");
    const returnDateField = document.getElementById("reserveReturnDate");

    if (titleEl) titleEl.textContent = car ? `Reserve the ${car}` : "Reserve a Vehicle";
    if (carNameEl) {
        carNameEl.textContent = car
            ? `Complete your details and we'll confirm your ${car} booking shortly.`
            : "Complete your details and we'll confirm your booking shortly.";
    }
    setMessage(errorEl, "");
    if (successEl) successEl.hidden = true;
    if (reserveForm) {
        reserveForm.reset();
        reserveForm.hidden = false;
    }
    if (carField) carField.value = car || "";
    populateColorOptions(car || "");

    // Never let someone pick a pick-up/return date in the past.
    const today = todayIso();
    if (pickupDateField) {
        pickupDateField.min = today;
        pickupDateField.value = today;
    }
    if (returnDateField) {
        returnDateField.min = today;
        returnDateField.value = today;
    }

    openModal(reserveModal);
}

// Keep the return date from ever being set before the (possibly updated)
// pick-up date.
const reservePickupDateEl = document.getElementById("reservePickupDate");
const reserveReturnDateEl = document.getElementById("reserveReturnDate");
if (reservePickupDateEl && reserveReturnDateEl) {
    reservePickupDateEl.addEventListener("change", () => {
        reserveReturnDateEl.min = reservePickupDateEl.value || todayIso();
        if (reserveReturnDateEl.value && reserveReturnDateEl.value < reserveReturnDateEl.min) {
            reserveReturnDateEl.value = reserveReturnDateEl.min;
        }
    });
}

document.querySelectorAll(".rent-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
        const car = btn.dataset.car || "";
        if (!currentUser) {
            pendingReserveCar = car;
            const msg = document.getElementById("loginModalMsg");
            if (msg) msg.textContent = `Log in to reserve the ${car}.`;
            openAuthModal("login");
            return;
        }
        openReserveModal(car);
    });
});

if (reserveForm) {
    reserveForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const errorEl = document.getElementById("modalError");
        const successEl = document.getElementById("modalSuccess");
        setMessage(errorEl, "");

        const data = await postForm("reserve.php", new FormData(reserveForm));

        if (data.ok) {
            reserveForm.hidden = true;
            if (successEl) successEl.hidden = false;
        } else if (data.requiresLogin) {
            closeModal(reserveModal);
            const msg = document.getElementById("loginModalMsg");
            const carField = document.getElementById("reserveCarField");
            pendingReserveCar = carField ? carField.value : null;
            if (msg) msg.textContent = "Please log in to reserve a vehicle.";
            openAuthModal("login");
        } else {
            setMessage(errorEl, data.error || "Could not save your reservation.");
        }
    });
}

// ===================== Contact form =====================
if (contactForm) {
    contactForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const errorEl = document.getElementById("contactError");
        const successEl = document.getElementById("contactSuccess");
        setMessage(errorEl, "");
        setMessage(successEl, "");

        const data = await postForm("contact_function.php", new FormData(contactForm));

        if (data.ok) {
            setMessage(successEl, data.message || "Thanks! We'll be in touch soon.", false);
            contactForm.reset();
        } else {
            setMessage(errorEl, data.error || "Could not send your message.", false);
        }
    });
}

// ===================== Newsletter form =====================
if (newsletterForm) {
    newsletterForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const msgEl = document.getElementById("newsletterMsg");
        setMessage(msgEl, "");

        const data = await postForm("subscribe.php", new FormData(newsletterForm));

        setMessage(msgEl, data.message || data.error || "", false);
        if (data.ok) newsletterForm.reset();
    });
}

// ===================== Mobile nav toggle =====================
if (navToggle && siteHeader) {
    navToggle.addEventListener("click", () => {
        const isOpen = siteHeader.classList.toggle("nav-open");
        navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    if (mainNav) {
        mainNav.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                siteHeader.classList.remove("nav-open");
                navToggle.setAttribute("aria-expanded", "false");
            });
        });
    }
}

// ===================== Back to top =====================
if (backToTop) {
    window.addEventListener("scroll", () => {
        backToTop.classList.toggle("visible", window.scrollY > 400);
    });

    backToTop.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

// ===================== My Reservations page =====================
const reservationsLoginTrigger = document.getElementById("reservationsLoginTrigger");
if (reservationsLoginTrigger) {
    reservationsLoginTrigger.addEventListener("click", () => openAuthModal("login"));
}

document.querySelectorAll(".cancel-reservation-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        if (!id) return;

        const confirmed = window.confirm("Cancel this reservation? This can't be undone.");
        if (!confirmed) return;

        btn.disabled = true;
        btn.textContent = "Cancelling…";

        const formData = new FormData();
        formData.append("reservation_id", id);
        const data = await postForm("cancel_reservation.php", formData);

        if (data.ok) {
            const card = btn.closest(".reservation-card");
            if (card) {
                card.classList.add("cancelled");
                const badge = card.querySelector(".reservation-badge");
                if (badge) {
                    badge.textContent = "Cancelled";
                    badge.classList.remove("status-confirmed");
                    badge.classList.add("status-cancelled");
                }
            }
            btn.remove();
        } else if (data.requiresLogin) {
            openAuthModal("login");
            btn.disabled = false;
            btn.textContent = "Cancel Reservation";
        } else {
            alert(data.error || "Could not cancel your reservation. Please try again.");
            btn.disabled = false;
            btn.textContent = "Cancel Reservation";
        }
    });
});

// ===================== Init =====================
checkSession();
