'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const formProfile = document.getElementById('formProfile');

  FormValidation.formValidation(formProfile, {
    fields: {
      username: {
        validators: {
          notEmpty: {
            message: 'Please enter an username'
          },
          stringLength: {
            min: 4,
            message: 'The username must be at least 4 characters long'
          }
        }
      },
      personal_email: {
        validators: {
          notEmpty: {
            message: 'Please enter personal email'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      password: {
        validators: {
          stringLength: {
            min: 8,
            message: 'The password must be at least 8 characters long'
          },
          regexp: {
            regexp: /^(?=.*[a-z])(?=.*[A-Z]).+$/,
            message: 'The password must contain at least one uppercase letter and one lowercase letter'
          }
        }
      },
      password_confirmation: {
        validators: {
          identical: {
            compare: () => formUser.querySelector('[name="password"]').value,
            message: 'The password and its confirmation do not match'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: '.mb-3'
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    },
    init: instance => {
      instance.on('plugins.message.placed', e => {
        if (e.element.parentElement.classList.contains('input-group')) {
          e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
        }
      });
    }
  }).on('core.form.valid', function () {
    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    $.ajax({
      data: $(formProfile).serialize(),
      url: window.location.pathname,
      type: 'PATCH',
      success: function (res) {
        Loading.remove();

        showToast(res.status, res.message);

        setTimeout(function () {
          if (res.redirect) {
            window.location.href = res.redirect;
          } else {
            window.location.reload();
          }
        }, 1500);
      },
      error: function (xhr, status, error) {
        let res = xhr.responseJSON;
        if (res) {
          Loading.remove();
          showToast(res.status, res.message);
          if (res.errors) {
            for (let field in res.errors) {
              res.errors[field].forEach(errorMessage => {
                console.log(`${field}: ${errorMessage}`);
              });
            }
          }
        } else {
          Loading.remove();
          showToast('danger', 'An unexpected error occurred');
        }
      }
    });
  });
});
