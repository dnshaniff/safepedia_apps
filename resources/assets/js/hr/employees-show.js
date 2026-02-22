'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableAgreements = $('.datatables-agreements'),
    modalAgreement = $('#modalAgreement'),
    modalTitle = modalAgreement.find('.modal-title'),
    currentPath = window.location.pathname;

  let dt_agreements;

  if (datatableAgreements) {
    dt_agreements = datatableAgreements.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: `${currentPath}/employee_agreements`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'agreement_type' },
        { data: 'date' },
        { data: 'notes' },
        { data: 'creator' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          searchable: false,
          targets: [0, 1, 2, 3, 4, -1]
        },
        {
          targets: -1,
          title: 'Actions',
          render: function (data, type, full, meta) {
            if (full.deleted_at !== null) {
              return `
                <span class="text-nowrap">
                  <button class="btn btn-icon me-2 restore-record" data-id="${data}">
                    <i class="bx bx-recycle"></i>
                  </button>
                  <button class="btn btn-icon force-record" data-id="${data}">
                    <i class="bx bx-trash"></i>
                  </button>
                </span>
              `;
            }

            return `
              <span class="text-nowrap">
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalAgreement" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-icon delete-record" data-id="${data}">
                  <i class="bx bx-trash-alt"></i>
                </button>
              </span>
            `;
          }
        }
      ],
      scrollCollapse: true,
      fixedHeader: { header: true, headerOffset: 70 },
      fixedColumns: { leftColumns: 1 },
      order: [[]],
      layout: {
        topStart: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: 'Show_MENU_ entries'
              }
            }
          ]
        },
        topEnd: {
          features: [
            {
              buttons: [
                {
                  text: 'Create New',
                  className: 'add-new btn btn-primary mb-3 mb-md-0',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalAgreement'
                  }
                }
              ]
            }
          ]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',
          first: '<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',
          last: '<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'
        }
      },
      createdRow: function (row, data) {
        if (data.deleted_at !== null) {
          $(row).addClass('bg-danger-subtle');
        }
      }
    });
  }

  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-search', classToAdd: 'me-3' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length', classToAdd: 'mb-0 mb-md-5' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-buttons', classToAdd: 'mb-0 w-auto' },
      { selector: '.dt-layout-start', classToAdd: 'mt-0 px-5' },
      {
        selector: '.dt-layout-end',
        classToAdd: 'justify-content-md-between justify-content-center d-flex',
        classToRemove: 'justify-content-between d-md-flex'
      },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
    ];

    // Delete record
    elementsToModify.forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(element => {
        if (classToRemove) {
          classToRemove.split(' ').forEach(className => element.classList.remove(className));
        }
        if (classToAdd) {
          classToAdd.split(' ').forEach(className => element.classList.add(className));
        }
      });
    });
  }, 100);

  const formAgreement = document.getElementById('formAgreement'),
    typeSelect = formAgreement.querySelector('#agreement_type'),
    startDate = formAgreement.querySelector('#start_date'),
    endDate = formAgreement.querySelector('#end_date'),
    effectiveDate = formAgreement.querySelector('#effective_date'),
    notesInput = formAgreement.querySelector('#notes'),
    btnSubmit = formAgreement.querySelector('button[type="submit"]');

  let editingId = null;
  const PERIOD_TYPES = new Set(['Contract', 'Extension']);

  function initFP(el) {
    if (!el) return;
    if (el._flatpickr) return;

    flatpickr(el, {
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d',
      static: true,
      allowInput: false
    });
  }

  initStatic($(typeSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'Contract', text: 'Contract' },
      { id: 'Conversion', text: 'Conversion' },
      { id: 'Extension', text: 'Extension' },
      { id: 'Promotion', text: 'Promotion' },
      { id: 'Resignation', text: 'Resignation' },
      { id: 'Warning', text: 'Warning' }
    ]
  });

  function setFPEnabled(el, enabled) {
    if (!el) return;

    initFP(el);

    const fp = el._flatpickr;
    const visible = fp?.altInput || el;

    if (!fp) return;

    if (enabled) {
      el.disabled = false;
      el.readOnly = false;
      visible.disabled = false;
      visible.readOnly = false;
    } else {
      fp.clear();
      el.value = '';
      visible.value = '';

      el.disabled = true;
      el.readOnly = true;
      visible.disabled = true;
      visible.readOnly = true;
    }
  }

  initFP(startDate);
  initFP(endDate);
  initFP(effectiveDate);

  function applyAgreementTypeRules(type) {
    type = (type || '').trim();

    if (type === '') {
      setFPEnabled(startDate, false);
      setFPEnabled(endDate, false);
      setFPEnabled(effectiveDate, false);
      return;
    }

    const isPeriod = PERIOD_TYPES.has(type);

    setFPEnabled(startDate, isPeriod);
    setFPEnabled(endDate, isPeriod);
    setFPEnabled(effectiveDate, !isPeriod);
  }

  $(typeSelect).on('change select2:select select2:clear', function () {
    applyAgreementTypeRules($(this).val());
  });

  applyAgreementTypeRules($(typeSelect).val());

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Agreement');
    editingId = null;
    $(btnSubmit).html('Submit');
  });

  // edit record
  $(document).on('click', '.edit-record', function () {
    const id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    modalTitle.html('Edit Agreements');
    $(btnSubmit).html('Save');

    $.get(`${currentPath}/employee_agreements/${id}/edit`, function (data) {
      editingId = id;

      $(typeSelect).val(data.agreement_type).trigger('change');
      startDate._flatpickr.setDate(data.start_date || null);
      endDate._flatpickr.setDate(data.end_date || null);
      effectiveDate._flatpickr.setDate(data.effective_date || null);
      notesInput.value = data.notes || '';
    });
  });

  FormValidation.formValidation(formAgreement, {
    fields: {
      agreement_type: {
        validators: {
          notEmpty: {
            message: 'Type must be selected'
          }
        }
      },
      start_date: {
        validators: {
          callback: {
            message: 'Start date is required for this type',
            callback: function (input) {
              const type = ($(typeSelect).val() || '').trim();
              const val = (input.value || '').trim();

              if (type === '') return true;

              if (PERIOD_TYPES.has(type)) {
                return val !== '';
              }

              return true;
            }
          }
        }
      },

      end_date: {
        validators: {
          callback: {
            message: 'End date is required for this type',
            callback: function (input) {
              const type = ($(typeSelect).val() || '').trim();
              const val = (input.value || '').trim();

              if (type === '') return true;

              if (PERIOD_TYPES.has(type)) {
                return val !== '';
              }

              return true;
            }
          }
        }
      },
      effective_date: {
        validators: {
          callback: {
            message: 'Effective date is required for this type',
            callback: function (input) {
              const type = ($(typeSelect).val() || '').trim();
              const val = (input.value || '').trim();

              if (type === '') return true;

              if (PERIOD_TYPES.has(type)) {
                return true;
              }

              return val !== '';
            }
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    let url = editingId ? `${currentPath}/employee_agreements/${editingId}` : `${currentPath}/employee_agreements`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formAgreement).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_agreements.draw(false);
        modalAgreement.modal('hide');

        showToast(res.status, res.message);
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

  // clearing form data when modal hidden
  modalAgreement.on('hidden.bs.modal', function () {
    formAgreement.reset();
    $(formAgreement).find('select').val(null).trigger('change');
    editingId = null;

    startDate._flatpickr.clear(false);
    endDate._flatpickr.clear(false);
    effectiveDate._flatpickr.clear(false);
  });

  // delete record
  $(document).on('click', '.delete-record', function () {
    var id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // sweetalert for confirmation of delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      Loading.circle({
        backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
        svgSize: '60px',
        svgColor: config.colors.white
      });

      if (result.value) {
        // delete the data
        $.ajax({
          method: 'DELETE',
          url: `${currentPath}/employee_agreements/${id}`,
          success: function (res) {
            Loading.remove();
            if (res.message) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: res.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
              dt_employees.draw(false);
            } else if (res.errors) {
              console.log(res.errors);
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: res.error,
                customClass: {
                  confirmButton: 'btn btn-danger'
                }
              });
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            Loading.remove();
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: jqXHR.responseJSON.error,
              customClass: {
                confirmButton: 'btn btn-danger'
              }
            });
          }
        });
      } else {
        Loading.remove();
        Swal.fire({
          title: 'Cancelled',
          text: 'The agreement is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // restore record
  $(document).on('click', '.restore-record', function () {
    var id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // sweetalert for confirmation of restore
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, restore it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      Loading.circle({
        backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
        svgSize: '60px',
        svgColor: config.colors.white
      });

      if (result.value) {
        // restore the data
        $.ajax({
          method: 'POST',
          url: `${currentPath}/employee_agreements/${id}/restore`,
          success: function (res) {
            Loading.remove();
            if (res.message) {
              Swal.fire({
                icon: 'success',
                title: 'Restored!',
                text: res.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
              dt_employees.draw(false);
            } else if (res.errors) {
              console.log(res.errors);
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: res.error,
                customClass: {
                  confirmButton: 'btn btn-danger'
                }
              });
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            Loading.remove();
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: jqXHR.responseJSON.error,
              customClass: {
                confirmButton: 'btn btn-danger'
              }
            });
          }
        });
      } else {
        Loading.remove();
        Swal.fire({
          title: 'Cancelled',
          text: 'The agreement is not restored!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // permanent delete record
  $(document).on('click', '.force-record', function () {
    var id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // sweetalert for confirmation of permanent delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, permanent delete!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      Loading.circle({
        backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
        svgSize: '60px',
        svgColor: config.colors.white
      });

      if (result.value) {
        // permanent delete the data
        $.ajax({
          method: 'DELETE',
          url: `${currentPath}/employee_agreements/${id}/force`,
          success: function (res) {
            Loading.remove();
            if (res.message) {
              Swal.fire({
                icon: 'success',
                title: 'Permanent Deleted!',
                text: res.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
              dt_employees.draw(false);
            } else if (res.errors) {
              console.log(res.errors);
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: res.error,
                customClass: {
                  confirmButton: 'btn btn-danger'
                }
              });
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            Loading.remove();
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: jqXHR.responseJSON.error,
              customClass: {
                confirmButton: 'btn btn-danger'
              }
            });
          }
        });
      } else {
        Loading.remove();
        Swal.fire({
          title: 'Cancelled',
          text: 'The agreement is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
