'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const $list = $('#org-unit'),
    modalOrgUnit = $('#modalOrgUnit'),
    modalTitle = modalOrgUnit.find('.modal-title'),
    formOrgUnit = document.getElementById('formOrgUnit'),
    unitName = formOrgUnit.querySelector('#unit_name'),
    unitCode = formOrgUnit.querySelector('#unit_code'),
    typeSelect = formOrgUnit.querySelector('#unit_type'),
    parentSelect = formOrgUnit.querySelector('#parent_id'),
    btnSubmit = formOrgUnit.querySelector('button[type="submit"]');

  let editingId = null,
    currentParentId = null;

  function fetchOrgUnits(parentId) {
    if (typeof parentId !== 'undefined') {
      currentParentId = parentId;
    }

    const params = {};
    if (currentParentId !== null && currentParentId !== '') {
      params.parent_id = currentParentId;
    }

    $.getJSON(`${baseUrl}org_units`, params, function (res) {
      const units = res.data || [];
      const breadcrumbs = res.breadcrumbs || [];

      renderBreadcrumbs(breadcrumbs);

      $list.empty();
      units.forEach(unit => $list.append(renderOrgUnitNode(unit)));

      initSortable();
    });
  }

  function renderBreadcrumbs(breadcrumbs) {
    const $bc = $('#org-breadcrumbs');
    $bc.empty();

    if (!breadcrumbs || breadcrumbs.length === 0) return;

    let html = `<nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">`;

    html += `<li class="breadcrumb-item">
              <a href="javascript:;" class="bc-link" data-id="">GST</a>
              <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>`;

    breadcrumbs.forEach((item, index) => {
      if (index === breadcrumbs.length - 1) {
        html += `<li class="breadcrumb-item active">${item.name}</li>`;
      } else {
        html += `<li class="breadcrumb-item">
                  <a href="javascript:;" class="bc-link" data-id="${item.id}">
                    ${item.name}
                  </a>
                  <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>`;
      }
    });

    html += `</ol></nav>`;

    $bc.html(html);
  }

  function renderOrgUnitNode(node) {
    const li = document.createElement('li');
    li.className = 'mb-2';
    li.dataset.id = node.id;

    const card = document.createElement('div');
    card.className = 'card shadow-none border mb-0';
    if (node.deleted_at) {
      card.classList.add('bg-danger-subtle', 'border-danger-subtle', 'text-muted');
    }

    const body = document.createElement('div');
    body.className = 'card-body py-3 px-4 d-flex align-items-center justify-content-between gap-2';

    const left = document.createElement('div');
    left.className = 'd-flex align-items-center gap-3';
    left.dataset.id = node.id;

    const iconWrapper = document.createElement('div');
    iconWrapper.className = 'drag-handle d-flex align-items-center pe-3 me-2 border-end cursor-pointer';
    iconWrapper.innerHTML = `<i class="bx bx-menu fs-4 text-muted"></i>`;

    const textWrapper = document.createElement('div');
    textWrapper.className = 'd-flex align-items-baseline cursor-pointer gap-2';

    textWrapper.innerHTML = `
    <span class="fw-bold ${node.deleted_at ? 'text-muted' : 'text-body'} fs-5">
      ${node.unit_name}
    </span>
    <small class="text-muted">(${node.unit_code})</small>
  `;

    left.appendChild(iconWrapper);
    left.appendChild(textWrapper);

    left.addEventListener('click', function (e) {
      if (e.target.closest('.drag-handle')) return;
      fetchOrgUnits(node.id);
    });

    const right = document.createElement('div');
    right.className = 'btn-group';
    right.innerHTML = node.deleted_at
      ? `
      <button class="btn btn-outline-danger restore-record" data-id="${node.id}">
        <i class="bx bx-recycle fs-4"></i>
      </button>
      <button class="btn btn-outline-danger force-record" data-id="${node.id}">
        <i class="bx bx-trash fs-4"></i>
      </button>
    `
      : `
      <button class="btn btn-outline-primary edit-record" data-id="${node.id}" data-bs-toggle="modal" data-bs-target="#modalOrgUnit">
        <i class="bx bx-edit fs-4"></i>
      </button>
      <button class="btn btn-outline-danger delete-record" data-id="${node.id}">
        <i class="bx bx-trash-alt fs-4"></i>
      </button>
    `;

    body.appendChild(left);
    body.appendChild(right);
    card.appendChild(body);
    li.appendChild(card);

    return li;
  }

  $list.on('click', '.drill-unit', function () {
    const id = this.dataset.id;
    fetchOrgUnits(id);
  });

  function initSortable() {
    if (!$list.length) return;

    Sortable.create($list[0], {
      handle: '.drag-handle',
      animation: 150,
      onEnd: function () {
        const items = [];
        $('#org-unit > li').each(function (index, li) {
          const id = li.dataset.id;
          if (!id) return;
          items.push({ id: id, sort_order: index + 1 });
        });

        const parentId = currentParentId || null;

        Swal.fire({
          title: 'Are you sure?',
          text: 'You are going to reorder the organization units.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, reorder',
          cancelButtonText: 'Cancel',
          customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
        }).then(function (result) {
          if (result.isConfirmed) {
            Loading.circle({
              backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
              svgSize: '60px',
              svgColor: config.colors.white
            });

            $.ajax({
              url: `${baseUrl}org_units/reorder`,
              method: 'PATCH',
              data: {
                parent_id: parentId,
                items: items
              },
              success: function (res) {
                Loading.remove();
                showToast(res.status, res.message);
                fetchOrgUnits(parentId);
              },
              error: function (xhr) {
                Loading.remove();
                const res = xhr.responseJSON || {};
                showToast(res.status || 'danger', res.message || 'Failed to reorder units');
                fetchOrgUnits(parentId);
              }
            });
          } else {
            Swal.fire({
              title: 'Cancelled',
              text: 'The organization units were not reordered.',
              icon: 'info',
              customClass: {
                confirmButton: 'btn btn-primary'
              },
              buttonsStyling: false
            });
            fetchOrgUnits(parentId);
          }
        });
      }
    });
  }

  fetchOrgUnits();

  $(document).on('click', '.bc-link', function (e) {
    e.preventDefault();
    const id = $(this).data('id') || '';
    fetchOrgUnits(id);
  });

  initStatic($(typeSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'Office', text: 'Office' },
      { id: 'Division', text: 'Division' },
      { id: 'Department', text: 'Department' },
      { id: 'Team', text: 'Team' }
    ]
  });

  initDropdownPaged($(parentSelect), {
    url: '/org_units/select',
    placeholder: 'Select an option',
    perPage: 10
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Organization Unit');
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
    modalTitle.html('Edit Organization Unit');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}org_units/${id}/edit`, function (data) {
      editingId = id;
      unitName.value = data.unit_name || '';
      unitCode.value = data.unit_code || '';

      if (data.unit_type) {
        $(typeSelect).val(data.unit_type).trigger('change');
      }

      data.parent && data.parent.id != null
        ? setValue($(parentSelect), { id: data.parent.id, text: data.parent.unit_name })
        : $(parentSelect).val(null).trigger('change');
    });
  });

  FormValidation.formValidation(formOrgUnit, {
    fields: {
      unit_name: {
        validators: {
          notEmpty: {
            message: 'Unit name is required'
          }
        }
      },
      unit_code: {
        validators: {
          notEmpty: {
            message: 'Unit code is required'
          }
        }
      },
      unit_type: {
        validators: {
          notEmpty: {
            message: 'Type must be selected'
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

    let url = editingId ? `${baseUrl}org_units/${editingId}` : `${baseUrl}org_units`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formOrgUnit).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        fetchOrgUnits();
        modalOrgUnit.modal('hide');

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
  modalOrgUnit.on('hidden.bs.modal', function () {
    formOrgUnit.reset();
    $(formOrgUnit).find('select').val(null).trigger('change');
    editingId = null;
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
          url: `${baseUrl}org_units/${id}`,
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
              fetchOrgUnits();
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
          text: 'The organization unit is not deleted!',
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
          url: `${baseUrl}org_units/${id}/restore`,
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
              fetchOrgUnits();
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
          text: 'The organization unit is not restored!',
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
          url: `${baseUrl}org_units/${id}/force`,
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
              fetchOrgUnits();
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
          text: 'The organization unit is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
