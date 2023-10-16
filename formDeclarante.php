<div id="formularioDeclarante" style="display: none;" class="center-container">
  <div class="form-group row">
    <label for="ejercicio" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Ejercicio</b> actual:</label>
    <div class="col-sm-4">
      <input type="year" class="form-control" id="ejercicio" name="ejercicio" pattern="[0-9]{4}" min="2015" max="2099" placeholder="ej:<?php echo Date("Y")?>" required>
    </div>
  </div>  

  <div class="form-group row">
    <label for="nif" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>NIF</b> del declarante:</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" id="nif" name="nif" pattern="[0-9]{8}[A-Z]{1}" placeholder="ej:12345678Z" required>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="denominacion" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Denominación</b> del declarante:</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" id="denominacion" name="denominacion" placeholder="ej:Fundación XYZ" maxlength="40" required>
    </div>
  </div>

  <div class="form-group row">
    <label for="telefono" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Teléfono</b> de contacto:</label>
    <div class="col-sm-4">
      <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" placeholder="ej:654321987" required>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="persona" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Persona</b> de Contacto:</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" id="persona" name="persona" maxlength="40" placeholder="ej:Antonio Pérez" required>
    </div>
  </div>

  <div class="form-group row">
    <label for="justificante" class="offset-sm-2 col-sm-4 col-form-label text-right text-white"><b>Número</b> justificante declaración:</label>
    <div class="col-sm-4">
      <input type="number" class="form-control" id="justificante" name="justificante" pattern="[0-9]{13}" placeholder="ej:1122334455667" required>
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
    </div>
  </div>

</div>