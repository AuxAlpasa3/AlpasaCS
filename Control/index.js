const vehiculo = document.getElementById('vehiculo');

function handleRadioClick() {
  if (document.getElementById('show').checked) {
    vehiculo.style.display = 'block';
  } else {
    vehiculo.style.display = 'none';
  }
}

const radioButtons = document.querySelectorAll(
  'input[name="snvehiculo"]',
);
radioButtons.forEach(radio => {
  radio.addEventListener('click', handleRadioClick);
});