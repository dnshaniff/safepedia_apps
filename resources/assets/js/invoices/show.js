'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const currentPath = `${window.location.pathname}/`;

  const datatablePayments = $('.datatables-payments'),
    modalPayment = $('#modalPayment'),
    modalTitle = modalPayment.find('.modal-title');

  let dt_payments;

  function formatCurrency(value) {
    const number = Number(value) || 0;

    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number);
  }

  if (datatablePayments) {
    dt_payments = new DataTable(datatablePayments, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${currentPath}invoice_payments`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'payment_date' },
        { data: 'amount' },
        { data: 'payment_method' },
        { data: 'file_path' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          searchable: false,
          targets: [0, 1, 2, 3, 4, 5, 6, -1]
        },
        {
          targets: 2,
          render: function (data, type, row) {
            if (type === 'sort' || type === 'type') {
              return Number(data) || 0;
            }

            return formatCurrency(data);
          }
        },
        {
          targets: 3,
          render: function (data, type, row) {
            const methods = {
              cash: 'Cash',
              bank_transfer: 'Bank Transfer'
            };

            return methods[data] || '-';
          }
        },
        {
          targets: 4,
          render: function (data, type, row) {
            if (!data) {
              return '-';
            }

            return `
              <a href="${data}" data-fancybox="file-${row.id}">
                <img
                  src="${data}"
                  alt="${row.file_name}"
                  class="rounded border"
                  style="width: 42px; height: 42px; object-fit: contain;"
                />
              </a>
            `;
          }
        },
        {
          targets: 5,
          render: function (data, type, row) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };

            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.creator}</span>
                <span class="fw-medium">${new Date(data).toLocaleString('en-GB', options)}</span>
              </div>
            `;
          }
        },
        {
          targets: 6,
          render: function (data, type, row) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };

            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.editor}</span>
                <span class="fw-medium">${new Date(data).toLocaleString('en-GB', options)}</span>
              </div>
            `;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          render: function (data, type, full, meta) {
            return `
              <span class="text-nowrap">
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalPayment" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                    'data-bs-target': '#modalPayment'
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

  const formPayment = document.getElementById('formPayment'),
    paymentDate = formPayment.querySelector('#payment_date'),
    fillAmount = formPayment.querySelector('#amount'),
    paymentMethod = formPayment.querySelector('#payment_method'),
    uploadFile = formPayment.querySelector('#file_upload'),
    previewWrapper = formPayment.querySelector('#uploadPreviewWrapper'),
    previewImage = formPayment.querySelector('#uploadPreview'),
    previewLink = formPayment.querySelector('#uploadPreviewLink'),
    btnSubmit = formPayment.querySelector('button[type="submit"]');

  let editingId = null,
    currentPreview = null,
    existingPreview = null;

  function initFP(element) {
    if (!element) return;

    flatpickr(element, {
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d',
      static: true,
      allowInput: false
    });
  }

  initFP(paymentDate);

  function formatAmount(value) {
    const cleanValue = String(value).replace(/\D/g, '');

    if (!cleanValue) return '';

    return cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  if (fillAmount) {
    fillAmount.addEventListener('input', event => {
      fillAmount.value = formatAmount(event.target.value);
    });

    registerCursorTracker({
      input: fillAmount,
      delimiter: '.'
    });
  }

  initStatic($(paymentMethod), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'bank_transfer', text: 'Bank Transfer' },
      { id: 'cash', text: 'Cash' }
    ]
  });

  uploadFile.addEventListener('change', function (e) {
    const file = e.target.files[0];

    if (!file) {
      if (existingPreview) {
        previewImage.src = existingPreview;
        previewLink.href = existingPreview;
        previewWrapper.classList.remove('d-none');
      }

      return;
    }

    if (!file.type.startsWith('image/')) {
      showToast('danger', 'Only image files are allowed');
      uploadFile.value = '';

      return;
    }

    if (currentPreview) {
      URL.revokeObjectURL(currentPreview);
    }

    currentPreview = URL.createObjectURL(file);
    previewImage.src = currentPreview;
    previewLink.href = currentPreview;
    previewWrapper.classList.remove('d-none');
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Payment');
    editingId = null;
    $(btnSubmit).html('Submit');
  });

  // edit record
  $(document).on('click', '.edit-record', function () {
    const id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // changing the title of modal
    modalTitle.html('Edit Existing Invoice');
    $(btnSubmit).html('Save');

    $.get(`${currentPath}invoice_payments/${id}/edit`, function (data) {
      editingId = id;

      paymentDate._flatpickr.setDate(data.payment_date || null);

      fillAmount.value = data.amount ? Math.round(Number(data.amount)).toLocaleString('id-ID') : '';

      if (data.payment_method) {
        $(paymentMethod).val(data.payment_method).trigger('change');
      }

      existingPreview = data.file_path || null;

      if (existingPreview) {
        previewImage.src = existingPreview;
        previewLink.href = existingPreview;
        previewWrapper.classList.remove('d-none');
      } else {
        previewImage.src = '';
        previewLink.href = '';
        previewWrapper.classList.add('d-none');
      }
    });
  });

  FormValidation.formValidation(formPayment, {
    fields: {
      payment_date: {
        validators: {
          notEmpty: {
            message: 'Date must be selected'
          }
        }
      },
      amount: {
        validators: {
          notEmpty: {
            message: 'Amount is required'
          }
        }
      },
      payment_method: {
        validators: {
          notEmpty: {
            message: 'Payment method must be selected'
          }
        }
      },
      file_upload: {
        validators: {
          callback: {
            message: 'Evidence is required',
            callback: function (input) {
              if (editingId) {
                return true;
              }

              return input.value.length > 0;
            }
          },
          file: {
            extension: 'jpg,jpeg,png',
            type: 'image/jpeg,image/png',
            maxSize: 1 * 1024 * 1024, // 1MB
            message: 'Please select a valid image file (jpg,jpeg,png) less than 1MB'
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

    const formData = new FormData(formPayment);
    if (editingId) {
      formData.append('_method', 'PATCH');
    }

    let url = editingId ? `${currentPath}invoice_payments/${editingId}` : `${currentPath}invoice_payments`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: formData,
      url: url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (res) {
        $('#paid-value').text(formatCurrency(res.invoice.paid_amount));

        $('#balance-value').text(formatCurrency(res.invoice.remaining_amount));

        Loading.remove();
        dt_payments.draw(false);
        modalPayment.modal('hide');

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
  modalPayment.on('hidden.bs.modal', function () {
    formPayment.reset();
    editingId = null;
    paymentDate._flatpickr.clear(false);
    $(formPayment).find('select').val('').trigger('change');

    if (currentPreview) {
      URL.revokeObjectURL(currentPreview);
    }

    previewImage.src = '';
    previewLink.href = '';
    previewWrapper.classList.add('d-none');
    currentPreview = null;
    existingPreview = null;
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
          url: `${currentPath}invoice_payments/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_payments.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(
              jqXHR.responseJSON?.status || 'danger',
              jqXHR.responseJSON?.message || 'An unexpected error occurred'
            );
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The payment is not deleted!');
      }
    });
  });
});
