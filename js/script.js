 const openPopup = (e) => {
        if (e) e.preventDefault();
        if (wrapper) wrapper.classList.add("show");
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
        closeBtn.addEventListener("click", () => {
            wrapper.classList.remove("show");
            wrapper.classList.remove("active");
        });
      }
    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            wrapper.classList.remove("show");
            wrapper.classList.remove("active");
        });
    }
    if (registerLink) {
        registerLink.addEventListener("click", (e) => {
            e.preventDefault();
            wrapper.classList.add("active");
        });
    }
    if (loginLink) {
        loginLink.addEventListener("click", (e) => {
            e.preventDefault();
            wrapper.classList.remove("active");
        });
    }
});


