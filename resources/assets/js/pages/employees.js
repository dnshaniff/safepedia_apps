'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
    // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableEmployees = $('.datatables-employees'),
    modalEmployee = $('#modalEmployee'),
    modalTitle = modalEmployee.find('.modal-title');

  let dt_employees;

  if (datatableEmployees) {
    dt_employees = new DataTable(datatableEmployees, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}employees`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'employee_number' },
        { data: 'full_name' },
        { data: 'position' },
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
          targets: [1, 2, 3]
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

            let createUserButton = '';

            if (!full.hasUser) {
              createUserButton = `
                <button class="btn btn-icon me-2 create-user" data-id="${data}" data-bs-toggle="modal" data-bs-target="#modalUser">
                  <i class="bx bx-user-plus"></i>
                </button>
              `;
            }

            return `
              <span class="text-nowrap">
                ${createUserButton}
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalEmployee" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Employee',
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
                    'data-bs-target': '#modalEmployee'
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

  const formEmployee = document.getElementById('formEmployee'),
    fullName = formEmployee.querySelector('#full_name'),
    fieldPosition = formEmployee.querySelector('#position'),
    btnSubmit = formEmployee.querySelector('button[type="submit"]');

  let editingId = null;

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Employee');
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
    modalTitle.html('Edit Existing Employee');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}employees/${id}/edit`, function (data) {
      editingId = id;
      fullName.value = data.full_name;
      fieldPosition.value = data.position;
    });
  });

  FormValidation.formValidation(formEmployee, {
    fields: {
      full_name: {
        validators: {
          notEmpty: {
            message: 'Name is required'
          }
        }
      },
      position: {
        validators: {
          notEmpty: {
            message: 'Position is required'
          }
        }
      },
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

    // adding/updating when form successfully validate
    let url = editingId ? `${baseUrl}employees/${editingId}` : `${baseUrl}employees`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formEmployee).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_employees.draw(false);
        modalEmployee.modal('hide');

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
  modalEmployee.on('hidden.bs.modal', function () {
    formEmployee.reset();
    editingId = null;
  });

  // delete record
  $(document).on('click', '.delete-record', function () {
    const id = $(this).data('id');

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
      if (result.value) {
        Loading.standard({
          backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
          svgSize: '0px'
        });

        // delete the data
        $.ajax({
          method: 'DELETE',
          url: `${baseUrl}employees/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_employees.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The employee is not deleted!');
      }
    });
  });

  // restore record
  $(document).on('click', '.restore-record', function () {
    var id = $(this).data('id');

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
          url: `${baseUrl}employees/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_employees.draw(false);
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
        showToast('info', 'The employee is not restored!');
      }
    });
  });

  // permanent delete record
  $(document).on('click', '.force-record', function () {
    var id = $(this).data('id');

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
          url: `${baseUrl}employees/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_employees.draw(false);
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
        showToast('info', 'The employee is not deleted!');
      }
    });
  });

  const modalUser = $('#modalUser'),
  modalTitleUser = modalUser.find('.modal-title');

  const formUser = document.getElementById('formUser'),
    userName = formUser.querySelector('#username'),
    roleSelect = formUser.querySelector('#role');

  initDropdownPaged($(roleSelect), {
    url: '/roles/select',
    placeholder: 'Select an option',
    perPage: 10,
    hideSearch: true
  });

  // edit record
  $(document).on('click', '.create-user', function () {
    const id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // get data
    $.get(`${baseUrl}employees/${id}/edit`, function (data) {
      editingId = id;
      modalTitleUser.html(`Create User for ${data.full_name}`);
    });
  });

  FormValidation.formValidation(formUser, {
    fields: {
      username: {
        validators: {
          notEmpty: {
            message: 'Username is required'
          },
          stringLength: {
            min: 4,
            message: 'The username must be at least 4 characters long'
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
      },
      role: {
        validators: {
          notEmpty: {
            message: 'Role must be selected'
          }
        }
      },
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
      data: $(formUser).serialize(),
      url: `${baseUrl}employees/${editingId}/user`,
      type: 'POST',
      success: function (res) {
        Loading.remove();
        dt_employees.draw(false);
        modalUser.modal('hide');

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
  modalUser.on('hidden.bs.modal', function () {
    formUser.reset();
    editingId = null;
    modalTitleUser.html('Create New User');
    $(formUser).find('select').val('').trigger('change');
  });
});
