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
  $denominacion = str_pad($_POST["denominacion"], 40, " ", STR_PAD_RIGHT);
  $tipo_soporte = "T";
  $telefono = $_POST["telefono"];
  $persona = str_pad($_POST["persona"], 40, " ", STR_PAD_RIGHT);
  $justificante = $_POST["justificante"];
  $tipo_decl = str_replace('X', ' ', $_POST["tipoDeclaracion"]);
  $decl_anterior = str_pad($_POST["declaracionAnterior"], 13, 0, STR_PAD_LEFT);
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
      $htel, $ftel, $mtel, $emails, $donacion, $moneda) = $row;

  $donacion = (float)$donacion;
  $resumen->totalImporte += $donacion;

  $provincia = empty($nprov) ? $tprov : $nprov;
  $pais = empty($npais) ? $tpais : $npais;
  $tel = empty($mtel) ? (empty($htel) ? $ftel : $htel) : $mtel;

  $caso_txt = $nombre." ".$apellidos." | ID: ".$nif." | ".$provincia."(".$pais.") | ".$tel." | ".$emails."\n";

  if ($moneda != "EUR") {
    $resumen->casos["moneda_extranjera"][] = "(".$moneda.")".$caso_txt;
    return "";
  }

  if (empty($apellidos)) {
    if (seemsLikeACompany($nif)) {
      $apellidos = "EMPRESA";
      $resumen->casos["empresas"][] = $caso_txt;
    }
    else {
      $resumen->casos["falta_apellido"][] = $caso_txt;
    }
  }

  //ignore other country persons
  if ($pais != "España" && $pais != "Spain"){
    if (_validateSpanishID($nif)) {
      $resumen->casos["extranjeros_dni_correcto"][] = $caso_txt;
    } else if ($nombre == "Anonymous" && $apellidos == "Anonymous") {
      $resumen->casos["anonimos"][] = "Donantes anónimos | Importe total: ".$donacion." €";
    } else {
      $resumen->casos["extranjeros"][] = $caso_txt;
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
  if (_validateSpanishID($nif)) {
    $nif_row = strtoupper($nif);
  } else {
    $resumen->casos["residentes_dni_incorrecto"][] = $caso_txt;
    return "";
  }
  # 25-37 NIF REPRESENTANTE LEGAL
  $nif_repr = str_repeat(' ',9);

  # 36-75 APELLIDOS Y NOMBRE
  $nombre = str_pad($apellidos.' '.$nombre,40," ", STR_PAD_RIGHT);

  # 76-77 CODIGO DE PROVINCIA
  $prov_code = 00;$resumen->provincias[$provincia][0];

  # 78 CLAVE
  $clave = 'A';

  # 84-96 IMPORTE (84-94 importe, 95-96 decimales)
  $importe = str_pad((int)$donacion, 11, 0, STR_PAD_LEFT);
  $decimales = substr(sprintf("%.2f", $donacion), -2);

  # 79-83 PORCENTAJE DE DEDUCCION
  //TO DO: calcular en base a declaraciones anteriores
  $deduc = ($donacion <= 150) ? "04000" : "03500";

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
  return $txtContent;
}

function _validateSpanishID($id) {
    $id = strtoupper($id);    
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

function seemsLikeACompany($id) {
  return preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $id);
}

function generateSummary($resumen){
  $summary = "\n\n----------- RESUMEN -----------  \n\n";
  $summary .= "Número de donantes: ".$resumen->totalRegistros."\n";
  $summary .= "Total donaciones: ".number_format($resumen->totalImporte, 2, ',', '.')." €";
  $summary .= "\n\n\n\n----------- CASOS PARTICULARES -----------\n";
  $res_dni_mal = $resumen->casos["residentes_dni_incorrecto"];
  $summary .= _generateSubSummary($res_dni_mal,"\nRESIDENTES NIF/NIE INCORRECTOS (".count($res_dni_mal)."):\n");
  $extr_dni_bien = $resumen->casos["extranjeros_dni_correcto"];
  $summary .= _generateSubSummary($extr_dni_bien,"\nEXTRANJEROS CON NIF/NIE CORRECTOS (".count($extr_dni_bien)."):\n");
  $falta_apellido = $resumen->casos["falta_apellido"];
  $summary .= _generateSubSummary($falta_apellido,"\nFALTA EL APELLIDO (".count($falta_apellido)."):\n");
  $moneda_extr = $resumen->casos["moneda_extranjera"];
  $summary .= _generateSubSummary($moneda_extr,"\nDONACIONES CON MONEDA EXTRANJERA (".count($moneda_extr)."):\n");
  $empresas = $resumen->casos["empresas"];
  $summary .= _generateSubSummary($empresas,"\nEMPRESAS (".count($empresas)."):\n");
  $anonimos = $resumen->casos["anonimos"];
  $summary .= _generateSubSummary($anonimos,"\nANÓNIMOS (acumulados en una fila) (".count($anonimos)."):\n");
  $extranjeros = $resumen->casos["extranjeros"];
  $summary .= _generateSubSummary($extranjeros,"\nEXTRANJEROS NO CONSIDERADOS (".count($extranjeros)."):\n");
  return $summary;
}

function _generateSubSummary($casos, $title){
  sort($casos);
  $summary = "";
  if (!empty($casos)) {
    $summary .= $title;
    foreach($casos as $caso){
      $summary .= $caso;
    }
  }
  return $summary;
}

?>