<?php
   error_reporting(E_ALL); ini_set('display_errors', 1);
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
        <p><h1 class="title-bar">DHAMMA <?php echo strtoupper($_GET["center"])?> - Modelo 182 - Generador de Archivo TXT</h1></p>
        <p>
          <a href="assets/docs/instrucciones.pdf" style="color:white; text-decoration:underline" target="_new">Instrucciones</a>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="assets/docs/historico.pdf" style="color:white; text-decoration:underline" target="_new">Histórico actualizaciones (2024)</a>
        </p>
        <br/>
        <?php if (!empty($message)) { ?>
          <p>
            <span id="message" style="
              color: white; 
              background-color:red;
              font-size: large; 
              padding: 10px; 
              border-radius: 5px; 
              display: inline-block;">
              <?php echo $message; ?>
            </span>
          </p>
          <br/>
        <?php } ?>
        <p>
          <form class="needs-validation" action="upload.php" method="post" enctype="multipart/form-data" novalidate>
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
              <label for="txt1" id="labeltxt1" class="button file-upload-button" style="background-color:#4e98b1">Selecciona TXT año anterior</label>
              <span id="file-name2" style="margin-top:10px; display:none"></span>              
              <br/>
            </p>
            <p>
              4) 
              <input type="file" id="txt2" name="txt2" accept=".txt" style="display:none"/>
              <label for="txt2" id="labeltxt2" class="button file-upload-button" style="background-color:#4e98b1">Selecciona TXT dos años antes</label>
              <span id="file-name3" style="margin-top:10px; display:none"></span>
              <br/>
            </p>
            <p>
              <span id="generar_txt_span" style="display:block">5) Generar TXT</span>
              <div id="generar_txt_div" style="color:white;display:none">5) <input type="submit" id="generate_txt" class="button" style="background-color:#2a8a40;" value="Generar TXT" name="submit"></div>
            </p>
            <input type="hidden" id="center" value="<?php echo $_GET["center"]?>">
          </form>
        </p>
      </div>
    </body>
    <script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
          document.getElementById('formularioDeclarante').style.display = 'block';
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
  </html>