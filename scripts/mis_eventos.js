document.addEventListener("DOMContentLoaded", function () {
    const contenedorEventos = document.getElementById("contenedor-mis-eventos");
    const filtroCampus = document.getElementById("filtro-campus");
    const filtroEstado = document.getElementById("filtro-estado");
    const toggleFiltersButton = document.getElementById("toggle-filters");
    const clearFiltersButton = document.getElementById("clear-filters");
    const filters = document.getElementById("filters");

    toggleFiltersButton.addEventListener("click", function () {
        if (filters.style.display === "none") {
            filters.style.display = "block";
            toggleFiltersButton.textContent = "Ocultar Filtros";
        } else {
            filters.style.display = "none";
            toggleFiltersButton.textContent = "Mostrar Filtros";
        }
    });

    clearFiltersButton.addEventListener("click", function () {
        filtroCampus.value = "";
        filtroEstado.value = "todos";
        cargarMisEventos();
    });

    function cargarMisEventos() {
        fetch("actions/mis_eventos.php")
            .then(response => response.json())
            .then(eventos => {
                contenedorEventos.innerHTML = "";
                eventos.forEach(evento => {
                    if (filtrarEvento(evento)) {
                        const card = document.createElement("div");
                        card.classList.add("card");
                        card.innerHTML = `
                        <div class="img"></div>
                        <div class="text">
                            <h3 class="h3">${evento.nombre}</h3>
                            <p class="p"><strong>Fecha:</strong> ${evento.fecha}</p>
                            <p class="p"><strong>Horario:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
                            <p class="p"><strong>Lugar:</strong> ${evento.lugar}</p>
                            <p class="p"><strong>Campus:</strong> ${evento.campus}</p>
                            ${evento.cancelable ? `<button class="cancelar-btn" data-id="${evento.id}">Cancelar</button>` : "<p>Evento pasado - No se puede cancelar</p>"}
                        </div>
                    `;
                        contenedorEventos.appendChild(card);
                    }
                });
                document.querySelectorAll(".cancelar-btn").forEach(btn => {
                    btn.addEventListener("click", function () {
                        cancelarEvento(this.dataset.id);
                    });
                });
            });
    }

    function filtrarEvento(evento) {
        const campusSeleccionado = filtroCampus.value;
        const estadoSeleccionado = filtroEstado.value;
        const esCancelable = evento.cancelable;

        return (campusSeleccionado === "" || evento.campus === campusSeleccionado) &&
            (estadoSeleccionado === "todos" ||
                (estadoSeleccionado === "vigentes" && esCancelable) ||
                (estadoSeleccionado === "pasados" && !esCancelable));
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

    filtroCampus.addEventListener("change", cargarMisEventos);
    filtroEstado.addEventListener("change", cargarMisEventos);

    cargarMisEventos();
});