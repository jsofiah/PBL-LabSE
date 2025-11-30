document.addEventListener("DOMContentLoaded", () => {

    const mobileNavToggle = document.querySelector(".mobile-nav-toggle");
    const mobileNav = document.querySelector(".mobile-nav");
    const overlay = document.querySelector(".overlay");
    const mobileDropdownBtns = document.querySelectorAll(".mobile-dropdown-btn");
    const desktopDropdowns = document.querySelectorAll(".dropdown");

    if (mobileNavToggle && mobileNav) {
        mobileNavToggle.addEventListener("click", () => {
            const icon = mobileNavToggle.querySelector("i");

            mobileNav.classList.toggle("active");
            overlay?.classList.toggle("active");
            document.body.style.overflow = mobileNav.classList.contains("active") ? "hidden" : "";

            if (icon) {
                icon.classList.toggle("bi-list", !mobileNav.classList.contains("active"));
                icon.classList.toggle("bi-x", mobileNav.classList.contains("active"));
            }

            mobileNavToggle.classList.toggle("active", mobileNav.classList.contains("active"));
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            closeMobileNav();
        });
    }

    function closeMobileNav() {
        if (!mobileNavToggle || !mobileNav) return;

        mobileNav.classList.remove("active");
        overlay?.classList.remove("active");
        document.body.style.overflow = "";

        const icon = mobileNavToggle.querySelector("i");
        if (icon) {
            icon.classList.remove("bi-x");
            icon.classList.add("bi-list");
        }

        mobileNavToggle.classList.remove("active");

        document.querySelectorAll(".mobile-dropdown-content").forEach(d => d.classList.remove("active"));
        document.querySelectorAll(".mobile-dropdown-btn").forEach(b => b.classList.remove("active"));
    }

    mobileDropdownBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const content = btn.nextElementSibling;
            const isOpen = content.classList.contains("active");

            document.querySelectorAll(".mobile-dropdown-content").forEach(d => d.classList.remove("active"));
            document.querySelectorAll(".mobile-dropdown-btn").forEach(b => b.classList.remove("active"));

            if (!isOpen) {
                btn.classList.add("active");
                content.classList.add("active");
            }
        });
    });

    desktopDropdowns.forEach(dropdown => {
        const dropbtn = dropdown.querySelector(".dropbtn");
        const dropdownContent = dropdown.querySelector(".dropdown-content");
        if (!dropbtn || !dropdownContent) return;

        dropdown.addEventListener("mouseenter", () => {
            dropdownContent.classList.add("show");
        });

        dropdown.addEventListener("mouseleave", () => {
            dropdownContent.classList.remove("show");
        });

        dropdown.addEventListener("touchstart", e => {
            if (window.innerWidth > 1024) {
                e.preventDefault();
                dropdownContent.classList.toggle("show");
            }
        });
    });

    document.addEventListener("click", e => {
        if (!e.target.closest(".dropdown") && window.innerWidth > 1024) {
            document.querySelectorAll(".dropdown-content").forEach(d => d.classList.remove("show"));
        }
    });

    document.querySelectorAll(".mobile-nav a").forEach(link => {
        link.addEventListener("click", () => {
            closeMobileNav();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 1024) {
            closeMobileNav();
        }
    });

});
