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
    left.className = 'd-flex align-items-center gap-2 drill-unit cursor-pointer';
    left.dataset.id = node.id;

    left.innerHTML = `
      <span class="fw-bold ${node.deleted_at ? 'text-muted' : 'text-body'} fs-5">
        ${node.unit_name}
      </span>
      <small class="text-muted">(${node.unit_code})</small>
    `;

    left.addEventListener('click', function () {
      fetchOrgUnits(node.id);
    });

    const right = document.createElement('div');
    right.className = 'btn-group';

    let btnHtml;
    if (!node.deleted_at) {
      btnHtml = `
        <button class="btn btn-outline-primary edit-record" data-id="${node.id}" data-bs-toggle="modal" data-bs-target="#modalOrgUnit">
          <i class="bx bx-edit fs-4"></i>
        </button>
        <button class="btn btn-outline-danger delete-record" data-id="${node.id}">
          <i class="bx bx-trash-alt fs-4"></i>
        </button>
      `;
    } else {
      btnHtml = `
        <button class="btn btn-outline-warning restore-record" data-id="${node.id}">
          <i class="bx bx-recycle fs-4"></i>
        </button>
        <button class="btn btn-outline-danger force-record" data-id="${node.id}">
          <i class="bx bx-trash fs-4"></i>
        </button>
      `;
    }

    right.innerHTML = btnHtml;

    body.appendChild(left);
    body.appendChild(right);
    card.appendChild(body);
    li.appendChild(card);

    if (node.children && node.children.length > 0) {
      const ul = document.createElement('ul');
      ul.className = 'list-unstyled ms-4 mt-2';
      node.children.forEach(child => {
        ul.appendChild(renderOrgUnitNode(child));
      });
      li.appendChild(ul);
    }

    return li;
  }

  $list.on('click', '.drill-unit', function () {
    const id = this.dataset.id;
    fetchOrgUnits(id);
  });

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

      if (data.parent && data.parent.id != null) {
        setValue($(parentSelect), { id: data.parent.id, text: data.parent.unit_name });
      } else {
        $(parentSelect).val(null).trigger('change');
      }
    });
  });

  FormValidation.formValidation(formOrgUnit, {
    fields: {
      unit_name: {
        validators: {
          notEmpty: {
            message: 'Please enter an organization unit name'
          }
        }
      },
      unit_code: {
        validators: {
          notEmpty: {
            message: 'Please enter an organization unit code'
          }
        }
      },
      unit_type: {
        validators: {
          notEmpty: {
            message: 'Please select an option'
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
    $(formOrgUnit).find('select').val('').trigger('change');
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
          text: 'The Organization Unit is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // showToast
  function showToast(status, message) {
    // Buat elemen toast secara dinamis
    const toastElement = document.createElement('div');
    toastElement.classList.add(
      'bs-toast',
      'toast',
      'toast-ex',
      'animate__animated',
      'my-2',
      'fade',
      `bg-${status}`,
      'animate__bounceInDown'
    );
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    toastElement.setAttribute('data-bs-delay', '3500');

    // Bagian header toast
    const toastHeader = document.createElement('div');
    toastHeader.classList.add('toast-header');

    const bellIcon = document.createElement('i');
    bellIcon.classList.add('bx', 'bx-bell', 'me-2');

    const headerText = document.createElement('div');
    headerText.classList.add('me-auto', 'fw-medium');
    headerText.innerText = 'System Message';

    const closeButton = document.createElement('button');
    closeButton.setAttribute('type', 'button');
    closeButton.classList.add('btn-close');
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', 'Close');

    toastHeader.appendChild(bellIcon);
    toastHeader.appendChild(headerText);
    toastHeader.appendChild(closeButton);

    // Bagian body toast
    const toastBody = document.createElement('div');
    toastBody.classList.add('toast-body');
    toastBody.innerText = message;

    // Gabungkan semua elemen
    toastElement.appendChild(toastHeader);
    toastElement.appendChild(toastBody);

    // Tempelkan elemen toast ke dalam dokumen
    document.body.appendChild(toastElement);

    // Tampilkan toast
    const myToast = new bootstrap.Toast(toastElement);
    myToast.show();

    // Hapus elemen toast setelah animasi selesai
    toastElement.addEventListener('hidden.bs.toast', function () {
      document.body.removeChild(toastElement);
    });
  }
});
