'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
// ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableConstructions = $('.datatables-constructions'),
    modalConstruction = $('#modalConstruction'),
    modalTitle = modalConstruction.find('.modal-title');

  let dt_constructions;

  if (datatableConstructions) {
    dt_constructions = new DataTable(datatableConstructions, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}warehouse_constructions`
      },
      columns: [
        { data: 'construction_number' },
        { data: 'warehouse_name' },
        { data: 'grand_total_budget' },
        { data: 'approval' },
        { data: 'status' },
        { data: 'created_at' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, -1]
        },
        {
          searchable: true,
          targets: [0, 1]
        },
        {
          targets: 0,
          render: function (data, type, row) {
            return `<a href="${baseUrl}warehouse_constructions/${row.id}"><small class="fw-medium">${data}</small></a>`;
          }
        },
        {
          targets: 2,
          render: function (data, type, row) {
            if (type === 'sort' || type === 'type') {
              return Number(data) || 0;
            }

            return formatCurrency(data);
          }
        },
        {
          targets: 4,
          render: function (data) {

            const statuses = {
              draft: {
                class: 'bg-label-info',
                text: 'Draft'
              },
              pending: {
                class: 'bg-label-warning',
                text: 'Pending Approval'
              },
              approved: {
                class: 'bg-label-success',
                text: 'Approved'
              },
              returned: {
                class: 'bg-label-primary',
                text: 'Returned'
              },
              canceled: {
                class: 'bg-label-danger',
                text: 'Canceled'
              }
            };

            const status = statuses[data] || statuses.draft;

            return `
              <span class="badge ${status.class}">
                ${status.text}
              </span>
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

            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.creator}</span>
                <span class="fw-medium">${new Date(data).toLocaleString('en-GB', options)}</span>
              </div>
            `;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          render: function (data, type, row) {
            if (row.deleted_at !== null) {
              return `
                <span class="text-nowrap">
                  <button class="btn btn-icon me-2 restore-record" data-id="${data}" title="Restore">
                    <i class="bx bx-recycle"></i>
                  </button>
                  <button class="btn btn-icon force-record" data-id="${data}" title="Permanent Delete">
                    <i class="bx bx-trash"></i>
                  </button>
                </span>
              `;
            }

            let buttons = `
              <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalConstruction" data-bs-toggle="modal" data-bs-dismiss="modal" title="Edit">
                <i class="bx bx-edit"></i>
              </button>
              <button class="btn btn-icon me-2 delete-record" data-id="${data}" title="Delete">
                <i class="bx bx-trash-alt"></i>
              </button>
            `;

            if (row.status === 'draft') {
              buttons += `
                <button class="btn btn-icon me-2 submit-document" data-id="${data}" title="Submit Document">
                  <i class="bx bx-send"></i>
                </button>
              `;
            }

            if (row.status === 'returned') {
              buttons += `
                <button class="btn btn-icon cancel-record" data-id="${data}" title="Cancel">
                  <i class="bx bx-x-circle"></i>
                </button>
              `;
            }

            return `
              <span class="text-nowrap">
                ${buttons}
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
                placeholder: 'Search',
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
                    'data-bs-target': '#modalConstruction'
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

  const formConstruction = document.getElementById('formConstruction'),
    warehouseName = formConstruction.querySelector('#warehouse_name'),
    latitudeField = formConstruction.querySelector('#latitude'),
    longitudeField = formConstruction.querySelector('#longitude'),
    dropzoneElement = formConstruction.querySelector('#documentDropzone'),
    btnSubmit = formConstruction.querySelector('button[type="submit"]');

  let editingId = null, constructionMap, constructionMarker, removedDocuments = [], isLoadingDocuments = false;

  function initMap(lat = null, lng = null) {
    if (constructionMap) {
      constructionMap.remove();
    }

    const mapLat = lat ?? -6.200000;
    const mapLng = lng ?? 106.816666;

    constructionMap = L.map('dragMap').setView(
      [mapLat, mapLng],
      15
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(constructionMap);

    constructionMarker = L.marker(
      [mapLat, mapLng],
      {
        draggable: true
      }
    ).addTo(constructionMap);

    latitudeField.value = Number(mapLat).toFixed(8);
    longitudeField.value = Number(mapLng).toFixed(8);

    constructionMarker.on('dragend', function (event) {
      const position = event.target.getLatLng();

      latitudeField.value = position.lat.toFixed(8);
      longitudeField.value = position.lng.toFixed(8);
    });

    if (lat === null && lng === null && navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          const currentLat = position.coords.latitude;
          const currentLng = position.coords.longitude;

          constructionMap.setView([currentLat, currentLng], 15);

          constructionMarker.setLatLng([currentLat, currentLng]);

          latitudeField.value = currentLat.toFixed(8);
          longitudeField.value = currentLng.toFixed(8);
      });
    }
  }

  const previewTemplate = `
    <div class="dz-preview dz-file-preview">
      <div class="dz-details">
        <div class="dz-thumbnail">
          <img data-dz-thumbnail>
          <span class="dz-nopreview">PDF</span>
          <div class="dz-success-mark"></div>
          <div class="dz-error-mark"></div>
          <div class="dz-error-message">
            <span data-dz-errormessage></span>
          </div>
        </div>

        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
      </div>
    </div>
  `;

  Dropzone.autoDiscover = false;

  const myDropzone = new Dropzone(dropzoneElement, {
    url: '#',
    autoProcessQueue: false,
    maxFiles: 5,
    maxFilesize: 2,
    acceptedFiles: '.pdf',
    addRemoveLinks: true,
    previewTemplate: previewTemplate,
  });

  myDropzone.on('removedfile', function (file) {
    if (isLoadingDocuments) return;

    if (file.existing && file.id) {
      removedDocuments.push(file.id);
    }
  });

  function formatCurrency(value) {
    const number = Number(value) || 0;

    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number);
  }

  function formatRupiah(value) {
    const cleanValue = String(value).replace(/\D/g, '');

    if (!cleanValue) return '';

    return cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function parseRupiah(value) {
    return Number(
      String(value)
        .replace(/\./g, '')
        .replace(/\D/g, '')
    ) || 0;
  }

  function initUnitPrice(row) {
    const unitPrice = row.querySelector('.unit-price');

    if (!unitPrice) return;

    unitPrice.addEventListener('input', event => {
      unitPrice.value = formatRupiah(event.target.value);
    });

    registerCursorTracker({
      input: unitPrice,
      delimiter: '.'
    });
  }

  function calculateLineTotal(row) {
    const qty = $(row).find('.item-qty').val();
    const unitPrice = $(row).find('.unit-price').val();

    const lineTotalText = $(row).find('.line-total-text');
    const lineTotalInput = $(row).find('.line-total');

    if (qty === '' || unitPrice === '') {
      lineTotalText.text(formatCurrency(0));
      lineTotalInput.val(0);

      updateConstructionSummary();

      return;
    }

    const qtyNumber = Number(qty) || 0;
    const unitPriceNumber = parseRupiah(unitPrice);

    const lineTotal = qtyNumber * unitPriceNumber;

    lineTotalText.text(formatCurrency(lineTotal));
    lineTotalInput.val(lineTotal);

    updateConstructionSummary();
  }

  function initLineTotal(row) {
    $(row)
      .find('.item-qty, .unit-price')
      .off('input.lineTotal')
      .on('input.lineTotal', function () {
        calculateLineTotal(row);
      });

    calculateLineTotal(row);
  }

  function getConstructionRows() {
    return $('.budget-repeater [data-repeater-item]');
  }

  function updateDeleteButtonState() {
    const rows = getConstructionRows();

    rows
      .find('[data-repeater-delete]')
      .toggleClass('d-none', rows.length <= 1);
  }

  function resetConstructionItemRow(row) {
      $(row).find('.item-item_name').val('');
      $(row).find('.item-qty').val('');
      $(row).find('.unit-price').val('');
      $(row).find('.line-total').val(0);

      $(row)
        .find('.line-total-text')
        .text(formatCurrency(0));
    }

    document
    .querySelectorAll('.budget-repeater [data-repeater-item]')
    .forEach(item => {
      initUnitPrice(item);
      initLineTotal(item);
    });

  updateDeleteButtonState();

  function updateConstructionSummary() {
    let grandTotal = 0;

    $('.budget-repeater [data-repeater-item]').each(function () {
      grandTotal += Number(
        $(this).find('.line-total').val()
      ) || 0;
    });

    $('.construction-total').text(
      formatCurrency(grandTotal)
    );
  }

  const constructionRepeater = $('.budget-repeater').repeater({
    initEmpty: false,

    show: function () {
      resetConstructionItemRow(this);

      initUnitPrice(this);
      initLineTotal(this);

      $(this).slideDown(function () {
        updateDeleteButtonState();
        updateConstructionSummary();
      });
    },

    hide: function (deleteElement) {
      if (getConstructionRows().length <= 1) {
        showToast(
          'danger',
          'Construction must have at least 1 item'
        );

        return;
      }

      $(this).slideUp(function () {
        deleteElement();

        updateDeleteButtonState();
        updateConstructionSummary();
      });
    }
  });

  updateDeleteButtonState();
  updateConstructionSummary();

  function fillConstructionItems(items) {
    const constructionItems = items.length ? items : [{}];

    constructionRepeater.setList(constructionItems.map(() => ({})));

    $('.budget-repeater [data-repeater-item]').each(function (index) {
      const item = constructionItems[index] || {};

      $(this)
        .find('.item-item_name')
        .val(item.item_name ?? '');

      $(this)
        .find('.item-qty')
        .val(item.quantity ?? '');

      $(this)
        .find('.unit-price')
        .val(
          item.unit_price
            ? formatRupiah(
                Math.round(item.unit_price)
              )
            : ''
        );

      $(this)
        .find('.line-total')
        .val(item.line_total ?? 0);

      $(this)
        .find('.line-total-text')
        .text(
          formatCurrency(
            item.line_total ?? 0
          )
        );

      initUnitPrice(this);
      initLineTotal(this);

      calculateLineTotal(this);
    });

    updateDeleteButtonState();
    updateConstructionSummary();
  }

  modalConstruction.on('shown.bs.modal', function () {
    if (!editingId) {
      initMap();
    }

    setTimeout(() => {
      constructionMap.invalidateSize();
    }, 100);
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Approval');
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
    modalTitle.html('Edit Existing Construction');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}warehouse_constructions/${id}/edit`, function (data) {
      editingId = id;

      warehouseName.value = data.warehouse_name || '';

      initMap(parseFloat(data.latitude), parseFloat(data.longitude));

      setTimeout(() => {
        constructionMap.invalidateSize();
      }, 100);

      fillConstructionItems(data.items || []);

      isLoadingDocuments = true;
      myDropzone.removeAllFiles(true);
      myDropzone.files = [];
      $(dropzoneElement).find('.dz-preview').remove();
      isLoadingDocuments = false;

      data.documents.forEach(doc => {
        const mockFile = {
          name: doc.original_name,
          size: doc.file_size,
          accepted: true,
          existing: true,
          id: doc.id
        };

        myDropzone.emit('addedfile', mockFile);
        myDropzone.emit('complete', mockFile);
        myDropzone.files.push(mockFile);
        mockFile.previewElement.querySelector('.dz-remove').dataset.id = doc.id;
      });
    });
  });

  FormValidation.formValidation(formConstruction, {
    fields: {
      warehouse_name: {
        validators: {
          notEmpty: {
            message: 'Warehouse name is required'
          }
        }
      },
      latitude: {
        validators: {
          notEmpty: {
            message: 'Location must be selected'
          }
        }
      },
      longitude: {
        validators: {
          notEmpty: {
            message: 'Location must be selected'
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
    const formData = new FormData(formConstruction);

    let url = editingId ? `${baseUrl}warehouse_constructions/${editingId}` : `${baseUrl}warehouse_constructions`;

    let method = editingId ? 'POST' : 'POST';

    if (editingId) {
      formData.append('_method', 'PATCH');
    }

    removedDocuments.forEach((id, index) => {
      formData.append(`removed_documents[${index}]`, id);
    });

    const activeFiles = myDropzone.files;

    if (activeFiles.length < 3) {
      showToast('info', 'Minimum 3 file attachments');

      return;
    }

    let documentIndex = 0;

    activeFiles.forEach(file => {
      if (!file.existing) {
        formData.append(`documents[${documentIndex}]`, file);
        documentIndex++;
      }
    });

    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    $.ajax({
      url: url,
      type: method,
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();

        dt_constructions.draw(false);

        modalConstruction.modal('hide');

        showToast(res.status, res.message);
      },
      error: function (xhr) {
        Loading.remove();

        const res = xhr.responseJSON;

        if (res) {
          showToast(res.status, res.message);
        } else {
          showToast('danger', 'An unexpected error occurred');
        }
      }
    });
  });

  modalConstruction.on('hidden.bs.modal', function () {
    formConstruction.reset();
    editingId = null;

    isLoadingDocuments = true;
    myDropzone.removeAllFiles(true);
    myDropzone.files = [];
    $(dropzoneElement).find('.dz-preview').remove();
    isLoadingDocuments = false;
    removedDocuments = [];

    $('.construction-total').text(formatCurrency(0));

    if (constructionMap) {
      constructionMap.remove();
      constructionMap = null;
      constructionMarker = null;
    }

    const firstRow = $('.budget-repeater [data-repeater-item]').first();

    $('.budget-repeater [data-repeater-item]')
      .not(firstRow)
      .remove();

    $(firstRow).find('input').val('');
    $(firstRow).find('.line-total').val(0);
    $(firstRow).find('.line-total-text').text(formatCurrency(0));

    latitudeField.value = '';
    longitudeField.value = '';
  });

  // submit record
  $(document).on('click', '.submit-document', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: 'Submit document?',
      text: 'This construction will be submitted for approval.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, submit',
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
          url: `${baseUrl}warehouse_constructions/${id}/submit`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_constructions.draw(false);
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
        showToast('info', 'The construction is not submit!');
      }
    });
  });

  // canceled record
  $(document).on('click', '.cancel-record', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: 'Cancel construction?',
      text: 'This returned construction will be canceled.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel',
      customClass: {
        confirmButton: 'btn btn-danger me-3',
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
          url: `${baseUrl}warehouse_constructions/${id}/cancel`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_constructions.draw(false);
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
        showToast('info', 'The construction is not cancel!');
      }
    });
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
          url: `${baseUrl}warehouse_constructions/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_constructions.draw(false);
          },
          error: function (jqXHR) {
            Loading.remove();
            showToast(jqXHR.responseJSON?.status || 'danger', jqXHR.responseJSON?.message || 'An unexpected error occurred');
          }
        });
      } else {
        Loading.remove();
        showToast('info', 'The construction is not deleted!');
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
          url: `${baseUrl}warehouse_constructions/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_constructions.draw(false);
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
        showToast('info', 'The construction is not restored!');
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
          url: `${baseUrl}warehouse_constructions/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_constructions.draw(false);
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
        showToast('info', 'The construction is not deleted!');
      }
    });
  });
});
