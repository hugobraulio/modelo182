<?php
   $message = '';
   if (isset($_GET['message'])) {
       $message = $_GET['message'];
   }
?>
<!DOCTYPE html>
  <html lang="es">
    <head>
      <meta charset="UTF-8">
      <title>Modelo 182 - Creación de Archivo</title>
      <link rel="stylesheet" href="assets/css/style.css">
      <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
      <script src="assets/js/scripts.js" defer></script>
    </head>
    <body>
      <div class="container">
        <p><h1 class="title-bar">Modelo 182 - Generador de Archivo TXT</h1></p>
        <p><a href="assets/docs/instrucciones.pdf" style="color:white; text-decoration:underline" target="_new">Instrucciones aquí</a></p>
        <br/>
        <p>
          <form action="upload.php" method="post" enctype="multipart/form-data">
            <p>
              1) <span class="button" id="rellenarForm">Rellena datos del declarante</span>
              <?php include("formDeclarante.php");?>
            </p>
            <p>
              2) 
              <input type="file" id="csv" name="csv" accept=".csv" style="display:none"/>
              <label for="csv" class="button file-upload-button">Selecciona archivo CSV</label>
              <span id="file-name1" style="margin-top:10px; display:none"></span>
              <br/>
            </p>
            <p>
              3) 
              <input type="file" id="txt1" name="txt1" accept=".txt" style="display:none"/>
              <label for="txt1" class="button file-upload-button" style="background-color:#4e98b1">Selecciona TXT año anterior</label>
              <span id="file-name2" style="margin-top:10px; display:none"></span>              
              <br/>
            </p>
            <p>
              4) 
              <input type="file" id="txt2" name="txt2" accept=".txt" style="display:none"/>
              <label for="txt2" class="button file-upload-button" style="background-color:#4e98b1">Selecciona TXT hace 2 años</label>
              <span id="file-name3" style="margin-top:10px; display:none"></span>
              <br/>
            </p>
            <p>
              5) <input type="submit" id="generate_txt" class="button" style="background-color:#2a8a40" value="Generar TXT" name="submit" disabled>
            </p>
            <p><span id="message"><?php echo $message; ?></span></p>
          </form>
        </p>
      </div>
    </body>
  </html>