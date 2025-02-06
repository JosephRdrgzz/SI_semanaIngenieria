document.addEventListener("DOMContentLoaded", function () {
    const contenedorMisEventos = document.getElementById("contenedor-mis-eventos");

    function cargarMisEventos() {
        fetch("actions/mis_eventos.php")
            .then(response => response.json())
            .then(eventos => {
                contenedorMisEventos.innerHTML = "";
                eventos.forEach(evento => {
                    const card = document.createElement("div");
                    card.classList.add("evento-card");
                    card.innerHTML = `
                        <h3>${evento.nombre}</h3>
                        <p><strong>Fecha:</strong> ${evento.fecha}</p>
                        <p><strong>Horario:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
                        <p><strong>Lugar:</strong> ${evento.lugar}</p>
                        <p><strong>Campus:</strong> ${evento.campus}</p>
                        ${evento.cancelable ? `<button class="cancelar-btn" data-id="${evento.id}">Cancelar</button>` : "<p>Evento pasado - No se puede cancelar</p>"}
                    `;
                    contenedorMisEventos.appendChild(card);
                });

                document.querySelectorAll(".cancelar-btn").forEach(btn => {
                    btn.addEventListener("click", function () {
                        cancelarEvento(this.dataset.id);
                    });
                });
            });
    }

    function cancelarEvento(evento_id) {
        fetch("actions/cancelar.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ evento_id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Inscripción cancelada");
                    cargarMisEventos();
                } else {
                    alert("Error: " + data.error);
                }
            });
    }

    cargarMisEventos();
});
