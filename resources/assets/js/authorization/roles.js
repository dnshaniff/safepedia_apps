'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableRoles = $('.datatables-roles'),
    modalRole = $('#modalRole'),
    modalTitle = modalRole.find('.modal-title');

  let dt_roles;

  if (datatableRoles) {
    dt_roles = new DataTable(datatableRoles, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}roles`
      },
      columns: [{ data: 'fake_id' }, { data: 'name' }, { data: 'created_at' }, { data: 'updated_at' }, { data: 'id' }],
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
          targets: 2,
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
          targets: 3,
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalRole" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Role',
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
                    'data-bs-target': '#modalRole'
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

    clearClassesTable();
  }

  const datatablePermissions = $('.datatables-permissions');
  let dt_permissions;
  let selectedPermission = new Set();

  function initialDTPermissions() {
    if ($.fn.dataTable.isDataTable(datatablePermissions)) {
      datatablePermissions.DataTable().destroy();
    }

    if (datatablePermissions) {
      dt_permissions = new DataTable(datatablePermissions, {
        processing: true,
        serverSide: true,
        rowId: 'name',
        ajax: {
          url: `${baseUrl}permissions`,
          data: function (d) {
            d.groupColumn = 1;
          }
        },
        columns: [
          { data: 'name', orderable: false, searchable: false, render: DataTable.render.select() },
          { data: 'group_name' },
          { data: 'display_name' },
          { data: 'name' }
        ],
        columnDefs: [{ orderable: false, searchable: true, targets: [1, 2, 3] }],
        scrollCollapse: true,
        fixedHeader: { header: true, headerOffset: 70 },
        fixedColumns: { leftColumns: 1 },
        order: [[]],
        select: {
          style: 'multi',
          selector: 'td:first-child'
        },
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
              { search: { placeholder: 'Search Permission', text: '_INPUT_' } },
              (() => {
                const div = document.createElement('div');
                div.className = 'permission_group w-px-200 pb-3 pb-sm-0 me-3';
                return div;
              })()
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
          const container = $('.permission_group');
          if (container.find('#permissionGroup').length) return;

          const select = $(
            '<select id="permissionGroup" class="form-select text-capitalize"><option value="">Select Group</option></select>'
          )
            .appendTo(container)
            .on('change', function () {
              const val = $(this).val();
              dt_permissions.column(1).search(val).draw();
            });

          json.groups.forEach(function (group) {
            select.append('<option value="' + group + '">' + group + '</option>');
          });
        }
      });

      dt_permissions.on('select', (e, dt, type, indexes) => {
        if (type !== 'row') return;
        dt.rows(indexes).every(function () {
          selectedPermission.add(this.id());
        });
      });
      dt_permissions.on('deselect', (e, dt, type, indexes) => {
        if (type !== 'row') return;
        dt.rows(indexes).every(function () {
          selectedPermission.delete(this.id());
        });
      });

      dt_permissions.on('draw', () => {
        dt_permissions.rows().every(function () {
          selectedPermission.has(this.id()) ? this.select() : this.deselect();
        });
      });
    }

    clearClassesTable();
  }

  const formRole = document.getElementById('formRole'),
    nameRole = formRole.querySelector('#name'),
    btnSubmit = formRole.querySelector('button[type="submit"]');

  let editingId = null;

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Role');
    editingId = null;
    initialDTPermissions();
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

    initialDTPermissions();

    modalTitle.html('Edit Role');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}roles/${id}/edit`, function (data) {
      editingId = id;
      nameRole.value = data.name;

      data.permissions.forEach(pid => selectedPermission.add(String(pid)));
      initialDTPermissions();
    });
  });

  FormValidation.formValidation(formRole, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter a role name'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
          return '.mb-4';
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
    let url = editingId ? `${baseUrl}roles/${editingId}` : `${baseUrl}roles`;
    let method = editingId ? 'PATCH' : 'POST';

    // Clear previous hidden inputs
    formRole.querySelectorAll('input[type="hidden"][name="permissions[]"]').forEach(e => e.remove());

    // Add selectedPermission to the form
    selectedPermission.forEach(permission => {
      formRole.insertAdjacentHTML('beforeend', `<input type="hidden" name="permissions[]" value="${permission}">`);
    });

    $.ajax({
      data: $(formRole).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_roles.draw(false);
        modalRole.modal('hide');

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

  modalRole.on('hidden.bs.modal', function () {
    formRole.reset();
    selectedPermission.clear();
    dt_permissions.rows().deselect();
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
          url: `${baseUrl}roles/${id}`,
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
              dt_roles.draw(false);
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
          text: 'The Permission is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  function clearClassesTable() {
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
  }
});
