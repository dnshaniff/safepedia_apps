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

  window.ResourceRegistry = window.ResourceRegistry || {};

  window.ResourceRegistry['employees'] = () => {
    dt_employees.ajax.reload();
  };

  if (datatableEmployees) {
    window.dt_employees = datatableEmployees.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}employees`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'full_name' },
        { data: 'company' },
        { data: 'job_title' },
        { data: 'join_date' },
        { data: 'employment_status' },
        { data: 'hrbp' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, 6, -1]
        },
        {
          searchable: true,
          targets: [1]
        },
        {
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, row) {
            const nik = row.employee_code;
            const fullName = data;
            const image = row.picture;
            const imageUrl = `${image ? `/storage/${image}` : `/assets/img/avatars/avatar.png`}`;

            const rowOutput = `
              <div class="d-flex justify-content-left align-items-center">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3">
                    <img src="${imageUrl}" alt="Avatar" class="w-px-38 h-px-38 rounded-circle" style="object-position: center; object-fit: cover;">
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <small class="text-muted ext">ID: ${nik}</small>
                  <a href="${baseUrl}employees/${row.id}" class="fw-medium">${fullName}</a>
                </div>
              </div>
            `;

            return rowOutput;
          }
        },
        {
          targets: 2,
          render: function (data, type, full, meta) {
            const companyMap = {
              GST: 'bg-label-primary',
              SMB: 'bg-label-danger',
              GIA: 'bg-label-dark'
            };

            const companyClass = companyMap[data] || 'bg-label-secondary';

            return `<span class="badge ${companyClass}">${data}</span>`;
          }
        },
        {
          targets: 3,
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.org_unit}</span>
                <span class="fw-medium">${data}</span>
              </div>
            `;
          }
        },
        {
          targets: 5,
          render: function (data, type, full, meta) {
            const statusMap = {
              Colleague: 'bg-label-success',
              Contract: 'bg-label-dark',
              Freelance: 'bg-label-primary',
              Intern: 'bg-label-info',
              Probation: 'bg-label-warning',
              Resign: 'bg-label-danger'
            };

            const statusClass = statusMap[data] || 'bg-label-secondary';

            return `<span class="badge ${statusClass}">${data}</span>`;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          render: function (data, type, row) {
            if (row.deleted_at !== null) {
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

            let buttons = `
              <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalEmployee" data-bs-toggle="modal" data-bs-dismiss="modal">
                <i class="bx bx-edit"></i>
              </button>
              <button class="btn btn-icon delete-record" data-id="${data}">
                <i class="bx bx-trash-alt"></i>
              </button>
            `;

            if (row.deleted_at === null && row.user_id === null && row.can_store_user === true) {
              buttons += `
                <button class="btn btn-icon create-user" data-id="${data}" data-bs-target="#modalUser" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-user-plus"></i>
                </button>
              `;
            }

            return `<span class="text-nowrap">${buttons}</span>`;
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
                  className: 'add-new btn btn-primary mb-3 mb-md-0 me-3',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalEmployee'
                  }
                }
              ]
            },
            {
              buttons: [
                {
                  text: '<i class="bx bx-import"></i>',
                  className: 'import-record btn btn-primary mb-3 mb-md-0',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalImport'
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
    employeeCode = formEmployee.querySelector('#employee_code'),
    fullName = formEmployee.querySelector('#full_name'),
    hrbpSelect = formEmployee.querySelector('#hrbp_id'),
    managerSelect = formEmployee.querySelector('#manager_id'),
    joinDate = formEmployee.querySelector('#join_date'),
    companySelect = formEmployee.querySelector('#company_id'),
    orgSelect = formEmployee.querySelector('#org_unit_id'),
    titleSelect = formEmployee.querySelector('#job_title_id'),
    statusSelect = formEmployee.querySelector('#employment_status'),
    officeEmail = formEmployee.querySelector('#office_email'),
    personalEmail = formEmployee.querySelector('#personal_email'),
    phoneNumber = formEmployee.querySelector('#phone_number'),
    genderSelect = formEmployee.querySelector('#gender'),
    dateBirth = formEmployee.querySelector('#date_of_birth'),
    ktpNumber = formEmployee.querySelector('#ktp_number'),
    btnSubmit = formEmployee.querySelector('button[type="submit"]');

  let editingId = null;

  if (phoneNumber) {
    phoneNumber.addEventListener('input', event => {
      const cleanValue = event.target.value.replace(/\D/g, '');
      phoneNumber.value = formatGeneral(cleanValue, {
        blocks: [4, 4, 5],
        delimiters: [' ', ' ']
      });
    });
    registerCursorTracker({
      input: phoneNumber,
      delimiter: ' '
    });
  }

  if (ktpNumber) {
    ktpNumber.addEventListener('input', event => {
      const cleanValue = event.target.value.replace(/\D/g, '');
      ktpNumber.value = formatGeneral(cleanValue, {
        blocks: [4, 4, 4, 4],
        delimiters: [' ', ' ', ' ']
      });
    });
    registerCursorTracker({
      input: ktpNumber,
      delimiter: ' '
    });
  }

  initDropdownPaged($(hrbpSelect), {
    url: '/employees/select?org_unit_code=HR&org_unit_type=Department',
    placeholder: 'Select an option',
    perPage: 10
  });

  initDropdownPaged($(managerSelect), {
    url: '/employees/select',
    placeholder: 'Select an option',
    perPage: 10
  });

  initDropdownPaged($(companySelect), {
    url: '/companies/select',
    placeholder: 'Select an option',
    perPage: 10,
    hideSearch: true
  });

  initDropdownPaged($(orgSelect), {
    url: '/org_units/select',
    placeholder: 'Select an option',
    perPage: 10
  });

  initDropdownPaged($(titleSelect), {
    url: '/job_titles/select',
    placeholder: 'Select an option',
    perPage: 10
  });

  initStatic($(statusSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'Colleague', text: 'Colleague' },
      { id: 'Contract', text: 'Contract' },
      { id: 'Freelance', text: 'Freelance' },
      { id: 'Intern', text: 'Intern' },
      { id: 'Probation', text: 'Probation' },
      { id: 'Resign', text: 'Resign' }
    ]
  });

  initStatic($(genderSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'Female', text: 'Female' },
      { id: 'Male', text: 'Male' }
    ]
  });

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

  initFP(joinDate);
  initFP(dateBirth);

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Employee');
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

    modalTitle.html('Edit Employee');
    $(btnSubmit).html('Save');

    $.get(`${baseUrl}employees/${id}/edit`, function (data) {
      editingId = id;

      employeeCode.value = data.employee_code || '';
      fullName.value = data.full_name || '';

      data.hrbp && data.hrbp.id != null
        ? setValue($(hrbpSelect), { id: data.hrbp.id, text: data.hrbp.full_name })
        : $(hrbpSelect).val(null).trigger('change');

      data.manager && data.manager.id != null
        ? setValue($(managerSelect), { id: data.manager.id, text: data.manager.full_name })
        : $(managerSelect).val(null).trigger('change');

      joinDate._flatpickr.setDate(data.join_date || null);

      data.company && data.company.id != null
        ? setValue($(companySelect), { id: data.company.id, text: data.company.company_name })
        : $(companySelect).val(null).trigger('change');

      data.org_unit && data.org_unit.id != null
        ? setValue($(orgSelect), { id: data.org_unit.id, text: data.org_unit.unit_name })
        : $(orgSelect).val(null).trigger('change');

      data.job_title && data.job_title.id != null
        ? setValue($(titleSelect), { id: data.job_title.id, text: data.job_title.title_name })
        : $(titleSelect).val(null).trigger('change');

      $(statusSelect).val(data.employment_status).trigger('change');

      officeEmail.value = data.office_email || '';
      personalEmail.value = data.personal_email || '';
      phoneNumber.value = data.phone_number;

      $(genderSelect).val(data.gender).trigger('change');

      dateBirth._flatpickr.setDate(data.date_of_birth || null);

      ktpNumber.value = data.ktp_number || '';
    });
  });

  FormValidation.formValidation(formEmployee, {
    fields: {
      employee_code: {
        validators: {
          notEmpty: {
            message: 'Please enter employee id'
          }
        }
      },
      full_name: {
        validators: {
          notEmpty: {
            message: 'Please enter full name'
          }
        }
      },
      join_date: {
        validators: {
          notEmpty: {
            message: 'Please select join date'
          }
        }
      },
      company_id: {
        validators: {
          notEmpty: {
            message: 'Please select company'
          }
        }
      },
      org_unit_id: {
        validators: {
          notEmpty: {
            message: 'Please select Organization Unit'
          }
        }
      },
      job_title_id: {
        validators: {
          notEmpty: {
            message: 'Please select job title'
          }
        }
      },
      employment_status: {
        validators: {
          notEmpty: {
            message: 'Please select employment type'
          }
        }
      },
      office_email: {
        validators: {
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      personal_email: {
        validators: {
          notEmpty: {
            message: 'Please enter personal email'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      phone_number: {
        validators: {
          notEmpty: {
            message: 'Please enter phone number'
          }
        }
      },
      gender: {
        validators: {
          notEmpty: {
            message: 'Please select gender'
          }
        }
      },
      date_of_birth: {
        validators: {
          notEmpty: {
            message: 'Please select birth date'
          }
        }
      },
      ktp_number: {
        validators: {
          notEmpty: {
            message: 'Please enter KTP number'
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
    $(formEmployee).find('select').val(null).trigger('change');
    editingId = null;

    dateBirth._flatpickr.clear(false);
    joinDate._flatpickr.clear(false);
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
          url: `${baseUrl}employees/${id}`,
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
          text: 'The Organization Unit is not deleted!',
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
          url: `${baseUrl}employees/${id}/restore`,
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
          text: 'The Organization Unit is not restored!',
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
          url: `${baseUrl}employees/${id}/force`,
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
          text: 'The Organization Unit is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  const modalUser = $('#modalUser'),
    formUser = document.getElementById('formUser'),
    roleSelect = formUser.querySelector('#role');

  initDropdownPaged($(roleSelect), {
    url: '/roles/select',
    placeholder: 'Select an option',
    perPage: 10,
    hideSearch: false
  });

  // create user
  $(document).on('click', '.create-user', function () {
    const id = $(this).data('id');
    editingId = id;
  });

  FormValidation.formValidation(formUser, {
    fields: {
      username: {
        validators: {
          notEmpty: {
            message: 'Please enter an username'
          },
          stringLength: {
            min: 4,
            message: 'The username must be at least 4 characters long'
          }
        }
      },
      role: {
        validators: {
          notEmpty: {
            message: 'Please select a role'
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
      data: $(formUser).serialize(),
      url: `${baseUrl}employees/${editingId}/storeUser`,
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
    $(formUser).find('select').val('').trigger('change');
  });

  const modalImport = $('#modalImport'),
    formImport = document.getElementById('formImport');

  FormValidation.formValidation(formImport, {
    fields: {
      file_import: {
        validators: {
          file: {
            extension: 'xls,xlsx',
            maxSize: 5120 * 1024, // ukuran maksimal dalam byte
            message: 'Please choose a valid file (xls, xlsx)'
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

    const formData = new FormData(formImport);

    $.ajax({
      data: formData,
      url: `${baseUrl}employees/import`,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();
        dt_employees.draw(false);
        modalImport.modal('hide');

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
  modalImport.on('hidden.bs.modal', function () {
    formImport.reset();
  });
});
