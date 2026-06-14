'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const numeralMaskElements = document.querySelectorAll('.numeral-mask');

  const formatNumeral = value => value.replace(/\D/g, '');

  if (numeralMaskElements.length > 0) {
    numeralMaskElements.forEach(numeralMaskEl => {
      numeralMaskEl.addEventListener('input', event => {
        numeralMaskEl.value = formatNumeral(event.target.value);
      });
    });
  }

  (() => {
    const maskWrapper = document.querySelector('.numeral-mask-wrapper');
    const twoStepsForm = document.querySelector('#twoStepsForm');

    if (maskWrapper) {
      Array.from(maskWrapper.children).forEach(pin => {
        pin.addEventListener('keyup', e => {
          if (/^\d$/.test(e.key)) {
            if (pin.nextElementSibling && pin.value.length === parseInt(pin.getAttribute('maxlength'))) {
              pin.nextElementSibling.focus();
            }
          } else if (e.key === 'Backspace') {
            if (pin.previousElementSibling) {
              pin.previousElementSibling.focus();
            }
          }
        });

        pin.addEventListener('keypress', e => {
          if (e.key === '-') {
            e.preventDefault();
          }
        });

        pin.addEventListener('keydown', e => {
          if (e.key === 'Enter') {

            e.preventDefault();

            twoStepsForm.requestSubmit();
          }
        });
      });
    }

    if (twoStepsForm) {
      const numeralMaskList = twoStepsForm.querySelectorAll('.numeral-mask');

      const keyupHandler = () => {
        let otpComplete = true;
        let otpValue = '';

        numeralMaskList.forEach(maskElement => {
          if (maskElement.value === '') {
            otpComplete = false;
          }
          otpValue += maskElement.value;
        });

        twoStepsForm.querySelector('[name="otp"]').value = otpComplete ? otpValue : '';
      };

      numeralMaskList.forEach(maskElement => {
        maskElement.addEventListener('keyup', keyupHandler);
      });

      if (typeof FormValidation !== 'undefined') {
        FormValidation.formValidation(twoStepsForm, {
          fields: {
            otp: {
              validators: {
                notEmpty: {
                  message: 'Please enter OTP'
                }
              }
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: '',
              rowSelector: '.form-control-validation'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            autoFocus: new FormValidation.plugins.AutoFocus()
          }
        });
      }
    }
  })();
});

