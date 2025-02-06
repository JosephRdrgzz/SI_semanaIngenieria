document.addEventListener("DOMContentLoaded", function () {
    const contenedorEventos = document.getElementById("contenedor-eventos");
    const filtroCampus = document.getElementById("filtro-campus");
    const filtroFecha = document.getElementById("filtro-fecha");
    const formInscripcion = document.getElementById("form-inscripcion");

    function cargarEventos() {
        fetch("actions/eventos.php")
            .then(response => response.json())
            .then(eventos => {
                contenedorEventos.innerHTML = "";
                eventos.forEach(evento => {
                    if (filtrarEvento(evento)) {
                        const card = document.createElement("div");
                        card.classList.add("evento-card");
                        card.innerHTML = `
                            <input type="checkbox" name="eventos" value="${evento.id}">
                            <h3>${evento.nombre}</h3>
                            <p><strong>Fecha:</strong> ${evento.fecha}</p>
                            <p><strong>Horario:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
                            <p><strong>Lugar:</strong> ${evento.lugar}</p>
                            <p><strong>Campus:</strong> ${evento.campus}</p>
                        `;
                        contenedorEventos.appendChild(card);
                    }
                });
            });
    }

    function filtrarEvento(evento) {
        const campusSeleccionado = filtroCampus.value;
        const fechaSeleccionada = filtroFecha.value;

        return (campusSeleccionado === "" || evento.campus === campusSeleccionado) &&
            (fechaSeleccionada === "" || evento.fecha === fechaSeleccionada);
    }

    formInscripcion.addEventListener("submit", function (e) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('input[name="eventos"]:checked');
        const eventosSeleccionados = Array.from(checkboxes).map(cb => cb.value);

        if (eventosSeleccionados.length === 0) {
            alert("Selecciona al menos un evento");
            return;
        }

        fetch("actions/inscribir.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ eventos: eventosSeleccionados })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.error);
                } else {
                    alert("Inscripción exitosa");
                    cargarEventos();
                }
            });
    });

    filtroCampus.addEventListener("change", cargarEventos);
    filtroFecha.addEventListener("change", cargarEventos);

    cargarEventos();
});
