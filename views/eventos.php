<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=login");
    exit();
}
?>

<h2 style="text-align: center;">Eventos Disponibles</h2>

<!-- Botón para mostrar/ocultar filtros -->
<button id="toggle-filters" style="display: block; margin: 0 auto;">Mostrar Filtros</button>

<!-- Filtros para eventos -->
<div id="filters" style="display: none; text-align: center; margin-top: 20px;">
    <label for="filtro-campus">Filtrar por Campus:</label>
    <select id="filtro-campus">
        <option value="">Todos</option>
        <option value="Norte">Norte</option>
        <option value="Sur">Sur</option>
        <option value="Externo">Externo</option>
    </select>

    <label for="filtro-fecha">Filtrar por Fecha:</label>
    <input type="date" id="filtro-fecha">

    <label for="filtro-tipo">Filtrar por Tipo de Evento:</label>
    <select id="filtro-tipo">
        <option value="">Todos</option>
        <!-- Options se llenarán dinámicamente con JavaScript -->
    </select>

    <!-- Botón para limpiar filtros -->
    <button id="clear-filters" style="display: block; margin: 20px auto;">Limpiar Filtros</button>
</div>

<!-- Contenedor de eventos -->
<div id="contenedor-eventos"></div>

<!-- Carrito flotante de eventos seleccionados -->
<div id="carrito-eventos" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none; /* Por defecto oculto */
    max-width: 300px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    z-index: 9999;
">
    <h3 style="margin-top: 0;">Eventos seleccionados</h3>
    <ul id="lista-eventos-seleccionados" style="list-style: disc; padding-left: 20px; margin: 0;"></ul>

    <!-- Formulario de inscripción dentro del carrito -->
    <form id="form-inscripcion" style="margin-top: 10px;">
        <button type="submit" style="width: 100%; background-color: orange;">
            <span class="text">Inscribirse</span>
            <!-- Tus elementos .blob, etc., si los deseas -->
        </button>
    </form>
</div>

<!-- Carga tu archivo JavaScript al final -->
<script src="scripts/eventos.js"></script>