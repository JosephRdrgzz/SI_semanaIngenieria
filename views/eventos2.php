<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Eventos Alternativos</title>

    <!-- Fuente de iconos (opcional) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <style>
        /* Estilos básicos */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1rem;
        }

        h2 {
            text-align: center;
        }
        p {
            text-align: center;
        }

        /* Contenedor de filtros */
        #filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            justify-content: center; /* Centrar en la fila */
        }
        #filtros > div {
            display: flex;
            flex-direction: column;
        }
        #filtros label {
            margin-bottom: 0.2rem;
            font-weight: 600;
        }
        #filtros select,
        #filtros input[type="date"] {
            padding: 0.4rem;
            font-size: 1rem;
        }
        #btn-limpiar-filtros {
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 1rem;
            align-self: flex-end; /* Ubicar el botón abajo */
            margin-top: auto;
        }

        /* Contenedor de tarjetas */
        #contenedor-eventos-alternativo {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center; /* Centrar tarjetas */
        }

        /* Tarjetas */
        .card {
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 300px;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .card-header {
            background-color: #f5f5f5;
            padding: 0.5rem;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title {
            font-weight: bold;
            font-size: 1rem;
        }
        .card-content {
            padding: 0.5rem;
            flex-grow: 1;
        }
        .card-detail {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .card-detail i {
            margin-right: 8px;
            color: #26a69a;
        }
        .card-action {
            border-top: 1px solid #ccc;
            padding: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-action a {
            color: #00796b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Badges de campus */
        .evento-badge {
            padding: 3px 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .campus-norte {
            background-color: #4CAF50;
        }
        .campus-sur {
            background-color: #2196F3;
        }
        .campus-externo {
            background-color: #FF9800;
        }

        /* Carrito de eventos */
        #carrito-eventos {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: 300px;
            transition: right 0.3s ease;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px 0 0 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            z-index: 9999;
        }

        /* Estado cerrado: se desplaza a la derecha dejando 40px visibles */
        #carrito-eventos.cart-closed {
            right: -260px; /* 300px - 40px = 260px de desplazamiento */
        }

        /* Botón toggle (>) */
        .cart-toggle {
            position: absolute;
            left: -40px; /* se coloca justo al borde, fuera del contenedor */
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background-color: rgba(255, 103, 36, 0.97); /* Color similar al de info-button */
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cart-toggle:hover {
            background-color: rgba(255, 103, 36, 0.97);
        }

        /* Cambiar color del botón cuando el carrito está cerrado */
        #carrito-eventos.cart-closed .cart-toggle {
            background-color: #4CAF50;
        }

        /* Estilos para el contenido del carrito */
        .cart-content {
            padding: 15px;
        }

        /* Leyenda */
        .cart-legend {
            font-size: 0.8rem;
            font-style: italic;
            text-align: center;
            margin-top: 10px;
        }

        /* (Opcional) Si quieres ajustar el botón del formulario */
        #form-inscripcion button {
            width: 100%;
            background-color: orange;
            padding: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Eventos Disponibles</h2>
    <p>Para inscribirte a un evento, selecciona los eventos que te interesen y haz clic en "Inscribirse".</p>

    <!-- Filtros -->
    <div id="filtros">
        <div>
            <label for="filtro-campus">Campus</label>
            <select id="filtro-campus">
                <option value="" selected>Todos los campus</option>
                <option value="Norte">Norte</option>
                <option value="Sur">Sur</option>
                <option value="Externo">Externo</option>
            </select>
        </div>
        <div>
            <label for="filtro-fecha">Fecha</label>
            <input type="date" id="filtro-fecha" />
        </div>
        <div>
            <label for="filtro-tipo-evento">Tipo de Evento</label>
            <select id="filtro-tipo-evento">
                <option value="" selected>Todos los tipos</option>
                <!-- Se cargarán dinámicamente desde el backend -->
            </select>
        </div>
        <div>
            <label for="filtro-palabra">Buscar por palabra</label>
            <input type="text" id="filtro-palabra" placeholder="Nombre del evento" />
        </div>

        <div>
            <button id="btn-limpiar-filtros" type="button">LIMPIAR FILTROS</button>
        </div>
    </div>

    <!-- Contenedor para los eventos -->
    <div id="contenedor-eventos-alternativo"></div>
</div>

<!-- Carrito flotante de eventos seleccionados -->
<div id="carrito-eventos">
    <!-- Botón/tache para abrir/cerrar el carrito -->
    <div class="cart-toggle">
        <span id="toggle-cart">></span>
    </div>
    <!-- Contenido del carrito -->
    <div class="cart-content">
        <h3>Eventos seleccionados</h3>
        <ul id="lista-eventos-seleccionados"></ul>
        <!-- Formulario de inscripción dentro del carrito -->
        <form id="form-inscripcion">
            <button type="submit">Inscribirse</button>
        </form>
        <!-- Leyenda -->
        <div class="cart-legend">Haz clic en el círculo para minimizar o maximizar el carrito.</div>
    </div>
</div>

<!-- Tu JavaScript propio -->
<script src="scripts/eventos_alternativo.js"></script>
</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const carrito = document.getElementById("carrito-eventos");
        const toggleCart = document.getElementById("toggle-cart");

        // Mostrar el carrito inicialmente
        carrito.style.display = "block";

        // Al hacer clic en el contenedor del toggle (el tache) se alterna la visibilidad
        document.querySelector(".cart-toggle").addEventListener("click", function() {
            carrito.classList.toggle("cart-closed");
            toggleCart.textContent = carrito.classList.contains("cart-closed") ? "<" : ">";
        });
    });
</script>

