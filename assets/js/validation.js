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
