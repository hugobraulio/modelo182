
document.getElementById('ejercicio').onchange = function() {
  label1 = document.getElementById('labeltxt1');
  label1.textContent = "Selecciona TXT año anterior (" + (parseInt(this.value) - 1).toString() + ")";
  label2 = document.getElementById("labeltxt2");
  label2.textContent = "Selecciona TXT dos años antes (" + (parseInt(this.value) - 2).toString() + ")";
}
document.getElementById('csv').onchange = function() {
    var filespan = document.getElementById('file-name1');
    filespan.textContent = 'Archivo seleccionado: ' + this.files[0].name;
    filespan.style.display = 'block';
    document.getElementById("generar_txt_span").style.display = "none";
    document.getElementById("generar_txt_div").style.display = 'block';
};

document.getElementById("txt1").onchange = function () {
  var filespan = document.getElementById("file-name2");
  filespan.textContent = "Archivo seleccionado: " + this.files[0].name;
  filespan.style.display = "block";
};
document.getElementById("txt2").onchange = function () {
  var filespan = document.getElementById("file-name3");
  filespan.textContent = "Archivo seleccionado: " + this.files[0].name;
  filespan.style.display = "block";
};
document.getElementById('rellenarForm').addEventListener('click', function(event) {
  event.preventDefault();
  
  const paragraph = document.getElementById('formularioDeclarante');
  
  if (paragraph.style.display === 'none') {
    paragraph.style.display = 'block';
  } else {
    paragraph.style.display = 'none';
  }
});

document.getElementById('tipoDeclaracion').addEventListener('change', function() {
  declAnterior = document.getElementById("declAnteriorDiv");
  if (this.value === 'XS' || this.value === 'CX'){
    declAnterior.style.display = 'flex';
  }
  else {
    declAnterior.style.display = 'none';
  }
});

// document.getElementById("generate_txt").addEventListener("submit", function (event) {
//   // Enable the bottom button after top button is clicked
//   event.preventDefault();
//   this.submit();
// });


