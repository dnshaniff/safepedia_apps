'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableAssetTypes = $('.datatables-asset_types'),
    modalAssetType = $('#modalAssetType'),
    modalTitle = modalAssetType.find('.modal-title');

  window.ResourceRegistry = window.ResourceRegistry || {};

  window.ResourceRegistry['asset_types'] = () => {
    dt_asset_types.ajax.reload();
  };

  if (datatableAssetTypes) {
    window.dt_asset_types = new DataTable(datatableAssetTypes, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}asset_types`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'category_name' },
        { data: 'type_name' },
        { data: 'creator' },
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
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.category_code}</span>
                <span class="fw-medium">${data}</span>
              </div>
            `;
          }
        },
        {
          targets: 2,
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.type_code}</span>
                <span class="fw-medium">${data}</span>
              </div>
            `;
          }
        },
        {
          targets: 4,
          render: function (data, type, full, meta) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };
            return new Date(data).toLocaleString('en-GB', options);
          }
        },
        {
          targets: 5,
          render: function (data, type, full, meta) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };
            return new Date(data).toLocaleString('en-GB', options);
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalAssetType" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Type',
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
                    'data-bs-target': '#modalAssetType'
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

  const formAssetType = document.getElementById('formAssetType'),
    categorySelect = formAssetType.querySelector('#asset_category_id'),
    typeName = formAssetType.querySelector('#type_name'),
    typeCode = formAssetType.querySelector('#type_code'),
    btnSubmit = formAssetType.querySelector('button[type="submit"]');

  let editingId = null;

  initDropdownPaged($(categorySelect), {
    url: '/asset_categories/select',
    placeholder: 'Select an option',
    perPage: 10
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Asset Type');
    editingId = null;
    $(btnSubmit).html('Submit');
  });

  // edit record
  $(document).on('click', '.edit-record', function (e) {
    const id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    modalTitle.html('Edit Asset Type');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}asset_types/${id}/edit`, function (data) {
      editingId = id;

      data.category && data.category.id != null
        ? setValue($(categorySelect), { id: data.category.id, text: data.category.category_name })
        : $(categorySelect).val(null).trigger('change');

      typeName.value = data.type_name || '';
      typeCode.value = data.type_code || '';
    });
  });

  FormValidation.formValidation(formAssetType, {
    fields: {
      asset_category_id: {
        validators: {
          notEmpty: {
            message: 'Category must be selected'
          }
        }
      },
      type_name: {
        validators: {
          notEmpty: {
            message: 'Type name is required'
          }
        }
      },
      type_code: {
        validators: {
          notEmpty: {
            message: 'Type code is required'
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
    }
  }).on('core.form.valid', function () {
    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    let url = editingId ? `${baseUrl}asset_types/${editingId}` : `${baseUrl}asset_types`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formAssetType).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_asset_types.draw(false);
        modalAssetType.modal('hide');

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

  modalAssetType.on('hidden.bs.modal', function () {
    formAssetType.reset();
    editingId = null;
    $(formAssetType).find('select').val('').trigger('change');
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
          url: `${baseUrl}asset_types/${id}`,
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
              dt_asset_types.draw(false);
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
          text: 'The type is not deleted!',
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
          url: `${baseUrl}asset_types/${id}/restore`,
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
              dt_asset_types.draw(false);
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
          text: 'The type is not restored!',
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
          url: `${baseUrl}asset_types/${id}/force`,
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
              dt_asset_types.draw(false);
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
          text: 'The type is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
