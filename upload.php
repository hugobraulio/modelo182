<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once 'M182generator.php';
require_once "classes/Resumen.php";

_saveConfig();

$resumen = new Resumen();

$donantes_2_años = [];
$donantes_1_años = [];
$donantes_recurrentes = [];
$center = $_POST["center"];
$ejercicio = (int)$_POST["ejercicio"];

$csvData = _getFileData('csv');
if (!empty($csvData)) {
  $skipfirst = true;
  $txt1Data = _getFileData('txt1');

  if (empty($txt1Data)) {
    header("Location: init.php?center=".$center."&ejercicio=".$ejercicio."&message=Ha fallado la subida del TXT del año anterior.");
    return;
  } 
  $año_txt1 = substr($txt1Data[0][0],4,4);
  if ((int)$ejercicio != (int)$año_txt1+1 ) { // si el TXT no es del año anterior
    header("Location: init.php?center=".$center."&ejercicio=".$ejercicio."&message=El TXT del año anterior (".($ejercicio-1).") es de otro año (".$año_txt1."). Por favor añade el correcto.");
    return;
  } 
  $resumen->donantes1año = _createDonantesHash($txt1Data);
  $txt2Data = _getFileData('txt2');
  if (empty($txt2Data)) {
    header("Location: init.php?center=".$center."&ejercicio=".$ejercicio."&message=Ha fallado la subida del TXT del año anterior.");
    return;
  } 
  $año_txt2 = substr($txt2Data[0][0],4,4);
  if ((int)$ejercicio != (int)$año_txt2+2 ) { // si el TXT no es dos años anteriores
    header("Location: init.php?center=".$center."&ejercicio=".$ejercicio."&message=El TXT de dos años antes (".($ejercicio-2).") es de otro año (".$año_txt2."). Por favor añade el correcto.");
    return;
  } 
  $resumen->donantes2años = _createDonantesHash($txt2Data);
  // Loop through CSV data to populate the TXT content
  $txt = generateModelo182($csvData, $resumen);  

  // save txt into a file
  $date_str = _generateDateString();

  // Check if the content is already UTF-8
  if (!mb_check_encoding($txt, 'UTF-8')) {
    // Convert the content to UTF-8
    iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $txt);
    //$txt = mb_convert_encoding($txt, 'UTF-8', mb_detect_encoding($txt, 'UTF-8, ISO-8859-1, ISO-8859-15', true));
  }
  file_put_contents('files/m182.txt', $txt);

  $summary_html = generateSummaryHTML($resumen);
  $summary_csv = generateSummaryCSV($resumen);
  file_put_contents('files/casos.csv', $summary_csv);

} else {
  header("Location: init.php?center=".$center."&ejercicio=".$ejercicio."&message=Falló la subida del archivo. Por favor, vuelve a intentarlo.");
}

function _saveConfig(){
  $config = require('config_'.$_POST["center"].'.php');
  $config['nif'] = strtoupper($_POST["nif"]);
  $config['denominacion'] = strtoupper($_POST["denominacion"]);
  $config['telefono'] = $_POST["telefono"];
  $config['persona'] = strtoupper($_POST["persona"]);
  $newConfig = '<?php return ' . var_export($config, true) . '; ?>';
  file_put_contents('config_'.$_POST["center"].'.php',$newConfig);
}

function _createDonantesHash($txtData){
  $donantesHash = [];
  for ($i = 1; $i < count($txtData); $i++) {
    //extract dni/nie/cif/nif from TXT of previous year
    $dni = strtoupper(substr($txtData[$i][0],17,9));
    $importe = substr($txtData[$i][0],83,11);
    $donantesHash[$dni] = $importe;
  }
  return $donantesHash;
}

function _getFileData($filename){
  $target_file = "files/" . basename($_FILES[$filename]["name"]);
  $data = [];
  if (move_uploaded_file($_FILES[$filename]["tmp_name"], $target_file)) {
    // Read CSV file into an array
    $file = fopen($target_file, 'r');
    while (($line = fgetcsv($file)) !== FALSE) {
      $data[] = $line;
    }
    fclose($file);
    unlink($target_file);
  }
  return $data;
}


function _generateDateString() {
  $now = new DateTime();
  $year = $now->format('Y');
  $month = $now->format('m');
  $day = $now->format('d');
  $hour = $now->format('H');
  $minutes = $now->format('i');
  return $year.'_'.$month.'_'.$day.'_'.$hour.$minutes;
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
  <script>
    // Function to handle the automatic download and deletion
    function downloadAndDelete(fileurl, filename) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', fileurl, true);
        xhr.responseType = 'blob';
        xhr.onload = function () {
            // Check if the request was successful
            if (this.status === 200) {
                // Create a new Blob object and set its content to the response
                var blob = new Blob([this.response], {type: 'application/octet-stream'});
                // Create a link element, set its href to the blob, and trigger the download
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                // Use a small delay before deleting the file
                setTimeout(function(){
                    // Request the deletion of the file
                    var deleteRequest = new XMLHttpRequest();
                    deleteRequest.open('GET', 'delete_file.php?file=' + fileurl.replace(/files\//g,''), true);
                    deleteRequest.send();
                }, 1000); // Delay in milliseconds
            }
        };
        xhr.send();
    }

    // Call the function with the URL to the file and the desired filename
    downloadAndDelete('files/m182.txt', '<?php echo "modelo182_".$ejercicio."_creado_"._generateDateString().".txt";?>');
    downloadAndDelete('files/casos.csv', '<?php echo "resumen_casos_"._generateDateString().".csv";?>');
  </script>
  
  <div class="container">
    <p><h2 class="title-bar">Modelo 182 - Archivo TXT generado con éxito</h2></p>
    <p><pre style="color:white">El fichero .TXT se ha descargado automáticamente.</pre></p>
    <p><pre style="color:white"><?php echo $summary_html; ?></pre></p>
  </div>
  </form>
</body>
<script>
  // Seleccionar todos los botones
  const toggleButtons = document.querySelectorAll(".toggle-button");

  toggleButtons.forEach(button => {
      button.addEventListener("click", () => {
          const targetId = button.getAttribute("data-target");
          const targetContent = document.getElementById(targetId);

          targetContent.classList.toggle("show");

          // Alternar la flecha y el texto del botón
          const arrow = button.querySelector(".arrow");
          const buttonText = button.querySelector("span");
          if (targetContent.classList.contains("show")) {
              arrow.textContent = "▲";
              buttonText.textContent = "Ocultar listado";
          } else {
              arrow.textContent = "▼";
              buttonText.textContent = "Mostrar listado";
          }
      });
  });
</script>
</html>