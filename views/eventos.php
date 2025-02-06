<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=login");
    exit();
}
?>

<h2>Eventos Disponibles</h2>

<!-- Filtros para eventos -->
<label for="filtro-campus">Filtrar por Campus:</label>
<select id="filtro-campus">
    <option value="">Todos</option>
    <option value="Norte">Norte</option>
    <option value="Sur">Sur</option>
    <option value="Externo">Externo</option>
</select>

<label for="filtro-fecha">Filtrar por Fecha:</label>
<input type="date" id="filtro-fecha">

<!-- Contenedor de eventos -->
<div id="contenedor-eventos"></div>

<!-- Formulario de inscripción -->
<form id="form-inscripcion">
    <button type="submit">Inscribirse</button>
</form>

<script src="scripts/eventos.js"></script>
