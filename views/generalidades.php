<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=login");
    exit();
}
?>

<style>
  .contenedor-home {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  .contenedor-home img {
    max-width: 100%;
    height: auto;
  }
  @media (max-width: 768px) {
    .contenedor-home {
      max-width: 100% !important;
      padding: 10px;
    }
  }
</style><div class="contenedor-home"><p><br></p><h4><strong style="color: rgb(34, 34, 34);">¿Qué es la semana de Ingeniería?</strong></h4><p><span style="background-color: rgb(255, 255, 255); color: rgb(105, 105, 105);">La </span><strong style="color: rgb(105, 105, 105);">Semana de Ingeniería</strong><span style="background-color: rgb(255, 255, 255); color: rgb(105, 105, 105);"> es un evento en el que los jóvenes estudiantes de ingeniería tienen un espacio para presentar proyectos supervisados por un profesor, con el propósito de difundir aplicaciones importantes que contribuyan al desarrollo científico y tecnológico de la sociedad mexicana.</span></p><p><br></p><p><span style="background-color: rgb(255, 255, 255); color: rgb(105, 105, 105);">De igual forma, a lo largo de esta semana se invita a una serie de expertos a dialogar con los alumnos sobre temas de interés. Aprovecha la oportunidad para participar y promover el intercambio de ideas entre las distintas áreas de la ingeniería.</span></p><p><br></p><p>Conferencias</p><p>Talleres</p><p>Visitas</p><p>Concursos</p><p>	Exposición de Proyectos <a href="https://www.anahuac.mx/mexico/EscuelasyFacultades/ingenieria/sites/default/files/Lineamientos%20para%20los%20expositores%20de%20proyectos%20de%20la%20Semana%20de%20Ingenier%C3%ADa%202025.pdf" rel="noopener noreferrer" target="_blank">Lineamientos</a></p><p>Concurso Catapultas <a href="https://www.anahuac.mx/mexico/EscuelasyFacultades/ingenieria/sites/default/files/Reglamento%20Concurso%20Catapultas%201.pdf" rel="noopener noreferrer" target="_blank">BASES</a></p><p>Mini olimpiadas  <a href="https://docs.google.com/forms/d/e/1FAIpQLSdLowqL7eXbBKhuyngQ8DwGDxzX04ljzIaXwEjvHyczBLDovg/viewform" rel="noopener noreferrer" target="_blank">Registro</a></p><p><br></p><p><br></p></div>