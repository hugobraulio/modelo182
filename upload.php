<?php
require_once 'M182generator.php';
require_once "classes/Resumen.php";

$resumen = new Resumen();

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["csv"]["name"]);

if (move_uploaded_file($_FILES["csv"]["tmp_name"], $target_file)) {
  // Read CSV file into an array
  $csvFile = fopen($target_file, 'r');
  $csvData = [];
  while (($line = fgetcsv($csvFile)) !== FALSE) {
    $csvData[] = $line;
  }
  fclose($csvFile);


  // Loop through CSV data to populate the TXT content
  $txt = generateModelo182($csvData, $resumen);  

  // save txt into a file
  $txt_filename = generateFilename();
  file_put_contents('downloads/'.$txt_filename, $txt);

  $summary = generateSummary($resumen);
  file_put_contents('downloads/summary.txt', $summary);

} else {
  header("Location: index.php?message=Falló la subida del archivo. Selecciona primero el archivo si no lo has hecho.");
}

function generateFilename() {
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
      <a href="downloads/<?php echo generateFilename();?>" class="button" style="background-color:#2a8a40" download>Descargar TXT para Hacienda</a>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <a href="downloads/summary.txt" class="button" style="background-color:#4e98b1" download>Descargar resumen</a>
    </p>
    <p><pre style="color:white"><?php echo $summary; ?></pre></p>
  </div>
</body>
</html>