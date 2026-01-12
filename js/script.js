document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.querySelector(".wrapper");
    const loginBtn = document.querySelector(".login-btn");
    const closeBtn = document.querySelector(".icon-close");
    const registerLink = document.querySelector(".register-link");
    const loginLink = document.querySelector(".login-link");
    const publishLink = document.querySelector(".btn-publish");
    const overlay = document.querySelector(".wrapper-overlay");
    const user = JSON.parse(localStorage.getItem("user"));

    const openPopup = (e) => {
        if (e) e.preventDefault();
        if (wrapper) wrapper.classList.add("show");
        if (overlay) overlay.classList.add("show");
    };

    const closePopup = () => {
        if (wrapper) {
            wrapper.classList.remove("show");
            wrapper.classList.remove("active");
        }
        if (overlay) overlay.classList.remove("show");
    };

    if (loginBtn && !user) {
        loginBtn.addEventListener("click", openPopup);
    }

    if (publishLink) {
        publishLink.addEventListener("click", (e) => {
            e.preventDefault();
            if (user) {
                window.location.href = "ajouter.html";
            } else {
                openPopup();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", closePopup);
    }

    if (overlay) {
        overlay.addEventListener("click", closePopup);
    }

    if (registerLink) {
        registerLink.addEventListener("click", (e) => {
            e.preventDefault();
            if (wrapper) wrapper.classList.add("active");
        });
    }

    if (loginLink) {
        loginLink.addEventListener("click", (e) => {
            e.preventDefault();
            if (wrapper) wrapper.classList.remove("active");
        });
    }
});