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
      <script src="assets/js/scripts.js" defer></script>
    </head>
    <body>
      <div class="container">
        <p><h1 class="title-bar">Modelo 182 - Generador de Archivo TXT</h1></p>
        <p><a href="#" id="instructionsLink" style="color:azure">Instrucciones aquí</a></p>
        <br/>
        <p id="instructionsParagraph" style="display: none;" class="center-container">
          Este formulario recoge el fichero CSV<sup>1</sup> que contiene los datos de los donantes de este año (o este <i>ejercicio</i>), así como, opcionalmente, los ficheros 
          ya presentados a Hacienda en los dos ultimos años y los procesa para generar 
          el <b>fichero <i>modelo 182</i></b> de este <i>ejercicio</i>, siguiendo las pautas establecidas
          por la Agencia Tributaria<sup>2</sup> 
          <br/><br/>
          Para facilitarle la labor, conforme agregue los ficheros de los años anteriores, algunos campos del formulario se irán rellenando automáticamente con
          datos extraídos de los propios ficheros. En cualquier caso, siempre debe revisar que todos los campos contienen los valores apropiados y actuales antes de enviar 
          el formulario.
          <br/><br/>
          Después del procesado, la aplicación elimina todos los ficheros y datos enviados y descarga automáticamente el <b>fichero resultado</b>, ya listo para presentar a Hacienda.
          <br/><br/>
          Adicionalmente, si así lo desea, puede usar las 
          <a href="https://www.agenciatributaria.es/AEAT.internet/Inicio/_otros_/Descarga_de_programas_de_ayuda/Prevalidacion_Cobol/Ejercicio_2014/Programas_de_prevalidacion_Cobol_Windows/Programas_de_prevalidacion_Cobol_Windows.shtml">herramientas de pre-validación</a>
          ofrecidas por la AEAT para verificar la validez del <b>fichero resultado</b> antes de presentarlo a Hacienda.
          <br/><br/>
        </p>
        <p>
          <form id="uploadForm" enctype="multipart/form-data">
            <p>1) <input type="file" id="csv" name="csv" accept=".csv" style="display:none"/>
                <label for="csv" class="button file-upload-button">Seleccionar archivo</label></p>
            <br/><span id="file-name"></span>
            </p>
            <p>2) <input type="submit" id="generate_txt" class="button" value="Generar TXT y descargarlo" name="submit" disabled></p>
            <p><span id="message"><?php echo $message; ?></span></p>
          </form>
        </p>
      </div>
    </body>
  </html>