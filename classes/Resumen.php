<?php
class Resumen {
  public $resumen = "";
  public $provincias = [
    "Araba/Álava" => ["01","14"],
    "Albacete" => ["02","07"],
    "Alicante/Alacant" => ["03","17"],
    "Almería" => ["04","01"],
    "Ávila" => ["05","08"],
    "Badajoz" => ["06","10"],
    "Balears,Illes" => ["07","04"],
    "Balears, Illes" => ["07","04"],
    "Barcelona" => ["08","09"],
    "Burgos" => ["09","08"],
    "Cáceres" => ["10","10"],
    "Cádiz" => ["11","01"],
    "Castellón,Castelló" => ["12","17"],
    "Castellón/Castelló" => ["12","17"],
    "Ciudad Real" => ["13","07"],
    "Córdoba" => ["14","01"],
    "Coruña,A" => ["15","11"],
    "Coruña, A" => ["15","11"],
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

  public $provs = array(    
    "Araba/Álava",
    "Albacete",
    "Alicante/Alacant",
    "Almería",
    "Ávila",
    "Badajoz",
    "Balears,Illes",
    "Barcelona",
    "Burgos",
    "Cáceres",
    "Cádiz",
    "Castellón/Castelló",
    "Ciudad Real",
    "Córdoba",
    "Coruña,A",
    "Cuenca",
    "Girona",
    "Granada",
    "Guadalajara",
    "Gipuzkoa/Guipúzcoa" ,
    "Huelva",
    "Huesca",
    "Jaén",
    "León",
    "Lleida",
    "La Rioja",
    "Lugo",
    "Madrid",
    "Málaga",
    "Murcia",
    "Navarra",
    "Ourense",
    "Asturias",
    "Palencia",
    "Las Palmas",
    "Pontevedra",
    "Salamanca",
    "Santa Cruz de Tenerife",
    "Cantabria",
    "Segovia",
    "Sevilla",
    "Soria",
    "Tarragona",
    "Teruel",
    "Toledo",
    "Valencia/València" ,
    "Valladolid",
    "Bizkaia/Vizcaya" ,
    "Zamora",
    "Zaragoza",
    "Ceuta",
    "Melilla"
  );

  public $replacements = array(
    
    'Á' => 'A',
    'á' => 'a',
    'À' => 'a',
    'à' => 'a',
    'Ä' => 'A',
    'ä' => 'a',
    'Å' => 'A',
    'å' => 'a',
    'Ã' => 'A',
    'ã' => 'a',
    'É' => 'E',
    'é' => 'e',
    'È' => 'E',
    'è' => 'e',
    'Í' => 'I',
    'í' => 'i',
    'ì' => 'i',
    'Ì' => 'I',
    'Ï' => 'I',
    'ï' => 'i',
    'l·l' => 'll',
    'Ó' => 'O',
    'ó' => 'o',
    'ò' => 'o',
    'Ò' => 'O',
    'ö' => 'o',
    'Ö' => 'O',
    'Õ' => 'O',
    'õ' => 'o',
    'ú' => 'u',
    'Ú' => 'U',
    'ù' => 'u',
    'Ù' => 'U',
    'ü' => 'u',
    'Ü' => 'U',
    'Mª' => 'M',
    'mª' => 'm',
    'Mº' => 'M',
    'mº' => 'm',
    'ñ' => 'n',
    'Ñ' => 'N',
    'ç' => 'c',
    'Ç' => 'C'
  );
  public $totalImporte = 0.00;
  public $totalImporteM182 = 0.00;
  public $totalRegistros = 0;
  public $totalRegistrosM182 = 0;
  public $donantes1año = [];
  public $donantes2años = [];
  public $casos_csv = [
    "residentes_dni_incorrecto" => [],
    "residentes_prov_incorrecta" => [],
    "residentes_prov_cpostal" => [],
    "extranjeros_dni_correcto" => [],
    "extranjeros" => [],
    "empresas" => [],
    "anonimos" => [],
    "falta_apellido" => [],
    "moneda_extranjera" => [],
    "recurrentes" => [],
    "menores_sin_dni" => [],
    "menores_con_dni" => [],
    "menores_con_dni_incorrecto" => []
  ];
  public $casos_array = [
    "residentes_dni_incorrecto" => [],
    "residentes_prov_incorrecta" => [],
    "residentes_prov_cpostal" => [],
    "extranjeros_dni_correcto" => [],
    "extranjeros" => [],
    "empresas" => [],
    "anonimos" => [],
    "falta_apellido" => [],
    "moneda_extranjera" => [],
    "recurrentes" => [],
    "menores_sin_dni" => [],
    "menores_con_dni" => [],
    "menores_con_dni_incorrecto" => []
  ];
}