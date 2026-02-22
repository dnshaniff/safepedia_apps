'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableManPower = $('.datatables-manpowers'),
    modalManPower = $('#modalManPower'),
    modalTitle = modalManPower.find('.modal-title');

  let dt_manpowers;

  if (datatableManPower) {
    dt_manpowers = new DataTable(datatableManPower, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}manpower_plans`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'org_unit' },
        { data: 'planned_date' },
        { data: 'number_positions' },
        { data: 'devices' },
        { data: 'status' },
        { data: 'creator' },
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
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${data}</span>
                <a href="${baseUrl}manpower_plans/${row.id}" class="fw-medium">${row.position_title}</a>
              </div>
            `;
          }
        },
        {
          targets: 5,
          render: function (data, type, full, meta) {
            const statusMap = {
              Pending: 'bg-label-danger',
              'On Progress': 'bg-label-warning',
              Done: 'bg-label-success'
            };

            const statusClass = statusMap[data] || 'bg-label-secondary';

            return `<span class="badge ${statusClass}">${data}</span>`;
          }
        },

        {
          targets: 6,
          render: function (data, type, row) {
            const formatDate = value => {
              if (!value) return '-';

              return new Date(value).toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              });
            };

            let tooltipContent = '';

            if (row.deleted_at) {
              tooltipContent = `Deleted: ${formatDate(row.deleted_at)}`;
            } else {
              tooltipContent = `
                Created: ${formatDate(row.created_at)}
                Updated: ${formatDate(row.updated_at)}
              `;
            }

            return `
                <span class="text-nowrap"
                  data-bs-toggle="tooltip"
                  data-bs-offset="0,8"
                  data-bs-placement="top"
                  data-bs-custom-class="tooltip-dark"
                  title="${tooltipContent}">
                  ${data}
                </span>
              `;
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalManPower" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Position',
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
                    'data-bs-target': '#modalManPower'
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

    new bootstrap.Tooltip(document.body, {
      selector: '[data-bs-toggle="tooltip"]'
    });
  }, 100);

  const formManpower = document.getElementById('formManPower'),
    orgSelect = formManpower.querySelector('#org_unit_id'),
    positionTitle = formManpower.querySelector('#position_title'),
    plannedDate = formManpower.querySelector('#planned_date'),
    numberPositions = formManpower.querySelector('#number_positions'),
    deviceSelect = formManpower.querySelector('#devices'),
    notesInput = formManpower.querySelector('#notes'),
    btnSubmit = formManpower.querySelector('button[type="submit"]');

  let editingId = null;

  initDropdownPaged($(orgSelect), {
    url: '/org_units/select',
    placeholder: 'Select an option',
    perPage: 10
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

  initFP(plannedDate);

  numberPositions.addEventListener('input', function () {
    if (this.value === '') return;

    const value = parseInt(this.value, 10);

    if (isNaN(value) || value < 0) {
      this.value = 0;
    }
  });

  initDropdownPaged($(deviceSelect), {
    url: '/asset_types/select?category_code=IT',
    placeholder: 'Select an option',
    perPage: 10
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Manpower Plan');
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

    modalTitle.html('Edit Manpower Plan');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}manpower_plans/${id}/edit`, function (data) {
      editingId = id;

      data.org_unit && data.org_unit.id != null
        ? setValue($(orgSelect), { id: data.org_unit.id, text: data.org_unit.unit_name })
        : $(orgSelect).val(null).trigger('change');

      positionTitle.value = data.position_title || '';
      plannedDate._flatpickr.setDate(data.planned_date || null);
      numberPositions.value = data.number_positions || '';

      if (Array.isArray(data.devices) && data.devices.length > 0) {
        const $select = $(deviceSelect).empty();
        data.devices.forEach(d => $select.append(new Option(d.type_name, d.id, true, true)));

        $select.trigger('change');
      } else {
        $(deviceSelect).val(null).trigger('change');
      }

      notesInput.value = data.notes || '';
    });
  });

  FormValidation.formValidation(formManpower, {
    fields: {
      org_unit_id: {
        validators: {
          notEmpty: {
            message: 'Organization unit must be selected'
          }
        }
      },
      position_title: {
        validators: {
          notEmpty: {
            message: 'Title is required'
          }
        }
      },
      planned_date: {
        validators: {
          notEmpty: {
            message: 'Planned date must be selected'
          }
        }
      },
      number_positions: {
        validators: {
          notEmpty: {
            message: 'Number of position is required'
          }
        }
      },
      'devices[]': {
        validators: {
          notEmpty: {
            message: 'At least one device must be selected'
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

    let url = editingId ? `${baseUrl}manpower_plans/${editingId}` : `${baseUrl}manpower_plans`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formManpower).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_manpowers.draw(false);
        modalManPower.modal('hide');

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

  modalManPower.on('hidden.bs.modal', function () {
    formManpower.reset();
    $(formManpower).find('select').val(null).trigger('change');
    editingId = null;

    plannedDate._flatpickr.clear(false);
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
          url: `${baseUrl}manpower_plans/${id}`,
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
              dt_manpowers.draw(false);
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
          text: 'The manpower is not deleted!',
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
          url: `${baseUrl}manpower_plans/${id}/restore`,
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
              dt_manpowers.draw(false);
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
          text: 'The manpower is not restored!',
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
          url: `${baseUrl}manpower_plans/${id}/force`,
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
              dt_manpowers.draw(false);
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
          text: 'The manpower is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
