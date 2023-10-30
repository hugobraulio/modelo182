<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once 'M182generator.php';
require_once "classes/Resumen.php";

$resumen = new Resumen();

$donantes_2_años = [];
$donantes_1_años = [];
$donantes_recurrentes = [];

$csvData = _getFileData('csv');
if (!empty($csvData)) {
  $skipfirst = true;
  $txt1Data = _getFileData('txt1');
  $resumen->donantes1año = _createDonantesHash($txt1Data);
  $txt2Data = _getFileData('txt2');
  $resumen->donantes2años = _createDonantesHash($txt2Data);
  // Loop through CSV data to populate the TXT content
  $txt = generateModelo182($csvData, $resumen);  

  // save txt into a file
  $txt_filename = _generateFilename();
  file_put_contents('downloads/'.$txt_filename, $txt);

  $summary_html = generateSummaryHTML($resumen);
  $summary_txt = generateSummaryTXT($resumen);
  file_put_contents('downloads/summary.txt', $summary_txt);

} else {
  header("Location: index.php?message=Falló la subida del archivo. Selecciona primero el archivo si no lo has hecho.");
}

function _createDonantesHash($txtData){
  $donantesHash = [];
  for ($i = 1; $i < count($txtData); $i++) {
    //extract dni/nie/cif/nif from TXT of previous year
    $dni = strtoupper(substr($txtData[$i][0],18,9));
    $importe = substr($txtData[$i][0],84,11);
    $donantesHash[] = [$dni => $importe];
  }
  return $donantesHash;
}

function _getFileData($filename){
  $target_file = "uploads/" . basename($_FILES[$filename]["name"]);
  $data = [];
  if (move_uploaded_file($_FILES[$filename]["tmp_name"], $target_file)) {
    // Read CSV file into an array
    $file = fopen($target_file, 'r');
    while (($line = fgetcsv($file)) !== FALSE) {
      $data[] = $line;
    }
    fclose($file);
  }
  return $data;
}


function _generateFilename() {
  $now = new DateTime();
  $year = $now->format('Y');
  $month = $now->format('m');
  $day = $now->format('d');
  $hour = $now->format('H');
  $minutes = $now->format('i');
  $formattedDate = $year.'_'.$month.'_'.$day.'_'.$hour.$minutes;

  return 'modelo182_'.$formattedDate.'.txt';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modelo 182</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">  
</head>
<body>
  <div class="container">
    <p><h2 class="title-bar">Modelo 182 - Archivo TXT generado con éxito</h2></p>
    <p><pre style="color:white">Para descargarlo, clica en el botón de abajo.</pre></p>
    <p>
      <a href="downloads/<?php echo _generateFilename();?>" class="button" style="background-color:#2a8a40" download>Descargar TXT para Hacienda</a>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <a href="downloads/summary.txt" class="button" style="background-color:#4e98b1" download>Descargar resumen</a>
    </p>
    <p><pre style="color:white"><?php echo $summary_html; ?></pre></p>
  </div>
</body>
</html>