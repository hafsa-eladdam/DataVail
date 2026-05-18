document.addEventListener("DOMContentLoaded", function() {
    
    // 1. GESTION DE L'ANIMATION SPLIT SCREEN (LOGIN / REGISTER)
    const sign_in_btn = document.querySelector("#sign-in-btn");
    const sign_up_btn = document.querySelector("#sign-up-btn");
    const container = document.querySelector(".container-anim");

    if (sign_up_btn && sign_in_btn && container) {
        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });
    }

    // 2. GESTION DE LA NAVBAR AU SCROLL (Effet visuel)
    const nav = document.querySelector('nav');
    if (nav) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                nav.style.padding = "10px 5%"; // Réduit un peu la taille
                nav.style.boxShadow = "0 5px 20px rgba(0,0,0,0.2)";
            } else {
                nav.style.padding = "15px 5%"; // Taille normale
                nav.style.boxShadow = "none";
            }
        });
    }
});