'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableJobTitles = $('.datatables-job_titles'),
    modalJobTitle = $('#modalJobTitle'),
    modalTitle = modalJobTitle.find('.modal-title');

  window.ResourceRegistry = window.ResourceRegistry || {};

  window.ResourceRegistry['job_titles'] = () => {
    dt_job_titles.ajax.reload();
  };

  if (datatableJobTitles) {
    window.dt_job_titles = new DataTable(datatableJobTitles, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}job_titles`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'title_name' },
        { data: 'creator' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, -1]
        },
        {
          searchable: true,
          targets: [1]
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalJobTitle" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search title',
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
                    'data-bs-target': '#modalJobTitle'
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

  const formJobTitle = document.getElementById('formJobTitle'),
    titleName = formJobTitle.querySelector('#title_name'),
    btnSubmit = formJobTitle.querySelector('button[type="submit"]');

  let editingId = null;

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Job Title');
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

    modalTitle.html('Edit Job Title');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}job_titles/${id}/edit`, function (data) {
      editingId = id;
      titleName.value = data.title_name || '';
    });
  });

  FormValidation.formValidation(formJobTitle, {
    fields: {
      title_name: {
        validators: {
          notEmpty: {
            message: 'Title name is required'
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

    let url = editingId ? `${baseUrl}job_titles/${editingId}` : `${baseUrl}job_titles`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formJobTitle).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_job_titles.draw(false);
        modalJobTitle.modal('hide');

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

  modalJobTitle.on('hidden.bs.modal', function () {
    formJobTitle.reset();
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
          url: `${baseUrl}job_titles/${id}`,
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
              dt_job_titles.draw(false);
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
          text: 'The title is not deleted!',
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
          url: `${baseUrl}job_titles/${id}/restore`,
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
              dt_job_titles.draw(false);
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
          text: 'The title is not restored!',
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
          url: `${baseUrl}job_titles/${id}/force`,
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
              dt_job_titles.draw(false);
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
          text: 'The title is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
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
      url: `${baseUrl}job_titles/import`,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();
        dt_job_titles.draw(false);
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
