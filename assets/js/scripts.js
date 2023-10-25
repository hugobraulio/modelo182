document.getElementById('csv').onchange = function() {
    document.getElementById('file-name').textContent = 'Archivo seleccionado: ' + this.files[0].name;
};
document.getElementById("csv").addEventListener("click", function () {
  // Enable the bottom button after top button is clicked
  document.getElementById("generate_txt").disabled = false;
});
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

