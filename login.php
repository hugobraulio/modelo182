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
      <title>Modelo 182 - Inicio de Sesión</title>
      <link rel="stylesheet" href="assets/css/style.css">
      <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
      <div class="container">
        <p><h1 class="title-bar">Modelo 182 - Inicio de Sesión</h1></p>
        <br/>
        <p>
          <form action="loginProcess.php" method="post" enctype="multipart/form-data">
            <div class="form-group row">
              <label for="center" class="col-sm-6 col-form-label text-right text-white">
                <b>Centro:</b>
              </label>
              <div class="col-sm-4">
              <select id="center" name="center" style="padding:4px" required>
                  <option value="nocenter">Selecciona Centro</option>  
                  <option value="neru">Dhamma Neru</option>
                  <option value="sacca">Dhamma Sacca</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="user" class="col-sm-6 col-form-label text-right text-white">
                <b>Usuario:</b>
              </label>
              <div class="col-sm-4">
                <input type="text" id="user" name="user" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="password" class="col-sm-6 col-form-label text-right text-white">
                <b>Contraseña:</b>
              </label>
              <div class="col-sm-4">
                <input type="password" id="password" name="password" required>
              </div>
              <br>
            </div>
            <div class="form-group row">
              <div class="col-sm-6 offset-sm-6">
                <input type="submit" id="login" class="button" style="background-color:#2a8a40" value="Iniciar Sesión" name="submit">
              </div>
            </div>
            <p><span id="message_login" style="font-size:large"><?php echo $message; ?></span></p>
          </form>
        </p>
      </div>
    </body>
  </html>