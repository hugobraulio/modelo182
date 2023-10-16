document.getElementById('uploadForm').addEventListener('submit', function(event) {
  event.preventDefault();
  const formData = new FormData(event.target);
  fetch('upload.php', { method: 'POST', body: formData })
    .then(response => response.text())
    .then(data => {
      // Trigger download
      const blob = new Blob([data], { type: 'text/plain' });
      const link = document.createElement('a');
      link.href = window.URL.createObjectURL(blob);
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hour = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');

      const formattedDate = `${year}_${month}_${day}_${hour}${minutes}${seconds}`;

      link.download = `modelo182_${formattedDate}.txt`;
      link.click();
      // Display success message
      document.getElementById('message').textContent = 'Fichero generado y descargado con éxito :-)';
    });
});
document.getElementById('csv').onchange = function() {
    document.getElementById('file-name').textContent = 'Archivo seleccionado: ' + this.files[0].name;
};
document.getElementById("csv").addEventListener("click", function () {
  // Enable the bottom button after top button is clicked
  document.getElementById("generate_txt").disabled = false;
});
document.getElementById('instructionsLink').addEventListener('click', function(event) {
  event.preventDefault();
  
  const paragraph = document.getElementById('instructionsParagraph');
  
  if (paragraph.style.display === 'none') {
    paragraph.style.display = 'block';
  } else {
    paragraph.style.display = 'none';
  }
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
  if (this.value === 'XS'){
    declAnterior.style.display = 'flex';
  }
  else {
    declAnterior.style.display = 'none';
  }
});

