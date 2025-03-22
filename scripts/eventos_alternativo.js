document.addEventListener("DOMContentLoaded", function () {
    const contenedorEventos = document.getElementById("contenedor-eventos-alternativo");
    const carritoEventos = document.getElementById("carrito-eventos");
    const listaEventosSeleccionados = document.getElementById("lista-eventos-seleccionados");
    const formInscripcion = document.getElementById("form-inscripcion");

    function cargarEventos() {
        fetch("actions/eventos.php")
            .then(response => response.json())
            .then(data => {
                console.log("Eventos parseados:", data);
                // Si la respuesta tiene "result", usamos esa propiedad; de lo contrario, asumimos que data es un arreglo
                const eventos = data.result ? data.result : data;
                contenedorEventos.innerHTML = ""; // Limpiar contenedor

                // Opcional: Filtrar para mostrar solo eventos futuros
                const now = new Date();
                eventos.forEach(evento => {
                    const eventDate = new Date(evento.fecha);
                    if (eventDate >= now) {
                        const col = document.createElement("div");
                        col.classList.add("col", "s12", "m6", "l4");

                        const card = document.createElement("div");
                        card.classList.add("card", "hoverable");
                        card.innerHTML = `
              <div class="card-header">
                <span class="card-title">${evento.nombre}</span>
                <div class="evento-badge campus-${evento.campus.toLowerCase()}">${evento.campus}</div>
              </div>
              <div class="card-content">
                <div class="card-detail">
                  <i class="material-icons">group</i>
                  <span>Capacidad: ${evento.capacidad}</span>
                </div>
                <div class="card-detail">
                  <i class="material-icons">event</i>
                  <span>Fecha: ${evento.fecha}</span>
                </div>
                <div class="card-detail">
                  <i class="material-icons">access_time</i>
                  <span>Horario: ${evento.hora_inicio} - ${evento.hora_fin}</span>
                </div>
                <div class="card-detail">
                  <i class="material-icons">place</i>
                  <span>Lugar: ${evento.lugar}</span>
                </div>
              </div>
              <div class="card-action">
                <a href="index.php?view=detalles_evento&id=${evento.id}" class="teal-text detalles-link">Detalles</a>
                <label>
                  <input type="checkbox" name="eventos" value="${evento.id}" class="filled-in">
                  <span>Seleccionar</span>
                </label>
              </div>
            `;
                        col.appendChild(card);
                        contenedorEventos.appendChild(col);
                    }
                });

                ajustarAlturaTarjetas();
                agregarEventListenersCheckboxes();
                actualizarCarrito();
                mostrarCarritoSiHayEventosSeleccionados();
            })
            .catch(error => {
                console.error("Error al cargar eventos:", error);
            });
    }

    function ajustarAlturaTarjetas() {
        const rows = document.querySelectorAll(".row");
        rows.forEach(row => {
            const cards = row.querySelectorAll(".card");
            let maxHeight = 0;
            cards.forEach(card => {
                card.style.height = "auto";
                if (card.offsetHeight > maxHeight) {
                    maxHeight = card.offsetHeight;
                }
            });
            cards.forEach(card => {
                card.style.height = `${maxHeight}px`;
            });
        });
    }

    function agregarEventListenersCheckboxes() {
        const checkboxes = document.querySelectorAll('input[name="eventos"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", () => {
                actualizarCarrito();
                mostrarCarritoSiHayEventosSeleccionados();
            });
        });
    }

    function actualizarCarrito() {
        listaEventosSeleccionados.innerHTML = "";
        const checkboxes = document.querySelectorAll('input[name="eventos"]:checked');
        checkboxes.forEach(checkbox => {
            const card = checkbox.closest(".card");
            const nombreEvento = card.querySelector(".card-title").textContent;
            const li = document.createElement("li");
            li.textContent = nombreEvento;
            listaEventosSeleccionados.appendChild(li);
        });
    }

    function mostrarCarritoSiHayEventosSeleccionados() {
        const checkboxes = document.querySelectorAll('input[name="eventos"]:checked');
        carritoEventos.style.display = (checkboxes.length > 0) ? "block" : "none";
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
                    cargarEventos(); // Recargar para actualizar la lista
                }
            })
            .catch(error => {
                console.error("Error al procesar la inscripción:", error);
                alert("Error al procesar la inscripción. Inténtalo de nuevo más tarde.");
            });
    });

    // Cargar eventos al inicio
    cargarEventos();
});
