<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
function generateModelo182($csvData, $resumen) {
    $txtContent = "";
    $is_headers = true;
    foreach ($csvData as $row) {
      $resumen->totalRegistros++;
      if ($is_headers) {
        //skip first $row, with header titles
        $is_headers = false;
        continue;
      }
      $txtContent .= _generateTipo2Row($row, $resumen);
    }
    $initialTxt = _generateTipo1Row($resumen->totalImporte,$resumen->totalRegistros);
    $initialTxt .= $txtContent;

  return $initialTxt;
}

function _generateTipo1Row($totalImporte,$totalRegistros){
  $tipo_reg = "1";
  $mod_decl = "182";
  $ejercicio = $_POST["ejercicio"];
  $nif_decl = $_POST["nif"];
  $denominacion = mb_str_pad($_POST["denominacion"], 40, " ", STR_PAD_RIGHT);
  $tipo_soporte = "T";
  $telefono = $_POST["telefono"];
  $persona = mb_str_pad($_POST["persona"], 40, " ", STR_PAD_RIGHT);
  $justificante = $_POST["justificante"];
  $tipo_decl = str_replace('X', ' ', $_POST["tipoDeclaracion"]);
  $decl_anterior = mb_str_pad($_POST["declaracionAnterior"], 13, 0, STR_PAD_LEFT);
  $total_registros = $totalRegistros;
  $total_donaciones = (int)$totalImporte;
  $decimales_donaciones = substr(sprintf("%.2f", $totalImporte), -2); ///to do. total anterior, los decimales (2)
  $naturaleza_decl = '1';
  $nif_titular_patrimonio = str_repeat(' ',9);
  $nombre_titular_patrimonio = str_repeat(' ',40);
  $blancos = str_repeat(' ',28);
  $sello_electronico = str_repeat(' ',13);
      
  $txtContent = $tipo_reg.$mod_decl.$ejercicio.$nif_decl.$denominacion.$tipo_soporte;
  $txtContent .= $telefono.$persona.$justificante.$tipo_decl."DECL_ANT:".$decl_anterior;
  $txtContent .= "TOTAL_REG:".$total_registros."TOTAL_DON:".$total_donaciones."DECIM:".$decimales_donaciones;
  $txtContent .= $naturaleza_decl.$nif_titular_patrimonio.$nombre_titular_patrimonio;
  $txtContent .= $blancos.$sello_electronico;
  return $txtContent;
}

function _generateTipo2Row($row, $resumen){
  list($nif, $apellidos, $nombre, $nprov, $npais, $tprov, $tpais, 
       $htel, $ftel, $mtel, $emails, $gender, $dob, $age, $donacion, $moneda) = $row;

  $donacion = (float)$donacion;
  $resumen->totalImporte += $donacion;

  $provincia = empty($nprov) ? $tprov : $nprov;
  $pais = empty($npais) ? $tpais : $npais;
  $tel = empty($mtel) ? (empty($htel) ? $ftel : $htel) : $mtel;

  $caso_csv = _csv($nombre).","._csv($apellidos).","._csv($nif).",";
  $caso_csv .= _csv($provincia).","._csv($pais).","._csv($tel).",";
  $caso_csv .= _csv($emails).","._csv($gender).","._csv($dob).",";
  $caso_csv .= _csv($age).","._csv($donacion).","._csv($moneda);
  $caso_array = [$nombre, $apellidos, $nif, $provincia, $pais, $tel, $emails, $gender, $dob, $age, $donacion, $moneda];

  if ($moneda != "EUR") {
    $resumen->casos_csv["moneda_extranjera"][] = $caso_csv;
    $resumen->casos_array["moneda_extranjera"][] = $caso_array;
    return "";
  }

  if (empty($apellidos)) {
    if (seemsLikeACompany($nif)) {
      $apellidos = "EMPRESA";
      $resumen->casos_csv["empresas"][] = $caso_csv;
      $resumen->casos_array["empresas"][] = $caso_array;
    }
    else {
      $resumen->casos_csv["falta_apellido"][] = $caso_csv;
      $resumen->casos_array["falta_apellido"][] = $caso_array;
    }
  }

  //ignore other country persons
  if ($pais != "España" && $pais != "Spain"){
    if (_validateSpanishID($nif) && !seemsLikeACompany($nif)) {
      $resumen->casos_csv["extranjeros_dni_correcto"][] = $caso_csv;
      $resumen->casos_array["extranjeros_dni_correcto"][] = $caso_array;
    } else if ($nombre == "Anonymous" && $apellidos == "Anonymous") {
      $resumen->casos_csv["anonimos"][] = "Donantes anónimos | Importe total: ".$donacion." €";
      $resumen->casos_array["anonimos"][] = $caso_array;
    } else {
      $resumen->casos_csv["extranjeros"][] = $caso_csv;
      $resumen->casos_array["extranjeros"][] = $caso_array;
    }
    return "";
  }
  
  #TIPO_DE_REGISTRO + MODELO_DECLARACION + EJERCICIO + NIF_DECLARANTE
  $constant = "2";
  $model = "182";
  $ejercicio = $_POST["ejercicio"];
  $nif_decl = $_POST["nif"];
  $initial_line = $constant.$model.$ejercicio.$nif_decl;
  $txtContent = $initial_line;

  # 18-26 NIF_DECLARADO
  $nif = strtoupper($nif);
  $nif = str_replace(".","",$nif);
  $nif = str_replace("-","",$nif);
  $nif = str_replace(" ","",$nif);
  if (_validateSpanishID($nif)) {
    $nif = strtoupper($nif);
  } else {
    $resumen->casos_csv["residentes_dni_incorrecto"][] = $caso_csv;
    $resumen->casos_array["residentes_dni_incorrecto"][] = $caso_array;
    return "";
  }
  # 25-37 NIF REPRESENTANTE LEGAL
  $nif_repr = str_repeat(' ',9);

  # 36-75 APELLIDOS Y NOMBRE
  $nombre = mb_str_pad($apellidos.' '.$nombre,40," ", STR_PAD_RIGHT);

  # 76-77 CODIGO DE PROVINCIA
  if (!empty($resumen->provincias[$provincia])){
    $prov_code = $resumen->provincias[$provincia][0];
  }
  else { //provincia inexistente
    $resumen->casos_csv["residentes_prov_incorrecta"][] = $caso_csv;
    $resumen->casos_array["residentes_prov_incorrecta"][] = $caso_array;
    return "";
  }

  # 78 CLAVE
  $clave = 'A';

  # 84-96 IMPORTE (84-94 importe, 95-96 decimales)
  $importe = str_pad((int)$donacion, 11, 0, STR_PAD_LEFT);
  $decimales = substr(sprintf("%.2f", $donacion), -2);

  # 79-83 PORCENTAJE DE DEDUCCION
  $esRecurrente = false;
  if ($donacion <= 150) {
    $deduc = "08000";
  } else if (_esDonanteRecurrente($nif, $donacion, $resumen)) { 
    $esRecurrente = true;
    $deduc = "04000";
  } else {
    $deduc = "03500";
  }

  # 97 EN ESPECIE
  $especie = str_repeat(" ",1);
  # 98-99 DEDUCCION COMUNIDAD AUTONOMA
  $deduc_ca = str_repeat("0",2);
  # 100-104 % DEDUCCION COMUNIDAD AUTONOMA
  $deduc_ca_porc = str_repeat("0",5);
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
  $recurrente = $esRecurrente ? "1" : "2";
  
  #133-250 BLANCOS
  $blancos = str_repeat(" ",118);

  $txtContent .= $nif.$nif_repr.$nombre.$prov_code.$clave.$deduc;
  $txtContent .= $importe.$decimales.$especie.$deduc_ca.$deduc_ca_porc;
  $txtContent .= $natur.$revoc.$revoc2.$bien.$bien_id.$recurrente.$blancos."\n";
  return $txtContent;
}
function mb_str_pad($input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_RIGHT) {
    //multi byte str_pad
    $diff = strlen($input) - mb_strlen($input);
    return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
}
function _validateSpanishID($id) {
    // NIF
    if (preg_match('/^[0-9]{8}[A-Z]$/', $id)) {
        $letter = substr($id, -1);
        $numbers = substr($id, 0, -1);
        $validChars = "TRWAGMYFPDXBNJZSQVHLCKE";
        if ($letter == $validChars[$numbers % 23]) {
            return true;
        }
    }
    // NIE
    elseif (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $id)) {
        $letter = substr($id, -1);
        $numbers = substr($id, 1, -1);
        $initial = substr($id, 0, 1);
        $validChars = "TRWAGMYFPDXBNJZSQVHLCKE";
        $initials = ['X' => '0', 'Y' => '1', 'Z' => '2'];
        if ($letter == $validChars[($initials[$initial] . $numbers) % 23]) {
            return true;
        }
    }
    // CIF (basic validation)
    elseif (seemsLikeACompany($id)){
        // Complex validation can go here
        return true;
    }
    return false;
}

function _esDonanteRecurrente ($nif, $donacion, $resumen){
  return (array_key_exists($nif, $resumen->donantes1año) &&
          array_key_exists($nif, $resumen->donantes2años) &&
          (int)$resumen->donantes1año[$nif] >= (int)$donacion &&
          (int)$resumen->donantes2años[$nif] >= (int)$donacion);
}

function seemsLikeACompany($id) {
  return preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $id);
}

function _csv($field){
  return (strpos($field, ',') !== false) ? '"'.$field.'"' : $field;
}

function generateSummaryHTML($resumen){
  $summary = "<p><div class='titlebig'>TOTAL REGISTROS Y DONACIONES</div></p>";
  $summary .= "<p>Número de donantes: ".$resumen->totalRegistros."</p>";
  $summary .= "<br/><p>Total donaciones: ".number_format($resumen->totalImporte, 2, ',', '.')." €</p>";
  $summary .= "<br/><p>Casos particulares: </p>";
  $summary .= "<ul class='offset-sm-3' style='text-align:left'>";
  $res_dni_mal = $resumen->casos_array["residentes_dni_incorrecto"];
  $summary .= "<li>Total residentes con DNI/NIE/NIF incorrecto: ".count($res_dni_mal)." caso(s)</li>";
  $res_prov_mal = $resumen->casos_array["residentes_prov_incorrecta"];
  $summary .= "<li>Total residentes con provincia incorrecta: ".count($res_prov_mal)." caso(s)</li>";
  $falta_apellido = $resumen->casos_array["falta_apellido"];
  $summary .= "<li>Total residentes sin apellido: ".count($falta_apellido)." caso(s)</li>";
  $extr_dni_bien = $resumen->casos_array["extranjeros_dni_correcto"];
  $summary .= "<li>Total extranjeros con DNI/NIE/NIF correcto: ".count($extr_dni_bien)." caso(s)</li>";
  $moneda_extr = $resumen->casos_array["moneda_extranjera"];
  $summary .= "<li>Total donaciones con moneda extranjera: ".count($moneda_extr)." caso(s)</li>";
  $empresas = $resumen->casos_array["empresas"];
  $summary .= "<li>Total empresas: ".count($empresas)." caso(s)</li>";
  $summary .= "</ul>";
  $summary .= "<p><pre style=\"color:white\">Se ha descargado automáticamente el resumen en formato .CSV</pre></p>";
  $anonimos = $resumen->casos_csv["anonimos"];
  $extranjeros = $resumen->casos_array["extranjeros"];

  $summary .= "<br/><br/><p><div class='title'>CASOS PARTICULARES</div></p>";
  if (count($res_dni_mal) > 0) {
    $summary .= "<div class='title'>RESIDENTES NIF/NIE INCORRECTOS (".count($res_dni_mal).")</div>";
    $summary .= _generateSummaryTable($res_dni_mal);
  }
  if (count($res_prov_mal) > 0) {
    $summary .= "<br/><br/><div class='title'>RESIDENTES PROVINCIA INCORRECTA (".count($res_prov_mal).")</div>";
    $summary .= _generateSummaryTable($res_prov_mal);
  }
  if (count($extr_dni_bien) > 0) {
    $summary .= "<br/><br/><div class='title'>EXTRANJEROS CON NIF/NIE CORRECTOS (".count($extr_dni_bien).")</div>";
    $summary .= _generateSummaryTable($extr_dni_bien);
  }
  if (count($falta_apellido) > 0) {
    $summary .= "<br/><br/><div class='title'>FALTA EL APELLIDO (".count($falta_apellido).")</div>";
    $summary .= _generateSummaryTable($falta_apellido);
  }
  if (count($moneda_extr) > 0) {
    $summary .= "<br/><br/><div class='title'>DONACIONES CON MONEDA EXTRANJERA (".count($moneda_extr).")</div>";
    $summary .= _generateSummaryTable($moneda_extr,false);
  }
  if (count($empresas) > 0) {
    $summary .= "<br/><br/><div class='title'>EMPRESAS (".count($empresas).")</div>";
    $summary .= _generateSummaryTable($empresas);
  }
  if (count($anonimos) > 0) {
    $summary .= "<br/><br/><div class='title'>ANÓNIMOS (acumulados en una fila)</div>";
    $summary .= "<br/><br/>".$anonimos[0]."";
  }
  if (count($extranjeros) > 0) {
    $summary .= "<br/><br/><div class='title'>EXTRANJEROS NO CONSIDERADOS (".count($extranjeros).")</div>";
    $summary .= _generateSummaryTable($extranjeros);
  }
  return $summary;
}
function _generateSummaryTable($casos,$is_eur=true){
  sort($casos);
  $summary = "";
  if (!empty($casos)) {
    $summary .= "
    <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>NIF/NIE/CIF</th>
        <th>Provincia (País)</th>
        <th>Teléfono(s)</th>
        <th>Email(s)</th>
        <th>Género</th>
        <th>Fecha Nac.</th>
        <th>Edad</th>
        <th>Donación</th>
      </tr>
    </thead>
    <tbody>";
    foreach($casos as $caso){
      $moneda = $is_eur ? " €" : " ".$caso[8];
      $summary .= "
      <tr>
        <td>".$caso[0]."</td>
        <td>".$caso[1]."</td>
        <td>".$caso[2]."</td>
        <td>".$caso[3]."(".$caso[4].")</td>
        <td>".$caso[5]."</td>
        <td>".$caso[6]."</td>
        <td>".$caso[7]."</td>
        <td>".$caso[8]."</td>
        <td>".$caso[9]."</td>
        <td>".$caso[10].$moneda."</td>
      </tr>";
    }
    $summary .= "</tbody></table>";
  }
  return $summary;
}
function generateSummaryCSV($resumen){
  $summary = "Nombre,Apellidos,NIF/NIE/DNI,Provincia,Pais,Teléfono,Email(s),Género,Fecha de Nacimiento,Edad,Importe,Moneda,ERROR\n";
  $res_dni_mal = $resumen->casos_csv["residentes_dni_incorrecto"];
  $summary .= _generateSubSummary($res_dni_mal,"RESIDENTES DNI INCORRECTO");
  $res_prov_mal = $resumen->casos_csv["residentes_prov_incorrecta"];
  $summary .= _generateSubSummary($res_prov_mal,"RESIDENTES PROVINCIA INCORRECTA");
  $extr_dni_bien = $resumen->casos_csv["extranjeros_dni_correcto"];
  $summary .= _generateSubSummary($extr_dni_bien,"EXTRANJEROS CON DNI/NIF/NIE CORRECTOS");
  $falta_apellido = $resumen->casos_csv["falta_apellido"];
  $summary .= _generateSubSummary($falta_apellido,"FALTA EL APELLIDO");
  $moneda_extr = $resumen->casos_csv["moneda_extranjera"];
  $summary .= _generateSubSummary($moneda_extr,"DONACIONES CON MONEDA EXTRANJERA");
  $empresas = $resumen->casos_csv["empresas"];
  $summary .= _generateSubSummary($empresas,"EMPRESAS");
  $extranjeros = $resumen->casos_csv["extranjeros"];
  $summary .= _generateSubSummary($extranjeros,"EXTRANJEROS NO CONSIDERADOS");
  return $summary;
}
function _generateSubSummary($casos, $type){
  sort($casos);
  $summary = "";
  foreach($casos as $caso){
    $summary .= $caso.",".$type."\n";
  }
  return $summary;
}

?>