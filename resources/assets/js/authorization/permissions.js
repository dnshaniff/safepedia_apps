'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatablePermissions = $('.datatables-permissions'),
    modalPermission = $('#modalPermission'),
    modalTitle = modalPermission.find('.modal-title');

  let dt_permissions, permissionGroupSelect;

  if (datatablePermissions) {
    const filterGroup = document.createElement('div');
    filterGroup.classList.add('permission_group', 'w-px-200', 'pb-3', 'pb-sm-0', 'me-3');
    dt_permissions = new DataTable(datatablePermissions, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}permissions`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'display_name' },
        { data: 'name' },
        { data: 'group_name' },
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
            return `
              <span class="text-nowrap">
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalPermission" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-icon delete-record" data-id="${data}">
                  <i class="bx bx-trash"></i>
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
                placeholder: 'Search Permission',
                text: '_INPUT_'
              }
            },
            filterGroup,
            {
              buttons: [
                {
                  text: 'Create New',
                  className: 'add-new btn btn-primary mb-3 mb-md-0',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalPermission'
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
      initComplete: function (settings, json) {
        permissionGroupSelect = $(
          '<select id="permissionGroup" class="form-select text-capitalize"><option value=""> Select Group </option></select>'
        )
          .appendTo('.permission_group')
          .on('change', function () {
            const val = $(this).val();
            dt_permissions.column(3).search(val).draw();
          });

        updatePermissionGroupOptions(json.groups);
      }
    });
  }

  function updatePermissionGroupOptions(groups) {
    if (!permissionGroupSelect) return; // Guard clause
    const currentVal = permissionGroupSelect.val();
    permissionGroupSelect.empty().append('<option value=""> Select Group </option>');
    groups.forEach(function (group) {
      permissionGroupSelect.append('<option value="' + group + '" class="text-capitalize">' + group + '</option>');
    });
    permissionGroupSelect.val(currentVal);
  }

  dt_permissions.on('draw.dt', function () {
    const json = dt_permissions.ajax.json();
    if (json && json.groups) {
      updatePermissionGroupOptions(json.groups);
    }
  });

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

  const formPermission = document.getElementById('formPermission'),
    namePermission = formPermission.querySelector('#display_name'),
    routePermission = formPermission.querySelector('#name'),
    groupPermission = formPermission.querySelector('#group_name'),
    btnSubmit = formPermission.querySelector('button[type="submit"]');

  let editingId = null;

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Permission');
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
    modalTitle.html('Edit Permission');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}permissions/${id}/edit`, function (data) {
      editingId = id;
      namePermission.value = data.display_name;
      routePermission.value = data.name;
      groupPermission.value = data.group_name;
    });
  });

  FormValidation.formValidation(formPermission, {
    fields: {
      display_name: {
        validators: {
          notEmpty: {
            message: 'permission name is required'
          }
        }
      },
      name: {
        validators: {
          notEmpty: {
            message: 'permission route is required'
          }
        }
      },
      group_name: {
        validators: {
          notEmpty: {
            message: 'Permission group is required'
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

    // adding/updating when form successfully validate
    let url = editingId ? `${baseUrl}permissions/${editingId}` : `${baseUrl}permissions`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formPermission).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_permissions.draw(false);
        modalPermission.modal('hide');

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
  modalPermission.on('hidden.bs.modal', function () {
    formPermission.reset();
  });

  // delete record
  $(document).on('click', '.delete-record', function () {
    const id = $(this).data('id'),
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
      if (result.value) {
        Loading.standard({
          backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
          svgSize: '0px'
        });

        // delete the data
        $.ajax({
          method: 'DELETE',
          url: `${baseUrl}permissions/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_permissions.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The permission is not deleted!');
      }
    });
  });
});
