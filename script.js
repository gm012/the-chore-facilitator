// ================================================================
// THE CHORE FACILITATOR
// FRONTEND INTERACTIONS
// ================================================================


// ================================================================
// MOBILE NAVIGATION
// ================================================================

const menuToggle = document.getElementById("menuToggle");
const mainNav = document.getElementById("mainNav");

if (menuToggle && mainNav) {

    menuToggle.addEventListener("click", () => {

        const isOpen = mainNav.classList.toggle("open");

        menuToggle.setAttribute(
            "aria-expanded",
            String(isOpen)
        );

    });


    // Close navigation after selecting a page
    mainNav.querySelectorAll("a").forEach((link) => {

        link.addEventListener("click", () => {

            mainNav.classList.remove("open");

            menuToggle.setAttribute(
                "aria-expanded",
                "false"
            );

        });

    });

}


// ================================================================
// HIDE HEADER ON SCROLL DOWN
// SHOW HEADER ON SCROLL UP
// ================================================================

const siteHeader = document.querySelector(".site-header");

let lastScrollY = window.scrollY;
let ticking = false;

const headerHideThreshold = 80;
const scrollDifferenceThreshold = 5;


function updateHeader() {

    if (!siteHeader) {
        return;
    }


    const currentScrollY = window.scrollY;

    const scrollDifference =
        currentScrollY - lastScrollY;


    // ------------------------------------------------------------
    // At the top of the page:
    // Always show the header.
    // ------------------------------------------------------------

    if (currentScrollY <= headerHideThreshold) {

        siteHeader.classList.remove("header-hidden");
        siteHeader.classList.remove("header-scrolled");

        lastScrollY = currentScrollY;

        ticking = false;

        return;

    }


    // Page has moved — allow subtle header shadow
    siteHeader.classList.add("header-scrolled");


    // ------------------------------------------------------------
    // Don't hide header while mobile navigation is open
    // ------------------------------------------------------------

    const mobileMenuOpen =
        mainNav &&
        mainNav.classList.contains("open");


    if (mobileMenuOpen) {

        siteHeader.classList.remove("header-hidden");

        lastScrollY = currentScrollY;

        ticking = false;

        return;

    }


    // ------------------------------------------------------------
    // SCROLLING DOWN
    // ------------------------------------------------------------

    if (
        scrollDifference >
        scrollDifferenceThreshold
    ) {

        siteHeader.classList.add(
            "header-hidden"
        );

    }


    // ------------------------------------------------------------
    // SCROLLING UP
    // ------------------------------------------------------------

    else if (
        scrollDifference <
        -scrollDifferenceThreshold
    ) {

        siteHeader.classList.remove(
            "header-hidden"
        );

    }


    lastScrollY = currentScrollY;

    ticking = false;

}


window.addEventListener(
    "scroll",
    () => {

        if (!ticking) {

            window.requestAnimationFrame(
                updateHeader
            );

            ticking = true;

        }

    },
    {
        passive: true
    }
);


// ================================================================
// FOOTER YEAR
// ================================================================

const yearElement =
    document.getElementById("year");

if (yearElement) {

    yearElement.textContent =
        new Date().getFullYear();

}


// ================================================================
// SCROLL REVEAL
// ================================================================

const revealItems =
    document.querySelectorAll(
        ".service-card, " +
        ".image-service-card, " +
        ".process-step, " +
        ".trust-points > div"
    );


if (
    "IntersectionObserver" in window
) {

    const revealObserver =
        new IntersectionObserver(
            (entries, observer) => {

                entries.forEach(
                    (entry) => {

                        if (
                            !entry.isIntersecting
                        ) {
                            return;
                        }


                        entry.target
                            .classList
                            .add("visible");


                        observer.unobserve(
                            entry.target
                        );

                    }
                );

            },
            {
                threshold: 0.15
            }
        );


    revealItems.forEach(
        (item) => {

            item.classList.add(
                "reveal"
            );

            revealObserver.observe(
                item
            );

        }
    );

}


/* ================================================================
   REDUCED MOTION
================================================================ */

const prefersReducedMotion =
    window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    );


if (prefersReducedMotion.matches) {

    revealItems.forEach(
        (item) => {

            item.classList.add(
                "visible"
            );

        }
    );

}