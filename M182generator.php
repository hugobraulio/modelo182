<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
function generateModelo182($csvData, $resumen) {
    $txtHash = [];
    $txtContent = "";
    $is_headers = true;
    foreach ($csvData as $row) {
      $resumen->totalRegistros++;
      if ($is_headers) {
        //skip first $row, with header titles
        $is_headers = false;
        continue;
      }
      [$nombre,$content] = _generateTipo2Row($row, $resumen);
      $txtHash[$nombre] = $content;
    }
    ksort($txtHash);
    $txtRows = array_values($txtHash);
    foreach ($txtRows as $txtRow) {
      $txtContent .= $txtRow;
    }
    $initialTxt = _generateTipo1Row($resumen->totalImporteM182,$resumen->totalRegistrosM182);
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
  $total_registros = mb_str_pad($totalRegistros, 9, 0, STR_PAD_LEFT);
  $total_donaciones = mb_str_pad((int)$totalImporte, 13, 0, STR_PAD_LEFT);
  $decimales_donaciones = substr(sprintf("%.2f", $totalImporte), -2); ///to do. total anterior, los decimales (2)
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

function _generateTipo2Row($row, $resumen){
  list($nif, $apellidos, $nombre, $nprov, $npais, $tprov, $tpais, 
       $htel, $ftel, $mtel, $emails, $gender, $dob, $age, $donacion, $moneda) = $row;

  $donacion = (float)$donacion;
  $resumen->totalImporte += $donacion;

  $provincia = $nprov; //empty($tprov) ? $nprov : $tprov;
  $pais = $npais; //empty($tpais) ? $npais : $tpais;
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

  $esEmpresa = false;
  if (empty($apellidos)) {
    if (seemsLikeACompany($nif)) {
      $apellidos = "EMPRESA";
      $resumen->casos_csv["empresas"][] = $caso_csv;
      $resumen->casos_array["empresas"][] = $caso_array;
      $esEmpresa = true;
    }
    else {
      $resumen->casos_csv["falta_apellido"][] = $caso_csv;
      $resumen->casos_array["falta_apellido"][] = $caso_array;
      return "";
    }
  }

  //ignore other country persons
  if ($pais != "España" && $pais != "Spain"){
    if (_validateSpanishID($nif)) {
      $resumen->casos_csv["extranjeros_dni_correcto"][] = $caso_csv;
      $resumen->casos_array["extranjeros_dni_correcto"][] = $caso_array;
      return "";
    } else if ($nombre == "Anonymous" && $apellidos == "Anonymous") {
      $resumen->casos_csv["anonimos"][] = "Donantes anónimos | Importe total: ".$donacion." €";
      $resumen->casos_array["anonimos"][] = $caso_array;
      return "";
    } else {
      $resumen->casos_csv["extranjeros"][] = $caso_csv;
      $resumen->casos_array["extranjeros"][] = $caso_array;
      return "";
    }
  }
  
  #TIPO_DE_REGISTRO + MODELO_DECLARACION + EJERCICIO + NIF_DECLARANTE
  $constant = "2";
  $model = "182";
  $ejercicio = $_POST["ejercicio"];
  $nif_decl = strtoupper($_POST["nif"]);
  $initial_line = $constant.$model.$ejercicio.$nif_decl;
  $txtContent = $initial_line;

  # 18-26 NIF_DECLARADO
  $nif = strtoupper($nif);
  $nif = str_replace(".","",$nif);
  $nif = str_replace("-","",$nif);
  $nif = str_replace(" ","",$nif);
  if (_validateSpanishID($nif) || $esEmpresa) {
    $nif = strtoupper($nif);
  } else {
    $resumen->casos_csv["residentes_dni_incorrecto"][] = $caso_csv;
    $resumen->casos_array["residentes_dni_incorrecto"][] = $caso_array;
    return "";
  }
  # 25-37 NIF REPRESENTANTE LEGAL
  $nif_repr = str_repeat(' ',9);

  # 36-75 APELLIDOS Y NOMBRE
  $nombre = $apellidos.' '.$nombre;
  $nombre = strtr($nombre, $resumen->replacements);
  $nombre = strtoupper($nombre);
  $nombre = mb_str_pad($nombre, 40, " ", STR_PAD_RIGHT);

  # 76-77 CODIGO DE PROVINCIA
  if (empty($resumen->provincias[$provincia])){
    $resumen->casos_csv["residentes_prov_incorrecta"][] = $caso_csv;
    $resumen->casos_array["residentes_prov_incorrecta"][] = $caso_array;
    return "";
  }
  $prov_code = $resumen->provincias[$provincia][0];
  
  //No more particular cases. So if we reach here this will go to the M182 register
  $resumen->totalImporteM182 += $donacion;
  $resumen ->totalRegistrosM182++;

  # 78 CLAVE
  $clave = 'A';

  # 84-96 IMPORTE (84-94 importe, 95-96 decimales)
  $importe = str_pad((int)$donacion, 11, 0, STR_PAD_LEFT);
  $decimales = substr(sprintf("%.2f", $donacion), -2);

  # 79-83 PORCENTAJE DE DEDUCCION
  $esRecurrente = false;
  if ($donacion <= 150 && !$esEmpresa) {
    $deduc = "08000";
  } else if ((array_key_exists($nif, $resumen->donantes1año) &&
              array_key_exists($nif, $resumen->donantes2años) &&
              (int)$resumen->donantes1año[$nif] <= (int)$donacion &&
              (int)$resumen->donantes2años[$nif] <= (int)$resumen->donantes1año[$nif])) {
    $esRecurrente = true;
    //$caso_array[6] = "1er año: ".$resumen->donantes2años[$nif]." <= 2ndo año: ".$resumen->donantes1año[$nif]." <= actual: ".$donacion;
    $resumen->casos_csv["recurrentes"][] = $caso_csv;
    $resumen->casos_array["recurrentes"][] = $caso_array;
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
  $natur = $esEmpresa ? "J" : "F";
  # 106 REVOCACION (¿Siempre en blanco? SI)
  $revoc = " ";
  # 107-110 REVOCACION
  $revoc2 = "0000";
  # 111 TIPO DE BIEN
  $bien = " ";
  # 112-131 IDENTIFICACION DEL BIEN
  $bien_id = str_repeat(" ",20);
  
  # 132 RECURRENCIA DONATIVOS
  $recurrente = $esRecurrente ? "1" : "2";
  
  # 133-250 BLANCOS
  $blancos = str_repeat(" ",118);

  $txtContent .= $nif.$nif_repr.$nombre.$prov_code.$clave.$deduc;
  $txtContent .= $importe.$decimales.$especie.$deduc_ca.$deduc_ca_porc;
  $txtContent .= $natur.$revoc.$revoc2.$bien.$bien_id.$recurrente.$blancos."\n";
  return [$nombre, $txtContent];
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
    else {
        return false;
    }
}

function seemsLikeACompany($id) {
  return preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $id);
}

function _csv($field){
  return (strpos($field, ',') !== false) ? '"'.$field.'"' : $field;
}

function generateSummaryHTML($resumen){
  $summary = "<br/><br/><p><div class='titlebig'><h3>TOTAL REGISTROS Y DONACIONES</h3></div></p>";
  $summary .= "<p>Número de donantes: <span style='color: #FFD700'>".$resumen->totalRegistros." donantes</span></p>";
  $summary .= "<p>Número de donantes Modelo 182: <span style='color: #FFD700'>".$resumen->totalRegistrosM182." donantes</span></p>";
  $summary .= "<br/><p>Total donaciones: <span style='color: #FFD700'>".number_format($resumen->totalImporte, 2, ',', '.')." €</span></p>";
  $summary .= "<p>Total donaciones Modelo 182: <span style='color: #FFD700'>".number_format($resumen->totalImporteM182, 2, ',', '.')." €</span></p>";
  $summary .= "<br/><p>Casos particulares incluidos en el TXT de Hacienda:</p>";
  $summary .= "<ul class='offset-sm-3' style='text-align:left'>";
  $empresas = $resumen->casos_array["empresas"];
  $summary .= "<li><a style='color:white;' href='#empresas'>Total empresas (apellido 'EMPRESA'): <span style='color: #FFD700'>".count($empresas)." caso(s)</span></a></li>";
  $recurrentes = $resumen->casos_array["recurrentes"];
  $summary .= "<li><a style='color:white;' href='#recurrentes'>Total recurrentes (donantes 3 años consecutivos): <span style='color: #FFD700'>".count($recurrentes)." caso(s)</span></a></li>";
  $summary .= "</ul>";
  $summary .= "<br/><p>Casos particulares NO INCLUIDOS en el TXT:</p>";
  $summary .= "<ul class='offset-sm-3' style='text-align:left'>";
  $res_dni_mal = $resumen->casos_array["residentes_dni_incorrecto"];
  $summary .= "<li><a style='color:white;' href='#nif_incorrecto'>Total residentes con DNI/NIE/NIF incorrecto: <span style='color: #FFD700'>".count($res_dni_mal)." caso(s)</span></a></li>";
  $res_prov_mal = $resumen->casos_array["residentes_prov_incorrecta"];
  $summary .= "<li><a style='color:white;' href='#prov_incorrecta'>Total residentes con provincia incorrecta o vacía: <span style='color: #FFD700'>".count($res_prov_mal)." caso(s)</span></a></li>";
  $extr_dni_bien = $resumen->casos_array["extranjeros_dni_correcto"];
  $summary .= "<li><a style='color:white;' href='#extr_nif_correcto'>Total residentes en el extranjero con NIF correcto: <span style='color: #FFD700'>".count($extr_dni_bien)." caso(s)</span></a></li>";
  $falta_apellido = $resumen->casos_array["falta_apellido"];
  $summary .= "<li><a style='color:white;' href='#falta_apellido'>Total residentes sin apellido: <span style='color: #FFD700'>".count($falta_apellido)." caso(s)</span></a></li>";
  $moneda_extr = $resumen->casos_array["moneda_extranjera"];
  $summary .= "<li><a style='color:white;' href='#moneda_extranjera'>Total donaciones con moneda extranjera: <span style='color: #FFD700'>".count($moneda_extr)." caso(s)</span></a></li>";
  $summary .= "</ul>";
  $summary .= "<p><pre style=\"color:white\">Se ha descargado automáticamente el resumen en formato .CSV</pre></p>";
  $anonimos = $resumen->casos_csv["anonimos"];
  $recurrentes = $resumen->casos_array["recurrentes"];
  $extranjeros = $resumen->casos_array["extranjeros"];

  $summary .= "<br/><br/><p><div class='title'><h3>CASOS PARTICULARES</h3></div></p>";
  if (count($res_dni_mal) > 0) {
    $summary .= "<a id='nif_incorrecto'><div class='title'>RESIDENTES NIF/NIE INCORRECTOS (".count($res_dni_mal).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($res_dni_mal);
  }
  if (count($res_prov_mal) > 0) {
    $summary .= "<br/><br/><a id='prov_incorrecta'><div class='title'>RESIDENTES PROVINCIA INCORRECTA (".count($res_prov_mal).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($res_prov_mal);
  }
  if (count($extr_dni_bien) > 0) {
    $summary .= "<br/><br/><a id='extr_nif_correcto'><div class='title'>RESIDENTES EN EL EXTRANJERO CON NIF/NIE CORRECTOS (".count($extr_dni_bien).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($extr_dni_bien);
  }
  if (count($empresas) > 0) {
    $summary .= "<br/><br/><a id='empresas'><div class='title'>EMPRESAS (".count($empresas).")</div>";
    $summary .= "<br/><div class='title'>(INCLUIDOS en el TXT de Hacienda con apellido 'EMPRESA')</div>";
    $summary .= _generateSummaryTable($empresas);
  }
  if (count($recurrentes) > 0) {
    $summary .= "<br/><br/><a id='recurrentes'><div class='title'>RECURRENTES (donantes tres años consecutivos) (".count($recurrentes).")</div></a>";
    $summary .= "<br/><div class='title'>(INCLUIDOS en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($recurrentes);
  }
  if (count($falta_apellido) > 0) {
    $summary .= "<br/><br/><a id='falta_apellido'><div class='title'>FALTA EL APELLIDO (".count($falta_apellido).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($falta_apellido);
  }
  if (count($moneda_extr) > 0) {
    $summary .= "<br/><br/><a id='moneda_extranjera'><div class='title'>DONACIONES CON MONEDA EXTRANJERA (".count($moneda_extr).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= _generateSummaryTable($moneda_extr,false);
  }
  if (count($anonimos) > 0) {
    $summary .= "<br/><br/><a id='anonimos'><div class='title'>ANÓNIMOS (acumulados en una fila)</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
    $summary .= "<br/><br/>".$anonimos[0]."";
  }
  if (count($extranjeros) > 0) {
    $summary .= "<br/><br/><br/><br/><a id='extranjeros'><div class='title'>RESIDENTES EN EL EXTRANJERO NO CONSIDERADOS (".count($extranjeros).")</div></a>";
    $summary .= "<br/><div class='title'>(no incluidos en el TXT de Hacienda)</div>";
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
      $moneda = $is_eur ? " €" : " ".$caso[11];
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
  $summary .= _generateSubSummary($extr_dni_bien,"RESIDENTES EN EL EXTRANJERO CON DNI/NIF/NIE CORRECTOS");
  $empresas = $resumen->casos_csv["empresas"];
  $summary .= _generateSubSummary($empresas,"EMPRESAS");
  $recurrentes = $resumen->casos_csv["recurrentes"];
  $summary .= _generateSubSummary($recurrentes,"RECURRENTES");
  $falta_apellido = $resumen->casos_csv["falta_apellido"];
  $summary .= _generateSubSummary($falta_apellido,"FALTA EL APELLIDO");
  $moneda_extr = $resumen->casos_csv["moneda_extranjera"];
  $summary .= _generateSubSummary($moneda_extr,"DONACIONES CON MONEDA EXTRANJERA");
  $extranjeros = $resumen->casos_csv["extranjeros"];
  $summary .= _generateSubSummary($extranjeros,"RESIDENTES EN EL EXTRANJERO NO INCLUIDOS EN EL TXT");
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