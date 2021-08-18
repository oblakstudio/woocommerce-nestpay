function checkCaptcha(e: Event) : void {

  const response = document.querySelector('[name=h-captcha-response]') as HTMLInputElement;

  if (response.value === '') {
    e.preventDefault();
    alert('Please complete the captcha');
  }

}

document.addEventListener('DOMContentLoaded', () => {

  document.getElementById('nestpay-form').addEventListener('submit', evt => checkCaptcha(evt));

});
