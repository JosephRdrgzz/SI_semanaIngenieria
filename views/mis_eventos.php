<h2>Mis Eventos</h2>

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

    <label for="filtro-estado">Filtrar por Estado:</label>
    <select id="filtro-estado">
        <option value="todos">Todos</option>
        <option value="vigentes">Vigentes</option>
        <option value="pasados">Pasados</option>
    </select>

    <!-- Botón para limpiar filtros -->
    <button id="clear-filters" style="display: block; margin: 20px auto;">Limpiar Filtros</button>
</div>

<!-- Contenedor de eventos -->
<div id="contenedor-mis-eventos"></div>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

<script src="scripts/mis_eventos.js"></script>
