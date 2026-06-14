'use strict';

document.addEventListener('DOMContentLoaded', function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const mapElement = document.getElementById('constructionMap');

  if (mapElement && typeof L !== 'undefined') {
    const latitude = parseFloat(mapElement.dataset.latitude);
    const longitude = parseFloat(mapElement.dataset.longitude);
    const warehouseName = mapElement.dataset.name || 'Warehouse Location';

    if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
      const constructionMap = L.map(mapElement).setView([latitude, longitude], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 19
      }).addTo(constructionMap);

      L.marker([latitude, longitude])
        .addTo(constructionMap)
        .bindPopup(warehouseName)
        .openPopup();

      setTimeout(() => {
        constructionMap.invalidateSize();
      }, 300);
    }
  }

  if (typeof Fancybox !== 'undefined') {
    Fancybox.bind('[data-fancybox="construction-documents"]', {
      iframe: {
        preload: false
      }
    });
  }

  const modalApproval = $('#modalApproval'),
    modalTitle = modalApproval.find('.modal-title');

  const formApproval = document.getElementById('formApproval'),
    fieldNotes = formApproval.querySelector('#notes');

  let constructionId = null, approvalId = null, currentAction = null;

  $(document).on('click', '.approve-construction', function () {
    constructionId = $(this).data('id');
    approvalId = $(this).data('approval-id');
    currentAction = 'approved';

    modalTitle.html('Approve Construction');
    fieldNotes.value = '';
    modalApproval.find('label[for="notes"]').html('Notes');
    modalApproval.modal('show');
  });

  $(document).on('click', '.return-construction', function () {
    constructionId = $(this).data('id');
    approvalId = $(this).data('approval-id');
    currentAction = 'returned';

    modalTitle.html('Return Construction');
    fieldNotes.value = '';
    modalApproval.find('label[for="notes"]').html('Notes <span class="text-danger">**</span>');
    modalApproval.modal('show');
  });

  FormValidation.formValidation(formApproval, {
    fields: {
      notes: {
        validators: {
          callback: {
            message: 'Notes is required when returning construction',
            callback: function (input) {
              if (currentAction !== 'returned') {
                return true;
              }

              return input.value.trim() !== '';
            }
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
      url: `/warehouse_constructions/${constructionId}/warehouse_construction_approvals/${approvalId}`,
      data: $(formApproval).serialize() + `&action=${currentAction}`,
      type: 'PATCH',
      success: function (res) {
        Loading.remove();
        modalApproval.modal('hide');
        showToast(res.status, res.message);

        setTimeout(function () {
           window.location.reload();
        }, 1500);
      },
      error: function (xhr) {
        let res = xhr.responseJSON;
        Loading.remove();
        if (res) {
          showToast(res.status, res.message);
          if (res.errors) {
            for (let field in res.errors) {
              res.errors[field].forEach(msg => console.log(`${field}: ${msg}`));
            }
          }
        } else {
          showToast('danger', 'An unexpected error occurred');
        }
      }
    });
  });
});
