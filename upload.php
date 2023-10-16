<?php
require_once 'M182generator.php';

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
  
  // Start output buffering
  ob_start();

  // Loop through CSV data to populate the TXT content
  $txtContent = generateModelo182($csvData);  

  // Output the TXT content
  echo $txtContent;

} else {

  header("Location: index.php?message=Falló la subida del archivo. Selecciona primero el archivo si no lo has hecho.");

}
?>