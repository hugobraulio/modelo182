<?php 
error_reporting(E_ALL); ini_set('display_errors', 1);
$config = require('config_'.$_GET["center"]).'.php'; ?>
<div id="formularioDeclarante" style="display: none; width:100%" class="center-container">
  <div class="form-group row">
    <label for="ejercicio" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Ejercicio</b> actual:</label>
    <div class="col-sm-4">
      <input type="year" class="form-control" id="ejercicio" name="ejercicio" pattern="[0-9]{4}" min="2015" max="2099" maxlength="4" placeholder="ej:<?php echo Date("Y")?>" required>
      <div class="invalid-tooltip">Selecciona un año válido</div>
    </div>
  </div>  

  <div class="form-group row">
    <label for="nif" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>NIF</b> del declarante:</label>
    <div class="col-sm-4">
      <input value="<?= $config['nif']?>" type="text" class="form-control" id="nif" name="nif" pattern="[A-Z]{1}[0-9]{8}" placeholder="ej:12345678Z" maxlength="9" required>
      <div class="invalid-tooltip">Selecciona un NIF/NIE/DNI válido</div>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="denominacion" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Denominación</b> del declarante:</label>
    <div class="col-sm-4">
      <input value="<?= $config['denominacion']?>" type="text" class="form-control" id="denominacion" name="denominacion" placeholder="ej:Fundación XYZ" maxlength="40" required>
      <div class="invalid-tooltip">Selecciona una denominación válida</div>
    </div>
  </div>

  <div class="form-group row">
    <label for="telefono" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Teléfono</b> de contacto:</label>
    <div class="col-sm-4">
      <input value="<?= $config['telefono']?>" type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" maxlength="9" placeholder="ej:654321987" required>
      <div class="invalid-tooltip">Selecciona un teléfono válido</div>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="persona" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Persona</b> de Contacto:</label>
    <div class="col-sm-4">
      <input value="<?= $config['persona']?>" type="text" class="form-control" id="persona" name="persona" maxlength="40" placeholder="ej:Antonio Pérez" required>
      <div class="invalid-tooltip">Selecciona una persona válida</div>
    </div>
  </div>

  <div class="form-group row">
    <label for="justificante" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Número</b> justificante declaración:</label>
    <div class="col-sm-4">
      <input value="1820000000001" type="number" class="form-control" id="justificante" name="justificante" maxlength="13" pattern="[0-9]{13}" placeholder="ej:1820000000001" required>
      <div class="invalid-tooltip">Selecciona un número de justificante válido</div>
    </div>
  </div>

  <div class="form-group row">
    <label for="tipoDeclaracion" class="offset-sm-2 col-sm-4 col-form-label text-right text-white">Tipo de Declaración:</label>
    <div class="col-sm-4">
      <select id="tipoDeclaracion" name="tipoDeclaracion" class="form-control">
        <option value="XX" selected>Normal</option>
        <option value="CX">Complementaria</option>
        <option value="XS">Sustitutiva</option>
      </select>
    </div>
  </div>

  <div class="form-group row" style="display:none;" id="declAnteriorDiv">
    <label for="declaracionAnterior" class="offset-sm-2 col-sm-4 col-form-label text-right text-white">ID Declaración Anterior:</label>
    <div class="col-sm-4">
      <input type="number" class="form-control" id="declaracionAnterior" name="declaracionAnterior" pattern="[0-9]{13}" placeholder="ej:1122334455667">
      <div class="invalid-tooltip">Selecciona un número de declaración válido</div>
    </div>
  </div>

  <input type="hidden" name="center" id="center" value="<?= $_GET["center"] ?>">

</div>