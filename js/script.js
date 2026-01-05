document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.querySelector(".wrapper");
    const loginBtn = document.querySelector(".login-btn");
    const publishLink = document.querySelector(".btn-publish");
    const closeBtn = document.querySelector(".icon-close");
    const registerLink = document.querySelector(".register-link");
    const loginLink = document.querySelector(".login-link");
    const user = JSON.parse(localStorage.getItem("user"));
