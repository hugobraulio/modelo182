<?php
function generateModelo182($csvData) {
    $txtContent = generateInitialM182line();
    $is_headers = true;
    
    foreach ($csvData as $row) {
      if ($is_headers) {
        //skip first $row, with header titles
        $is_headers = false;
        continue;
      }
      
      list($nif,$apellidos,$nombre,$provincia,$donacion,$moneda) = $row;

      #TIPO_DE_REGISTRO + MODELO_DECLARACION + EJERCICIO + NIF_DECLARANTE
      $constant = "2";
      $model = "182";
      $ejercicio = $_POST["ejercicio"];
      $nif_decl = $_POST["nif"];
      $initial_line = $constant.$model.$ejercicio.$nif_decl;
      $txtContent .= $initial_line;

      # 18-26 NIF_DECLARADO
      $nif_row = validateNIForNIE($nif) ? strtoupper($nif) : str_repeat(' ',9);
      # 25-37 NIF REPRESENTANTE LEGAL
      $nif_repr = str_repeat(' ',9);
      # 36-75 APELLIDOS Y NOMBRE
      $nombre = str_pad($apellidos.' '.$nombre,40," ", STR_PAD_RIGHT);
      # 76-77 CODIGO DE PROVINCIA
    }

  return $txtContent;
}

function generateInitialM182line(){
  $tipo_reg = "1";
  $mod_decl = "182";
  $ejercicio = $_POST["ejercicio"];
  $nif_decl = $_POST["nif"];
  $denominacion = str_pad($_POST["denominacion"], 40, " ", STR_PAD_RIGHT);
  $tipo_soporte = "T";
  $telefono = $_POST["telefono"];
  $persona = str_pad($_POST["persona"], 40, " ", STR_PAD_RIGHT);
  $justificante = $_POST["justificante"];
  $tipo_decl = str_replace('X', ' ', $_POST["tipoDeclaracion"]);
  $decl_anterior = str_pad($_POST["declaracionAnterior"], 13, 0, STR_PAD_LEFT);
  $total_registros = ''; //to do
  $total_donaciones = ''; //to do. total sin decimales, 13 zeros
  $decimales_donaciones = ''; ///to do. total anterior, los decimales (2)
  $naturaleza_decl = '1';
  $nif_titular_patrimonio = str_repeat(' ',9);
  $nombre_titular_patrimonio = str_repeat(' ',40);
  $blancos = str_repeat(' ',28);
  $sello_electronico = str_repeat(' ',13);
      
  $txtContent = $tipo_reg.$mod_decl.$ejercicio.$nif_decl.$denominacion.$tipo_soporte;
  $txtContent .= $telefono.$persona.$justificante.$tipo_decl.$decl_anterior;
  $txtContent .= $total_registros.$total_donaciones.$decimales_donaciones;
  $txtContent .= $naturaleza_decl.$nif_titular_patrimonio.$nombre_titular_patrimonio;
  $txtContent .= $blancos.$sello_electronico;
  return $txtContent;
}

function validateNIForNIE($id) {
    $id = strtoupper($id);
    
    // Check NIE, first character must be X, Y or Z
    if (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $id)) {
        // Replace first letter with corresponding number: X->0, Y->1, Z->2
        $id = strtr($id, 'XYZ', '012') . substr($id, 1);
    }
    
    // Check length and format (now NIE should be converted into NIF format)
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $id)) {
        return false;
    }
    
    // Extract the number and letter
    $number = substr($id, 0, 8);
    $letter = substr($id, 8, 1);
    
    // Calculate the letter
    $valid_letters = "TRWAGMYFPDXBNJZSQVHLCKE";
    $index = intval($number) % 23;
    $calculated_letter = $valid_letters[$index];
    
    // Check if the letter is valid
    return $calculated_letter == $letter;
}

?>