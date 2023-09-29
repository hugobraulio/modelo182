<?php
function generateModelo182($csvData) {
    $txtContent = generateInitialM182line();
    
    foreach ($csvData as $row) {
      // Assuming columns in CSV: donor_id, amount, ...
      list($donor_id, $amount /* , ... */) = $row;

      // Format the data according to Modelo 182 specifications
      $constant = "2";
      $model = "182";
      $last_year = (date("Y") - 1);
      $formatted_line = sprintf(
        $constant.$model.$last_year."%010s%010d\n",
        $donor_id,
        $amount * 100 // Assuming the amount should be written in cents
      );

      // Append to TXT content
      $txtContent .= $formatted_line;
    }

  return $txtContent;
}

function generateInitialM182line(){
  $txtContent = "";
  return $txtContent;
}
?>