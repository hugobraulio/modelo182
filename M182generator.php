<?php
$PROVINCIAS = [
  "Araba/Álava" => ["01","14"],
  "Albacete" => ["02","07"],
  "Alicante/Alacant" => ["03","17"],
  "Almería" => ["04","01"],
  "Ávila" => ["05","08"],
  "Badajoz" => ["06","10"],
  "Balears,Illes" => ["07","04"],
  "Barcelona" => ["08","09"],
  "Burgos" => ["09","08"],
  "Cáceres" => ["10","10"],
  "Cádiz" => ["11","01"],
  "Castellón,Castelló" => ["12","17"],
  "Ciudad Real" => ["13","07"],
  "Córdoba" => ["14","01"],
  "Coruña,A" => ["15","11"],
  "Cuenca" => ["16","07"],
  "Girona" => ["17","09"],
  "Granada" => ["18","01"],
  "Guadalajara" => ["19","07"],
  "Gipuzkoa/Guipúzcoa" =>  ["20","14"],
  "Huelva" => ["21","01"],
  "Huesca" => ["22","02"],
  "Jaén" => ["23","01"],
  "León" => ["24","08"],
  "Lleida" => ["25","09"],
  "La Rioja" => ["26","16"],
  "Lugo" => ["27","11"],
  "Madrid" => ["28","12"],
  "Málaga" => ["29","01"],
  "Murcia" => ["30","13"],
  "Navarra" => ["31","15"],
  "Ourense" => ["32","11"],
  "Asturias" => ["33","03"],
  "Palencia" => ["34","08"],
  "Las Palmas" => ["35","05"],
  "Pontevedra" => ["36","11"],
  "Salamanca" => ["37","08"],
  "Santa Cruz de Tenerife" => ["38","05"],
  "Cantabria" => ["39","06"],
  "Segovia" => ["40","08"],
  "Sevilla" => ["41","01"],
  "Soria" => ["42","08"],
  "Tarragona" => ["43","09"],
  "Teruel" => ["44","02"],
  "Toledo" => ["45","07"],
  "Valencia/València" =>  ["46","17"],
  "Valladolid" => ["47","08"],
  "Bizkaia/Vizcaya" =>  ["48","14"],
  "Zamora" => ["49","08"],
  "Zaragoza" => ["50","02"],
  "Ceuta" => ["51","18"],
  "Melilla" => ["52","19"]
];
function generateModelo182($csvData) {
    global $PROVINCIAS;
    $txtContent = generateInitialM182line();
    $is_headers = true;
    
    foreach ($csvData as $row) {
      if ($is_headers) {
        //skip first $row, with header titles
        $is_headers = false;
        continue;
      }
      
      list($nif,$apellidos,$nombre,$provincia,$donacion,$moneda) = $row;

      $moneda = (float)$moneda;

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
      $prov_code = $PROVINCIAS[$provincia][0];
      # 78 CLAVE
      $clave = 'A';
      # 79-83 PORCENTAJE DE DEDUCCION
      //TO DO: calcular en base a declaraciones anteriores
      $deduc = "03500";
      # 84-96 IMPORTE (84-94 importe, 95-96 decimales)
      $importe = str_pad((int)$moneda, 11, 0, STR_PAD_LEFT);
      $decimales = substr(sprintf("%.2f", $importe), -2);
      # 97 EN ESPECIE
      $especie = str_repeat(" ",1);
      # 98-99 DEDUCCION COMUNIDAD AUTONOMA
      $deduc_ca = str_repeat(" ",2);
      # 100-104 % DEDUCCION COMUNIDAD AUTONOMA
      $deduc_ca_porc = str_repeat(" ",5);
      # 105 NATURALEZA DEL DECLARADO
      $natur = "F";
      #106 REVOCACION (¿Siempre en blanco? SI)
      $revoc = " ";
      #107-110 REVOCACION
      $revoc2 = "0000";
      #111 TIPO DE BIEN
      $bien = " ";
      #112-131 IDENTIFICACION DEL BIEN
      $bien_id = str_repeat(" ",20);
      #132 RECURRENCIA DONATIVOS
      //TO DO
      //# Forzar conversion de bool --> 1 ó 2
      //dflineas2["Tipo2"] += (mask_recurrentes * -1 + 2).astype(str)
      #133-250 BLANCOS
      $blancos = str_repeat(" ",118);

      $txtContent .= $nif_row.$nif_repr.$nombre.$prov_code.$clave.$deduc;
      $txtContent .= $importe.$decimales.$especie.$deduc_ca.$deduc_ca_porc;
      $txtContent .= $natur.$revoc.$revoc2.$bien.$bien_id.$blancos."\n";
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