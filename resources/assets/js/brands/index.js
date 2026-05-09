'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableBrands = $('.datatables-brands'),
    modalBrand = $('#modalBrand'),
    modalTitle = modalBrand.find('.modal-title');

  let dt_brands;

  if (datatableBrands) {
    dt_brands = new DataTable(datatableBrands, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}brands`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'name' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, -1]
        },
        {
          searchable: true,
          targets: [1]
        },
        {
          targets: 1,
          responsivePriority: 1,
          render: function (data, type, row) {
            let brandName = data;
            let logo = row.file_path ? `${baseUrl}storage/${row.file_path}` : null;

            let logoOutput = logo
            ? `
              <a href="${logo}" data-fancybox="brand-${row.id}">
                <img
                  src="${logo}"
                  alt="${brandName}"
                  class="rounded border"
                  style="width: 42px; height: 42px; object-fit: contain;"
                />
              </a>
            `
            : `
              <div
                class="d-flex align-items-center justify-content-center rounded bg-label-secondary"
                style="
                  width: 42px;
                  height: 42px;
                  font-size: 16px;
                  font-weight: 700;
                "
              >
                ${brandName.charAt(0).toUpperCase()}
              </div>
            `;

            return `
              <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                  ${logoOutput}
                </div>
                <div class="d-flex align-items-center">
                  <span class="fw-medium text-heading mb-0" style=" line-height: 1;" />
                    ${brandName}
                  </span>
                </div>
              </div>
            `;
          }
        },
        {
          targets: 2,
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
          targets: 3,
          render: function (data, type, row) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };

            if (row.deleted_at !== null) {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.deleter}</span>
                  <span class="fw-medium">${new Date(row.deleted_at).toLocaleString('en-GB', options)}</span>
                </div>
              `;
            } else {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.editor}</span>
                  <span class="fw-medium">${new Date(data).toLocaleString('en-GB', options)}</span>
                </div>
              `;
            }
          }
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalBrand" data-bs-toggle="modal" data-bs-dismiss="modal">
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
              search: {
                placeholder: 'Search Name',
                text: '_INPUT_'
              }
            },
            {
              buttons: [
                {
                  text: 'Create New',
                  className: 'add-new btn btn-primary mb-3 mb-md-0',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalBrand'
                  }
                }
              ]
            },
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

  const formBrand = document.getElementById('formBrand'),
    fillName = formBrand.querySelector('#name'),
    uploadLogo = formBrand.querySelector('#file_upload'),
    previewWrapper = formBrand.querySelector('#logoPreviewWrapper'),
    previewImage = formBrand.querySelector('#logoPreview'),
    previewLink = formBrand.querySelector('#logoPreviewLink'),
    btnSubmit = formBrand.querySelector('button[type="submit"]');

  let editingId = null, currentPreview = null, existingPreview = null;

  uploadLogo.addEventListener('change', function (e) {
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
      uploadLogo.value = '';

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
    modalTitle.html('Create New Brand');
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
    modalTitle.html('Edit Existing Brand');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}brands/${id}/edit`, function (data) {
      editingId = id;
      fillName.value = data.name || '';
      existingPreview = data.file_path ? `${baseUrl}storage/${data.file_path}` : null;

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

  FormValidation.formValidation(formBrand, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Brand name is required'
          },
          stringLength: {
            min: 4,
            message: 'Brand name must be at least 4 characters long'
          }
        }
      },
      file_upload: {
        validators: {
          file: {
            extenstion: 'png',
            type: 'image/png',
            maxSize: 4 * 1024 * 1024, // 4MB
            message: 'Please select a valid image file (png) less than 4MB'
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

    const formData = new FormData(formBrand);

    let url = editingId ? `${baseUrl}brands/${editingId}` : `${baseUrl}brands`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: formData,
      url: url,
      type: method,
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();
        dt_brands.draw(false);
        modalBrand.modal('hide');

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
  modalBrand.on('hidden.bs.modal', function () {
    formBrand.reset();
    editingId = null;

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
          url: `${baseUrl}brands/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_brands.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The brand is not deleted!');
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
          url: `${baseUrl}brands/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_brands.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The brand is not restored!');
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
          url: `${baseUrl}brands/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_brands.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The brand is not deleted!');
      }
    });
  });
});
