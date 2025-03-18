document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger");
    const overlay = document.getElementById("myOverlay");
    const overlayClose = document.getElementById("overlayClose");

    // Al hacer clic en el botón hamburguesa -> mostrar overlay
    hamburger.addEventListener("click", () => {
        overlay.classList.add("my-overlay--active");
    });

    // Al hacer clic en el botón de cerrar -> ocultar overlay
    overlayClose.addEventListener("click", () => {
        overlay.classList.remove("my-overlay--active");
    });
});
