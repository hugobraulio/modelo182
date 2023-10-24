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
        <!--p id="instructionsParagraph" style="display: none;" class="center-container">
          Este formulario recoge el fichero CSV que contiene los datos de los donantes de este ejercicio, así como, opcionalmente, los ficheros 
          ya presentados a Hacienda en los dos ultimos años y los procesa para generar 
          el <b>fichero <i>modelo 182</i></b> de este <i>ejercicio</i>, siguiendo las pautas establecidas
          por la Agencia Tributaria 
          <br/><br/>
          Para facilitarle la labor, conforme agregue los ficheros de los años anteriores, algunos campos del formulario se irán rellenando automáticamente con
          datos extraídos de los propios ficheros. En cualquier caso, siempre debe revisar que todos los campos contienen los valores apropiados y actuales antes de enviar 
          el formulario.
          <br/><br/>
          Después del procesado, la aplicación elimina todos los ficheros y datos enviados y descarga automáticamente el <b>fichero resultado</b>, ya listo para presentar a Hacienda.
          <br/><br/>
          Puedes usar las 
          <a href="https://www.agenciatributaria.es/AEAT.internet/Inicio/_otros_/Descarga_de_programas_de_ayuda/Prevalidacion_Cobol/Ejercicio_2014/Programas_de_prevalidacion_Cobol_Windows/Programas_de_prevalidacion_Cobol_Windows.shtml">
          herramientas de pre-validación</a> ofrecidas por la AEAT para verificar la validez del <b>fichero resultado</b> antes de presentarlo a Hacienda.
          <br/><br/>
          <div id="subscripters">
            <p>
              <sup>1</sup>: Columnas del CSV deben estar en este orden (no importa el nombre de los campos, o incluso si no tienen nombre):
            </p>
            <p>	
              <center><img id='imgCSV' src='../static/img/csv_tabla.png?32352' width="80%"/></p></center>
            </p>
            <p><sup>2</sup>: Puedes usar este fichero CSV de donaciones de ejemplo
              <a href="{{ url_for('public.sample') }}">(descargar aqui)</a>.<br/> Fíjate en las 2 últimas lineas y verás que tienen DNIs mal-formados. 
              <br/>También verás que el nombre de las columnas es distinto al propuesto. Aún así, la aplicación procesa el fichero correctamente.
            </p>			
            <p><sup>3</sup>: Especificaciones oficiales del Formato del <i>modelo 182</i> 
              <a href="https://www6.aeat.es/static_files/common/internet/dep/aplicaciones/modelos/2017/Modelo182.pdf">aqui</a> 
            </p>
          </div>
        </p-->
        <p>
          <form id="uploadForm" enctype="multipart/form-data">
            <p>
              1) <span class="button" id="rellenarForm" style="background-color:#2a8a40">Rellena datos del declarante</span>
              <?php include("formDeclarante.php");?>
            </p>
            <p>
              2) <input type="file" id="csv" name="csv" accept=".csv" style="display:none"/>
              <label for="csv" class="button file-upload-button">Selecciona archivo CSV</label>
              <span id="file-name"></span>
              <br/>
            </p>
            <p>
              3) 
              <label for="csv" class="button file-upload-button" disabled style="background-color:#4e98b1">Selecciona TXT año anterior</label>
              <span id="file-name" disabled></span>
              <br/>
            </p>
            <p>
              4) 
              <label for="csv" class="button file-upload-button" disabled style="background-color:#4e98b1">Selecciona TXT hace 2 años</label>
              <span id="file-name" disable></span>
              <br/>
            </p>
            <p>
              5) <input type="submit" id="generate_txt" class="button" style="background-color:#2a8a40" value="Generar TXT y descargarlo" name="submit" disabled>
            </p>
            <p><span id="message"><?php echo $message; ?></span></p>
          </form>
        </p>
      </div>
    </body>
  </html>