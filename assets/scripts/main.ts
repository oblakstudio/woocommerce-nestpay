function checkCaptcha(e: Event): void {
  const active = document.querySelector<HTMLInputElement>('input[name="hcActive"]').value === 'yes';
  const response = document.querySelector<HTMLInputElement>('[name=h-captcha-response]');

  if (!active) {
    return;
  }

  if (response.value === '') {
    e.preventDefault();
    alert('Please complete the captcha');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('nestpay-payment-form') as HTMLFormElement;

  if (form.dataset.autoRedirect === 'yes') {
    form.submit();
    return;
  }

  form.addEventListener('submit', (evt) => checkCaptcha(evt));
});
