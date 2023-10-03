<?php
session_start();
if (isset($_SESSION['variable'])) {
    $miVariable = $_SESSION['variable'];
$archivoPath = $miVariable; // Ruta al archivo que deseas descargar
$nombreArchivo = $archivoPath;
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . filesize($archivoPath));
// Enviar el contenido del archivo para la descarga
readfile($archivoPath);
// Limpiar la variable de sesión
 unset($_SESSION['variable']);
  $response['status'] = 'success';
    $response['message'] = 'Descarga exitosa';
 //echo '<script>window.location.href = "final.html";</script>';
    //exit;
} else {
    echo "No se encontró el nombre de archivo en la sesión.";
}
echo json_encode($response);
?>
