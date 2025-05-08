document.addEventListener("DOMContentLoaded", function () {
    const contenedorEventos = document.getElementById("contenedor-eventos");
    const filtroCampus = document.getElementById("filtro-campus");
    const filtroFecha = document.getElementById("filtro-fecha");
    const filtroTipo = document.getElementById("filtro-tipo");

    const toggleFiltersButton = document.getElementById("toggle-filters");
    const clearFiltersButton = document.getElementById("clear-filters");
    const filters = document.getElementById("filters");

    // Carrito y formulario
    const carritoEventos = document.getElementById("carrito-eventos");
    const listaEventosSeleccionados = document.getElementById("lista-eventos-seleccionados");
    const formInscripcion = document.getElementById("form-inscripcion");

    // Mostrar / ocultar filtros
    toggleFiltersButton.addEventListener("click", function () {
        if (filters.style.display === "none") {
            filters.style.display = "block";
            toggleFiltersButton.textContent = "Ocultar Filtros";
        } else {
            filters.style.display = "none";
            toggleFiltersButton.textContent = "Mostrar Filtros";
        }
    });

    // Botón para limpiar filtros
    clearFiltersButton.addEventListener("click", function () {
        filtroCampus.value = "";
        filtroFecha.value = "";
        filtroTipo.value = "";
        cargarEventos();
    });

    // Carga los tipos de evento (para el <select>)
    function cargarTiposEvento() {
        fetch("actions/tipos_evento.php")
            .then(response => response.json())
            .then(tipos => {
                console.log("Tipos de evento recibidos:", tipos);
                tipos.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo;
                    option.textContent = tipo;
                    filtroTipo.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Error al cargar tipos de evento:", error);
            });
    }

    // Carga y filtra eventos
    function cargarEventos() {
        fetch("actions/eventos.php")
            .then(r => r.json())
            .then(data => {
                contenedorEventos.innerHTML = "";
                // si viene debug/result...
                const eventos = data.result || data;

                // 1) Filtramos sólo los que pasan el filtro general
                const filtrados = eventos.filter(filtrarEvento);

                // 2) Separamos en dos arrays
                const conImagen    = filtrados.filter(e => e.has_image);
                const sinImagen    = filtrados.filter(e => !e.has_image);

                // Función auxiliar para crear y añadir la tarjeta
                function crearCard(evento) {
                    const card = document.createElement("div");
                    card.classList.add("card");

                    // Si tiene imagen, anulamos la altura fija y mantenemos el contexto para .save
                    const imgInlineStyle = evento.has_image
                        ? 'style="display:block; position:relative; height:auto;"'
                        : "";

                    card.innerHTML = `
    <div class="img" ${imgInlineStyle}>
      ${ evento.has_image
                        ? `<img
              src="admin/${evento.imagen_path}"
              alt="${evento.nombre}"
              style="
                display: block;
                width: 100%;
                height: auto;
                object-fit: cover;
                border-top-left-radius: 30px;
                border-top-right-radius: 30px;
              "
            >`
                        : `` }
      <div class="save">
        <input type="checkbox" name="eventos" value="${evento.id}">
      </div>
    </div>
    <div class="text">
      <h3 class="h3">${evento.nombre}</h3>
      <p class="p"><strong>Fecha:</strong> ${evento.fecha}</p>
      <p class="p"><strong>Horario:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
      <p class="p"><strong>Lugar:</strong> ${evento.lugar}</p>
      <p class="p"><strong>Campus:</strong> ${evento.campus}</p>
      <p class="p"><strong>Tipo:</strong> ${evento.tipo_evento}</p>
    </div>
    <a href="index.php?view=detalles_evento&id=${evento.id}" class="info-button">
      Más Información
    </a>
  `;

                    contenedorEventos.appendChild(card);
                }
                // 3) Primero tarjetas CON imagen
                conImagen.forEach(crearCard);

                // 4) Luego tarjetas SIN imagen
                sinImagen.forEach(crearCard);

                // 5) Reconfiguramos checkbox/carrito
                agregarEventListenersCheckboxes();
                actualizarCarrito();
                mostrarCarritoSiHayEventosSeleccionados();
            })
            .catch(e => console.error("Error cargando eventos:", e));
    }
    // Filtra un evento según campus, fecha y tipo
    function filtrarEvento(evento) {
        const campusSeleccionado = filtroCampus.value;
        const fechaSeleccionada = filtroFecha.value;
        const tipoSeleccionado = filtroTipo.value;

        return (
            (campusSeleccionado === "" || evento.campus === campusSeleccionado) &&
            (fechaSeleccionada === "" || evento.fecha === fechaSeleccionada) &&
            (tipoSeleccionado === "" || evento.tipo_evento === tipoSeleccionado)
        );
    }

    // Añade listeners a los checkboxes
    function agregarEventListenersCheckboxes() {
        const checkboxes = document.querySelectorAll('input[name="eventos"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", () => {
                actualizarCarrito();
                mostrarCarritoSiHayEventosSeleccionados();
            });
        });
    }

    // Actualiza la lista de eventos seleccionados en el carrito
    function actualizarCarrito() {
        listaEventosSeleccionados.innerHTML = "";
        const checkboxes = document.querySelectorAll('input[name="eventos"]:checked');
        checkboxes.forEach(checkbox => {
            const card = checkbox.closest(".card");
            const nombreEvento = card.querySelector(".h3").textContent;
            const li = document.createElement("li");
            li.textContent = nombreEvento;
            listaEventosSeleccionados.appendChild(li);
        });
    }

    // Muestra/oculta el carrito en función de si hay eventos seleccionados
    function mostrarCarritoSiHayEventosSeleccionados() {
        const checkboxes = document.querySelectorAll('input[name="eventos"]:checked');
        carritoEventos.style.display = (checkboxes.length > 0) ? "block" : "none";
    }

    // Manejo del submit de inscripción
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
                    // Actualiza la lista de eventos disponibles; el evento inscrito ya no se mostrará.
                    cargarEventos();
                }
            })
            .catch(error => {
                console.error("Error al procesar la inscripción:", error);
                alert("Error al procesar la inscripción. Inténtalo de nuevo más tarde.");
            });
    });


    // Listeners para recargar eventos cuando cambien los filtros
    filtroCampus.addEventListener("change", cargarEventos);
    filtroFecha.addEventListener("change", cargarEventos);
    filtroTipo.addEventListener("change", cargarEventos);

    // Cargar al inicio
    cargarTiposEvento();
    cargarEventos();
});

