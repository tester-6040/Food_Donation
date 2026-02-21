document.querySelectorAll('form[data-validate]').forEach((form) => {
  form.addEventListener('submit', (e) => {
    const invalid = [...form.querySelectorAll('[required]')].find((field) => !String(field.value || '').trim());
    if (invalid) {
      e.preventDefault();
      alert('Please fill all required fields.');
      invalid.focus();
    }
  });
});

document.querySelectorAll('[data-fill-location]').forEach((button) => {
  button.addEventListener('click', () => {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported in this browser.');
      return;
    }

    const latSelector = button.getAttribute('data-lat-target');
    const lngSelector = button.getAttribute('data-lng-target');
    const latInput = latSelector ? document.querySelector(latSelector) : null;
    const lngInput = lngSelector ? document.querySelector(lngSelector) : null;

    if (!latInput || !lngInput) {
      alert('Latitude/Longitude fields not found.');
      return;
    }

    const previousText = button.textContent;
    button.disabled = true;
    button.textContent = 'Fetching location...';

    navigator.geolocation.getCurrentPosition(
      (position) => {
        latInput.value = position.coords.latitude.toFixed(7);
        lngInput.value = position.coords.longitude.toFixed(7);
        button.textContent = 'Location filled';
        setTimeout(() => {
          button.disabled = false;
          button.textContent = previousText;
        }, 1200);
      },
      (error) => {
        alert('Unable to fetch location: ' + error.message);
        button.disabled = false;
        button.textContent = previousText;
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  });
});
