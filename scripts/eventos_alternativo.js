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
                const eventos = data.result ? data.result : data;
                allEventos = eventos;
                renderEventos();
            })
            .catch(error => {
                console.error("Error al cargar eventos:", error);
            });
    }


    function renderEventos() {
        const now = new Date();
        // Calcular fecha y hora con 3 horas adicionales
        const futureThreeHours = new Date(now.getTime() + (3 * 60 * 60 * 1000));
        
        const campusVal = filtroCampus.value;
        const fechaVal = filtroFecha.value; // "YYYY-MM-DD"
        const tipoVal = filtroTipoEvento.value;
        const palabraVal = filtroPalabra ? filtroPalabra.value.toLowerCase() : "";

        // Filtra
        const eventosFiltrados = allEventos.filter(evento => {
            // Crear un objeto de fecha que combine fecha y hora del evento
            const eventDate = new Date(evento.fecha + 'T' + evento.hora_fin);
            // Por defecto, mostrar solo eventos que terminen al menos 3 horas después de ahora
            // A menos que se haya aplicado un filtro de fecha específico
            const validTime = fechaVal !== "" || eventDate > futureThreeHours;
            
            const matchCampus = (campusVal === "" || evento.campus.toLowerCase() === campusVal.toLowerCase());
            const matchFecha = (fechaVal === "" || evento.fecha === fechaVal);
            const matchTipo = (tipoVal === "" || evento.tipo_evento === tipoVal);
            // Filtrado por palabra en el nombre
            const matchPalabra = (palabraVal === "" || evento.nombre.toLowerCase().includes(palabraVal));
            
            return validTime && matchCampus && matchFecha && matchTipo && matchPalabra;
        });

        // Renderizar
        contenedorEventos.innerHTML = "";
        eventosFiltrados.forEach(evento => {
            const card = document.createElement("div");
            card.classList.add("card");
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
        // Si quieres que todas tengan la misma altura en cada fila
        // En este ejemplo, te puede bastar con no forzar la altura
        // y dejar que el contenido fluya. Pero si quieres mantenerlo:
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

    // Manejo del formulario de inscripción
    formInscripcion.addEventListener("submit", function(e) {
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
                    location.href = "index.php?view=mis_eventos";
                }
            })
            .catch(error => {
                console.error("Error al procesar la inscripción:", error);
                alert("Error al procesar la inscripción. Inténtalo de nuevo más tarde.");
            });
    });

    // Cargar tipos de eventos únicos
    function cargarTiposEvento() {
        fetch("actions/tipos_evento.php")
            .then(response => response.json())
            .then(data => {
                // data es un array de tipos únicos
                filtroTipoEvento.innerHTML = '<option value="">Todos los tipos</option>';
                data.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo;
                    option.textContent = tipo;
                    filtroTipoEvento.appendChild(option);
                });
                // Ya no se llama a M.FormSelect.init
            })
            .catch(error => {
                console.error("Error al cargar tipos de eventos:", error);
            });
    }

    // Limpiar filtros
    btnLimpiar.addEventListener("click", () => {
        filtroCampus.value = "";
        filtroFecha.value = "";
        filtroTipoEvento.value = "";
        filtroPalabra.value = "";
        renderEventos();
    });

    // Escuchamos cambios en los filtros
    filtroCampus.addEventListener("change", renderEventos);
    filtroFecha.addEventListener("change", renderEventos);
    filtroTipoEvento.addEventListener("change", renderEventos);
    filtroPalabra.addEventListener("input", renderEventos);


    // Inicial
    cargarTiposEvento();
    cargarEventos();
});
