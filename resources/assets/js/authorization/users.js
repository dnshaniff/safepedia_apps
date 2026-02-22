'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableUsers = $('.datatables-users'),
    modalUser = $('#modalUser'),
    modalTitle = modalUser.find('.modal-title');

  let dt_users, userRoleSelect;

  if (datatableUsers) {
    const filterRole = document.createElement('div');
    filterRole.classList.add('user_roles', 'w-px-200', 'pb-3', 'pb-sm-0', 'me-3');
    dt_users = new DataTable(datatableUsers, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}users`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'username' },
        { data: 'full_name' },
        { data: 'role' },
        { data: 'status' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, 6, -1]
        },
        {
          searchable: true,
          targets: [1, 2]
        },
        {
          targets: 4,
          render: function (data, type, full, meta) {
            const userStatus = data === 'active' ? 'ACTIVE' : 'INACTIVE',
              statusClass = userStatus === 'ACTIVE' ? 'bg-label-success' : 'bg-label-danger';

            return '<span class="badge ' + statusClass + '">' + userStatus + '</span>';
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
          targets: 6,
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalUser" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Username',
                text: '_INPUT_'
              }
            },
            filterRole
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
      initComplete: function (settings, json) {
        userRoleSelect = $(
          '<select id="userRole" class="form-select text-capitalize"><option value=""> Select Group </option></select>'
        )
          .appendTo('.user_roles')
          .on('change', function () {
            var val = $(this).val();
            dt_users.column(2).search(val).draw();
          });

        updateUserRoleOptions(json.roles);
      },
      createdRow: function (row, data) {
        if (data.deleted_at !== null) {
          $(row).addClass('bg-danger-subtle');
        }
      }
    });

    function updateUserRoleOptions(roles) {
      if (!userRoleSelect) return; // Guard clause
      var currentVal = userRoleSelect.val();
      userRoleSelect.empty().append('<option value=""> Select Role </option>');
      roles.forEach(function (role) {
        userRoleSelect.append('<option value="' + role + '" class="text-capitalize">' + role + '</option>');
      });
      userRoleSelect.val(currentVal);
    }

    dt_users.on('draw.dt', function () {
      var json = dt_users.ajax.json();
      if (json && json.roles) {
        updateUserRoleOptions(json.roles);
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

  const formUser = document.getElementById('formUser'),
    userName = formUser.querySelector('#username'),
    roleSelect = formUser.querySelector('#role'),
    statusSelect = formUser.querySelector('#status'),
    btnSubmit = formUser.querySelector('button[type="submit"]');

  let editingId = null;

  initStatic($(statusSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'active', text: 'Active' },
      { id: 'inactive', text: 'Inactive' }
    ]
  });

  initDropdownPaged($(roleSelect), {
    url: '/roles/select',
    placeholder: 'Select an option',
    perPage: 10,
    hideSearch: true
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
    modalTitle.html('Edit User');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}users/${id}/edit`, function (data) {
      editingId = id;
      userName.value = data.username || '';

      if (data.status) {
        $(statusSelect).val(data.status).trigger('change');
      }

      const role = Array.isArray(data.roles) && data.roles.length ? data.roles[0] : null;
      if (role && role.id != null) {
        setValue($(roleSelect), { id: role.name, text: role.name });
      }
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
    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    let url = editingId ? `${baseUrl}users/${editingId}` : `${baseUrl}users`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formUser).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_users.draw(false);
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
    $(formUser).find('select').val('').trigger('change');
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
          url: `${baseUrl}users/${id}`,
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
              dt_users.draw(false);
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
          text: 'The User is not deleted!',
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
          url: `${baseUrl}users/${id}/restore`,
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
              dt_users.draw(false);
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
          text: 'The User is not restored!',
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
          url: `${baseUrl}users/${id}/force`,
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
              dt_users.draw(false);
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
          text: 'The User is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
