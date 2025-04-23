<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Sólo admins
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso no autorizado');
}

// ID válido
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die('ID inválido');
}

// 1) Traer evento + JSONB de asistencia
$sql = "
    SELECT nombre,
           capacidad,
           asistencia::text AS asistencia_json
    FROM evento
    WHERE id = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$evt = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$evt) {
    die('Evento no encontrado');
}
$asisArr = json_decode($evt['asistencia_json'], true);

// 2) Totales
$totalReg   = count($asisArr);
$totalAsist = 0;
foreach ($asisArr as $hs) {
    if (is_array($hs) && count($hs) > 0) {
        $totalAsist++;
    }
}
$cap  = max(1, (int)$evt['capacidad']);
$porc = round(($totalAsist / $cap) * 100);

// 3) Traer nombre e ingeniería
$alumnosInfo = [];
if ($totalReg > 0) {
    $exps         = array_keys($asisArr);
    $ph          = implode(',', array_fill(0, count($exps), '?'));
    $sqlAl       = "
        SELECT a.exp,
               a.nombre,
               p.descripcion AS programa
        FROM alumnos a
        LEFT JOIN catalogo_programas p
          ON a.idprograma = p.clave
        WHERE a.exp IN ($ph)
    ";
    $stmtAl      = $pdo->prepare($sqlAl);
    $stmtAl->execute($exps);
    while ($r = $stmtAl->fetch(PDO::FETCH_ASSOC)) {
        $alumnosInfo[$r['exp']] = $r;
    }
}

// 4) Contar por programa
$countsRegistro = [];
$countsAsist    = [];
foreach ($asisArr as $exp => $hs) {
    $prog = $alumnosInfo[$exp]['programa'] ?? 'Desconocido';
    $countsRegistro[$prog] = ($countsRegistro[$prog] ?? 0) + 1;
    if (is_array($hs) && count($hs) > 0) {
        $countsAsist[$prog] = ($countsAsist[$prog] ?? 0) + 1;
    }
}

// 5) Función de pie chart que devuelve colores usados
function createPieChart(array $counts, int $size, string $path): array {
    $img = imagecreatetruecolor($size, $size);
    $bg  = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, $size, $size, $bg);
    $total = array_sum($counts);
    $start = -90;
    $sliceColors = [];
    foreach ($counts as $label => $val) {
        $angle = ($val / $total) * 360;
        $r = rand(50,200); $g = rand(50,200); $b = rand(50,200);
        $sliceColors[$label] = [$r,$g,$b];
        $col = imagecolorallocate($img, $r, $g, $b);
        imagefilledarc($img, $size/2, $size/2, $size, $size, $start, $start + $angle, $col, IMG_ARC_PIE);
        $start += $angle;
    }
    imagepng($img, $path);
    imagedestroy($img);
    return $sliceColors;
}

// — generar los tres pasteles
$tmpMain  = sys_get_temp_dir()."/pie_main_{$id}.png";
$tmpReg   = sys_get_temp_dir()."/pie_reg_{$id}.png";
$tmpAsist = sys_get_temp_dir()."/pie_asist_{$id}.png";

$colorsMain  = createPieChart([ 'Asistieron' => $totalAsist, 'No asistieron' => ($totalReg - $totalAsist) ], 200, $tmpMain);
$colorsReg   = createPieChart($countsRegistro,  200, $tmpReg);
$colorsAsist = createPieChart($countsAsist,     200, $tmpAsist);

// 6) Crear PDF
$pdf = new \TCPDF();
$pdf->SetCreator('Sistema');
$pdf->SetAuthor('Admin');
$pdf->SetTitle("Asistencia {$evt['nombre']}");
$pdf->AddPage();

// --- Resumen ---
$pdf->SetFont('helvetica','B',16);
$pdf->Cell(0,10,'Reporte de Asistencia',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('helvetica','',12);
$pdf->Cell(0,6,"Evento      : {$evt['nombre']}",0,1);
$pdf->Cell(0,6,"Capacidad   : {$evt['capacidad']}",0,1);
$pdf->Cell(0,6,"Registrados : {$totalReg}",0,1);
$pdf->Cell(0,6,"Asistieron  : {$totalAsist} ({$porc}%)",0,1);
$pdf->Ln(6);

// --- Gráfico global pequeño ---
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,6,'Porcentaje de asistencia',0,1);
$pdf->Image($tmpMain, 15, $pdf->GetY(), 80, 0, 'PNG');
$pdf->Ln(40);

// --- Leyenda global ---
$pdf->SetFont('helvetica','',10);
foreach ($colorsMain as $label => $rgb) {
    list($r,$g,$b) = $rgb;
    $pdf->SetFillColor($r,$g,$b);
    // cuadro de color
    $pdf->Cell(5,5,'',0,0,'',true);
    $pdf->Cell(0,5," $label",0,1);
}
$pdf->Ln(6);

// --- Tabla detalle con Nombre e Ingeniería ---
$pdf->SetFont('helvetica','B',11);
$pdf->Cell(30,8,'Expediente',1);
$pdf->Cell(60,8,'Nombre',1);
$pdf->Cell(60,8,'Ingeniería',1);
$pdf->Cell(40,8,'Check-in/out',1,1);
$pdf->SetFont('helvetica','',10);
foreach ($asisArr as $exp => $hs) {
    $txt      = implode(' | ', $hs);
    $nombre   = $alumnosInfo[$exp]['nombre']   ?? '';
    $programa = $alumnosInfo[$exp]['programa'] ?? '';
    $pdf->Cell(30,6, $exp,      1);
    $pdf->Cell(60,6, $nombre,   1);
    $pdf->Cell(60,6, $programa, 1);
    $pdf->Cell(40,6, $txt,      1,1);
}
$pdf->Ln(6);

// --- Pie chart registros por ingeniería ---
$pdf->AddPage();
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,6,'Registros por Ingeniería',0,1);
$pdf->Image($tmpReg, 15, $pdf->GetY(), 80, 0, 'PNG');
$pdf->Ln(40);
$pdf->SetFont('helvetica','',10);
foreach ($colorsReg as $label => $rgb) {
    list($r,$g,$b) = $rgb;
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Cell(5,5,'',0,0,'',true);
    $pdf->Cell(0,5," $label ({$countsRegistro[$label]})",0,1);
}
$pdf->Ln(6);

// --- Pie chart asistencias por ingeniería ---
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,6,'Asistencias por Ingeniería',0,1);
$pdf->Image($tmpAsist, 15, $pdf->GetY(), 80, 0, 'PNG');
$pdf->Ln(40);
$pdf->SetFont('helvetica','',10);
foreach ($colorsAsist as $label => $rgb) {
    list($r,$g,$b) = $rgb;
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Cell(5,5,'',0,0,'',true);
    $pdf->Cell(0,5," $label ({$countsAsist[$label]})",0,1);
}

// 7) Limpiar y output
@unlink($tmpMain);
@unlink($tmpReg);
@unlink($tmpAsist);

$pdf->Output("asistencia_evento_{$id}.pdf", 'D');
exit;

