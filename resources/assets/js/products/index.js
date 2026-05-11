'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableProducts = $('.datatables-products'),
    modalProduct = $('#modalProduct'),
    modalTitle = modalProduct.find('.modal-title');

  let dt_products;

  if (datatableProducts) {
    dt_products = new DataTable(datatableProducts, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}products`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'name' },
        { data: 'brand' },
        { data: 'status' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, -1]
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
            let logo = row.thumbnail ?? null;

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
          targets: 3,
          render: function (data, type, row) {
            const userStatus = data === 'active' ? 'ACTIVE' : 'INACTIVE',
              statusClass = userStatus === 'ACTIVE' ? 'bg-label-success' : 'bg-label-danger';

            return '<span class="badge ' + statusClass + '">' + userStatus + '</span>';
          }
        },
        {
          targets: 4,
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
          targets: 5,
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalProduct" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Product',
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
                    'data-bs-target': '#modalProduct'
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

  const formProduct = document.getElementById('formProduct'),
    fillName = formProduct.querySelector('#name'),
    fillDescription = formProduct.querySelector('#description'),
    selectBrand = formProduct.querySelector('#brand_id'),
    selectStatus = formProduct.querySelector('#status'),
    dropzoneElement = formProduct.querySelector('#productDropzone'),
    btnSubmit = formProduct.querySelector('button[type="submit"]');

  let editingId = null, selectedThumbnail = null, removedImages = [];

  initDropdownPaged($(selectBrand), {
    url: '/brands/select',
    placeholder: 'Select an option',
    perPage: 10,
    hideSearch: false
  });

  initStatic($(selectStatus), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'active', text: 'Active' },
      { id: 'inactive', text: 'Inactive' }
    ]
  });

  Dropzone.autoDiscover = false;

  const previewTemplate = `
  <div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message">
          <span data-dz-errormessage></span>
        </div>
        <div class="thumbnail-badge d-none">
          <span class="badge bg-primary"><i class="bx bxs-star me-1"></i>Thumbnail</span>
        </div>
        <div class="thumbnail-overlay">
          <button type="button" class="btn btn-sm btn-light set-thumbnail">Set As Thumbnail</button>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>
  `;

  const myDropzone = new Dropzone(dropzoneElement, {
    url: '#',
    autoProcessQueue: false,
    maxFiles: 5,
    maxFilesize: 4,
    acceptedFiles: '.png,.jpg,.jpeg,.webp',
    addRemoveLinks: true,
    previewTemplate: previewTemplate
  });

  myDropzone.on('addedfile', function (file) {
    const preview = file.previewElement;
    const button = preview.querySelector('.set-thumbnail');
    const badge = preview.querySelector('.thumbnail-badge');

    if (!editingId && myDropzone.files.length === 1) {
      badge.classList.remove('d-none');
      file.isThumbnail = true;
      selectedThumbnail = file;
    }

    button.addEventListener('click', function () {
      myDropzone.files.forEach(f => {
        f.isThumbnail = false;
        const el = f.previewElement.querySelector('.thumbnail-badge');

        el.classList.add('d-none');
      });

      file.isThumbnail = true;
      selectedThumbnail = file;

      badge.classList.remove('d-none');
    });
  });

  myDropzone.on('removedfile', function (file) {
    if (file.existing && file.id) {
      removedImages.push(file.id);
    }

    file.isThumbnail = false;

    if (file.previewElement) {
      const removedBadge = file.previewElement.querySelector('.thumbnail-badge');

      if (removedBadge) {
        removedBadge.classList.add('d-none');
      }
    }

    if (selectedThumbnail === file) {
      selectedThumbnail = null;

      myDropzone.files.forEach(f => {
        f.isThumbnail = false;

        const badge = f.previewElement?.querySelector('.thumbnail-badge');

        badge?.classList.add('d-none');
      });

      if (myDropzone.files.length > 0) {
        const firstFile = myDropzone.files[0];

        firstFile.isThumbnail = true;

        selectedThumbnail = firstFile;

        firstFile.previewElement.querySelector('.thumbnail-badge').classList.remove('d-none');
      }
    }
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Product');
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
    modalTitle.html('Edit Existing Product');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}products/${id}/edit`, function (data) {
      editingId = id;
      fillName.value = data.name || '';
      fillDescription.value = data.description || '';

      if (data.brand) {
        const option = new Option(data.brand.name, data.brand.id, true, true);

        $(selectBrand).append(option).trigger('change');
      }

      if (data.status) {
        $(selectStatus).val(data.status).trigger('change');
      }

      myDropzone.removeAllFiles(true);

      data.images.forEach((image, index) => {
        const mockFile = {
          name: image.file_name,
          size: image.file_size,
          accepted: true,
          existing: true,
          id: image.id
        };

        myDropzone.emit('addedfile', mockFile);

        myDropzone.emit('thumbnail', mockFile, `/storage/${image.file_path}`);

        myDropzone.emit('complete', mockFile);

        myDropzone.files.push(mockFile);

        mockFile.previewElement.querySelector('.dz-remove').dataset.id = image.id;

        if (image.is_primary) {
          mockFile.isThumbnail = true;
          selectedThumbnail = mockFile;

          mockFile.previewElement.querySelector('.thumbnail-badge').classList.remove('d-none');
        }
      });
    });
  });

  FormValidation.formValidation(formProduct, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Product name is required'
          },
          stringLength: {
            min: 4,
            message: 'Product name must be at least 4 characters long'
          }
        }
      },
      description: {
        validators: {
          notEmpty: {
            message: 'Description is required'
          }
        }
      },
      brand_id: {
        validators: {
          notEmpty: {
            message: 'Brand must be selected'
          }
        }
      },
      status: {
        validators: {
          notEmpty: {
            message: 'Status must be selected'
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
    const formData = new FormData(formProduct);

    let url = editingId ? `${baseUrl}products/${editingId}` : `${baseUrl}products`;

    if (editingId) {
      formData.append('_method', 'PATCH');
    }

    removedImages.forEach((id, index) => {
      formData.append(`removed_images[${index}]`, id);
    });

    const activeFiles = myDropzone.files;

    if (activeFiles.length === 0) {
      showToast('info', 'At least one product image is required');

      return;
    }

    activeFiles.forEach((file, index) => {
      if (!file.existing) {
        formData.append(`images[${index}]`, file);
      }

      if (selectedThumbnail === file) {
        formData.append('thumbnail_index', index);
      }
    });

    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    $.ajax({
      data: formData,
      url: url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();
        dt_products.draw(false);
        modalProduct.modal('hide');

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

  modalProduct.on('hidden.bs.modal', function () {
    formProduct.reset();
    editingId = null
    $(formProduct).find('select').val('').trigger('change');

    myDropzone.removeAllFiles(true);
    myDropzone.files = [];
    $(dropzoneElement).find('.dz-preview').remove();
    selectedThumbnail = null;
    removedImages = [];
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
          url: `${baseUrl}products/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_products.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The product is not deleted!');
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
          url: `${baseUrl}products/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_products.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The product is not restored!');
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
          url: `${baseUrl}products/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_products.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The product is not deleted!');
      }
    });
  });
});
