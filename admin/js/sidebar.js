document.addEventListener("DOMContentLoaded", function () {

    const submenuPanel = document.getElementById("submenuPanel");
    const menuLinks = document.querySelectorAll(".menu-link");

    menuLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            const toggleId = link.getAttribute("data-toggle");
            const submenu = document.getElementById(toggleId);
            const chevron = link.querySelector(".chevron");

            if (window.innerWidth <= 768) {
                e.stopPropagation();
                submenuPanel.innerHTML = submenu.innerHTML;
                submenuPanel.style.display = "block";
                return;
            }

            document.querySelectorAll(".submenu").forEach(sub => {
                if (sub !== submenu) sub.classList.remove("show");
            });

            document.querySelectorAll(".chevron").forEach(ch => {
                if (ch !== chevron) ch.classList.remove("rotate");
            });

            submenu.classList.toggle("show");
            chevron.classList.toggle("rotate");
        });
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".submenu-panel") && !e.target.closest(".menu-link")) {
            submenuPanel.style.display = "none";
        }
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) submenuPanel.style.display = "none";
    });

});
const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');

profileBtn.addEventListener('click', (e) => {
    e.stopPropagation();

    if (window.innerWidth <= 768) {
        submenuPanel.innerHTML = profileDropdown.innerHTML;
        submenuPanel.style.display = "block";
        return;
    }

    profileDropdown.classList.toggle('show');
});

document.addEventListener('click', (e) => {
    if (window.innerWidth > 768) {
        if (!profileBtn.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    }
});

document.addEventListener("click", function (e) {
    if (window.innerWidth <= 768 &&
        !e.target.closest(".submenu-panel") &&
        !e.target.closest(".menu-link") &&
        !e.target.closest("#profileBtn")
    ) {
        submenuPanel.style.display = "none";
    }
});
