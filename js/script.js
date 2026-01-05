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
