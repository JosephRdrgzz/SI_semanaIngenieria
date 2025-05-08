document.addEventListener("DOMContentLoaded", function() {
    const contenedorEventos = document.getElementById("contenedor-eventos-alternativo");
    const carritoEventos = document.getElementById("carrito-eventos");
    const listaEventosSeleccionados = document.getElementById("lista-eventos-seleccionados");
    const formInscripcion = document.getElementById("form-inscripcion");

    // Elementos de filtros
    const filtroCampus = document.getElementById("filtro-campus");
    const filtroFecha = document.getElementById("filtro-fecha");
    const filtroTipoEvento = document.getElementById("filtro-tipo-evento");
    const btnLimpiar = document.getElementById("btn-limpiar-filtros");
    const filtroPalabra = document.getElementById("filtro-palabra");

    let allEventos = [];

    function cargarEventos() {
        fetch("actions/eventos.php")
            .then(response => response.json())
            .then(data => {
                console.log("Eventos parseados:", data);
                allEventos = data.result ? data.result : data;
                renderEventos();
            })
            .catch(error => {
                console.error("Error al cargar eventos:", error);
            });
    }

    function renderEventos() {
        const now = new Date();
        const futureThreeHours = new Date(now.getTime() + (3 * 60 * 60 * 1000));

        const campusVal = filtroCampus.value;
        const fechaVal = filtroFecha.value; // "YYYY-MM-DD"
        const tipoVal = filtroTipoEvento.value;
        const palabraVal = filtroPalabra ? filtroPalabra.value.toLowerCase() : "";

        const eventosFiltrados = allEventos.filter(evento => {
            const eventDate = new Date(evento.fecha + 'T' + evento.hora_fin);
            const validTime = fechaVal !== "" || eventDate > futureThreeHours;
            const matchCampus = (campusVal === "" || evento.campus.toLowerCase() === campusVal.toLowerCase());
            const matchFecha  = (fechaVal === "" || evento.fecha === fechaVal);
            const matchTipo   = (tipoVal === ""  || evento.tipo_evento === tipoVal);
            const matchPalabra= (palabraVal === "" || evento.nombre.toLowerCase().includes(palabraVal));
            return validTime && matchCampus && matchFecha && matchTipo && matchPalabra;
        });

        contenedorEventos.innerHTML = "";
        eventosFiltrados.forEach(evento => {
            const card = document.createElement("div");
            card.classList.add("card");

            // Sección de imagen (altura fija para consistencia)
            const imgSection = evento.has_image
                ? `<div class="card-media">
                     <img src="admin/${evento.imagen_path}" alt="${evento.nombre}">
                   </div>`
                : `<div class="card-media"></div>`;

            card.innerHTML = `
                ${imgSection}
                <div class="card-header">
                  <span class="card-title">${evento.nombre}</span>
                  <div class="evento-badge campus-${evento.campus.toLowerCase()}">
                    ${evento.campus}
                  </div>
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
                  <a href="index.php?view=detalles_evento&id=${evento.id}"
                     class="teal-text detalles-link">Detalles</a>
                  <label>
                    <input type="checkbox" name="eventos" value="${evento.id}">
                    <span>Seleccionar</span>
                  </label>
                </div>
            `;

            contenedorEventos.appendChild(card);
        });

        ajustarAlturaTarjetas();
        agregarEventListenersCheckboxes();
        actualizarCarrito();
        mostrarCarritoSiHayEventosSeleccionados();
    }

    function ajustarAlturaTarjetas() {
        // Unificar altura de todas las tarjetas
        const cards = document.querySelectorAll(".card");
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
    }

    function agregarEventListenersCheckboxes() {
        document.querySelectorAll('input[name="eventos"]').forEach(checkbox => {
            checkbox.addEventListener("change", () => {
                actualizarCarrito();
                mostrarCarritoSiHayEventosSeleccionados();
            });
        });
    }

    function actualizarCarrito() {
        listaEventosSeleccionados.innerHTML = "";
        document.querySelectorAll('input[name="eventos"]:checked').forEach(cb => {
            const card = cb.closest(".card");
            const li = document.createElement("li");
            li.textContent = card.querySelector(".card-title").textContent;
            listaEventosSeleccionados.appendChild(li);
        });
    }

    function mostrarCarritoSiHayEventosSeleccionados() {
        const anyChecked = document.querySelectorAll('input[name="eventos"]:checked').length > 0;
        carritoEventos.style.display = anyChecked ? "block" : "none";
    }

    formInscripcion.addEventListener("submit", function(e) {
        e.preventDefault();
        const seleccionados = Array.from(
            document.querySelectorAll('input[name="eventos"]:checked')
        ).map(cb => cb.value);

        if (seleccionados.length === 0) {
            alert("Selecciona al menos un evento");
            return;
        }

        fetch("actions/inscribir.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ eventos: seleccionados })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert("Error: " + data.error);
            } else {
                alert("Inscripción exitosa");
                location.href = "index.php?view=mis_eventos";
            }
        })
        .catch(err => {
            console.error("Error al procesar la inscripción:", err);
            alert("Error al procesar la inscripción. Inténtalo de nuevo más tarde.");
        });
    });

    function cargarTiposEvento() {
        fetch("actions/tipos_evento.php")
            .then(response => response.json())
            .then(data => {
                filtroTipoEvento.innerHTML = '<option value="">Todos los tipos</option>';
                data.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo;
                    option.textContent = tipo;
                    filtroTipoEvento.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Error al cargar tipos de eventos:", error);
            });
    }

    btnLimpiar.addEventListener("click", () => {
        filtroCampus.value = "";
        filtroFecha.value = "";
        filtroTipoEvento.value = "";
        filtroPalabra.value = "";
        renderEventos();
    });

    filtroCampus.addEventListener("change", renderEventos);
    filtroFecha.addEventListener("change", renderEventos);
    filtroTipoEvento.addEventListener("change", renderEventos);
    filtroPalabra.addEventListener("input", renderEventos);

    // Inicialización
    cargarTiposEvento();
    cargarEventos();
});
