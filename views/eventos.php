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
        <!-- Options will be populated by JavaScript -->
    </select>

    <!-- Botón para limpiar filtros -->
    <button id="clear-filters" style="display: block; margin: 20px auto;">Limpiar Filtros</button>
</div>

<!-- Contenedor de eventos -->
<div id="contenedor-eventos"></div>

<!-- Formulario de inscripción -->
<form id="form-inscripcion" style="position: fixed; bottom: 20px; right: 20px; display: none;">
    <button type="submit">
        <span class="text">Inscribirse</span>
        <span class="blob"></span>
        <span class="blob"></span>
        <span class="blob"></span>
        <span class="blob"></span>
    </button>
</form>

<script src="scripts/eventos.js"></script>