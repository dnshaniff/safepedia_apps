'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableInvoices = $('.datatables-invoices'),
    modalInvoice = $('#modalInvoice'),
    modalTitle = modalInvoice.find('.modal-title');

  let dt_invoices;

  function formatRupiahCurrency(value) {
    const number = Number(value) || 0;

    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number);
  }

  if (datatableInvoices) {
    dt_invoices = new DataTable(datatableInvoices, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}invoices`
      },
      columns: [
        { data: 'number' },
        { data: 'status' },
        { data: 'customer_name' },
        { data: 'grand_total' },
        { data: 'issued_date' },
        { data: 'grand_total' },
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
            return `<small class="fw-medium">${data}</small>`;
          }
        },
        {
          targets: 1,
          render: function (data, type, row) {
            const statusMap = {
              EXPIRED: 'bg-label-secondary',
              UNPAID: 'bg-label-danger',
              'DP PAID': 'bg-label-warning',
              'PARTIAL PAID': 'bg-label-info',
              PAID: 'bg-label-success'
            };

            const statusClass = statusMap[data] || 'bg-label-secondary';

            return '<span class="badge ' + statusClass + '">' + data + '</span>';
          }
        },
        {
          targets: 2,
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.customer_phone}</span>
                <span class="fw-medium">${data}</span>
              </div>
            `;
          }
        },
        {
          targets: [3, 5],
          render: function (data, type, row) {
            if (type === 'sort' || type === 'type') {
              return Number(data) || 0;
            }

            return formatRupiahCurrency(data);
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
                <button class="btn btn-icon me-2 invoice-pdf" data-id="${data}">
                  <i class="bx bx-file"></i>
                </button>
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalInvoice" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Invoice Number',
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
                    'data-bs-target': '#modalInvoice'
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

  const formInvoice = document.getElementById('formInvoice'),
    customerName = formInvoice.querySelector('#customer_name'),
    customerPhone = formInvoice.querySelector('#customer_phone'),
    customerAddress = formInvoice.querySelector('#customer_address'),
    paymentTerms = formInvoice.querySelector('#payment_terms'),
    fillReference = formInvoice.querySelector('#reference'),
    issuedDate = formInvoice.querySelector('#issued_date'),
    validUntil = formInvoice.querySelector('#valid_until'),
    btnSubmit = formInvoice.querySelector('button[type="submit"]');

  let editingId = null;

  if (customerPhone) {
    customerPhone.addEventListener('input', event => {
      const cleanValue = event.target.value.replace(/\D/g, '');
      customerPhone.value = formatGeneral(cleanValue, {
        blocks: [4, 4, 5],
        delimiters: [' ', ' ']
      });
    });
    registerCursorTracker({
      input: customerPhone,
      delimiter: ' '
    });
  }

  initStatic($(paymentTerms), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'cbd', text: 'Cash Before Delivery' },
      { id: 'cod', text: 'Cash on Delivery' },
      { id: 'dp', text: 'Down Payment' }
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

  initFP(issuedDate);
  initFP(validUntil);

  function initProductSelect(element) {
    if (!element.length) return;

    initDropdownPaged(element, {
      url: '/products/select',
      placeholder: 'Select Product',
      perPage: 10,
      hideSearch: false,
      dropdownParent: $('#modalInvoice')
    });
  }

  initProductSelect($('.invoice-repeater .select-product'));

  function formatRupiah(value) {
    const cleanValue = String(value).replace(/\D/g, '');

    if (!cleanValue) return '';

    return cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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

  function resetProductSelect(row) {
    const productSelect = $(row).find('.select-product');

    productSelect
      .removeClass('select2-hidden-accessible')
      .removeAttr('data-select2-id')
      .removeAttr('aria-hidden')
      .removeAttr('tabindex')
      .val(null)
      .empty();

    $(row).find('.select2-container').remove();

    initProductSelect(productSelect);
  }

  function parseRupiah(value) {
    return Number(String(value).replace(/\./g, '').replace(/\D/g, '')) || 0;
  }

  function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(value);
  }

  function initDiscount(row) {
    const discount = row.querySelector('.item-discount');

    if (!discount) return;

    discount.addEventListener('input', event => {
      let value = event.target.value.replace(/\D/g, '');

      if (value !== '') {
        value = Math.min(Number(value), 100);
      }

      discount.value = value;
      calculateLineTotal(row);
    });
  }

  function calculateLineTotal(row) {
    const product = $(row).find('.select-product').val();
    const qty = $(row).find('.item-qty').val();
    const uom = $(row).find('.item-uom').val();
    const unitPrice = $(row).find('.unit-price').val();
    const discount = $(row).find('.item-discount').val();

    const lineTotalText = $(row).find('.line-total-text');
    const lineTotalInput = $(row).find('.line-total');

    if (!product || qty === '' || uom === '' || unitPrice === '' || discount === '') {
      lineTotalText.text(formatCurrency(0));
      lineTotalInput.val(0);
      updateInvoiceSummary();
      return;
    }

    const qtyNumber = Number(qty) || 0;
    const unitPriceNumber = parseRupiah(unitPrice);
    const discountNumber = Math.min(Math.max(Number(discount) || 0, 0), 100);

    const subtotal = qtyNumber * unitPriceNumber;
    const discountAmount = subtotal * (discountNumber / 100);
    const lineTotal = subtotal - discountAmount;

    lineTotalText.text(formatCurrency(lineTotal));
    lineTotalInput.val(lineTotal);

    updateInvoiceSummary();
  }

  function initLineTotal(row) {
    $(row)
      .find('.select-product')
      .off('change.lineTotal')
      .on('change.lineTotal', function () {
        calculateLineTotal(row);
      });

    $(row)
      .find('.item-qty, .item-uom, .unit-price, .item-discount')
      .off('input.lineTotal')
      .on('input.lineTotal', function () {
        calculateLineTotal(row);
      });

    calculateLineTotal(row);
  }

  function getInvoiceItemRows() {
    return $('.invoice-repeater [data-repeater-item]');
  }

  function updateDeleteButtonState() {
    const rows = getInvoiceItemRows();

    rows.find('[data-repeater-delete]').toggleClass('d-none', rows.length <= 1);
  }

  function resetInvoiceItemRow(row) {
    $(row).find('.item-qty').val('');
    $(row).find('.item-uom').val('');
    $(row).find('.unit-price').val('');
    $(row).find('.item-discount').val('');
    $(row).find('.line-total').val(0);
    $(row).find('.line-total-text').text(formatCurrency(0));
  }

  document.querySelectorAll('.invoice-repeater [data-repeater-item]').forEach(item => {
    initUnitPrice(item);
    initDiscount(item);
    initLineTotal(item);
  });

  updateDeleteButtonState();

  function hasValidInvoiceItem() {
    let valid = false;

    $('.invoice-repeater [data-repeater-item]').each(function () {
      const product = $(this).find('.select-product').val();
      const qty = $(this).find('.item-qty').val();
      const uom = $(this).find('.item-uom').val();
      const unitPrice = $(this).find('.unit-price').val();
      const discount = $(this).find('.item-discount').val();

      if (product && qty !== '' && uom !== '' && unitPrice !== '' && discount !== '') {
        valid = true;
        return false;
      }
    });

    return valid;
  }

  function getInvoiceRowCalculation(row) {
    const product = $(row).find('.select-product').val();
    const qty = $(row).find('.item-qty').val();
    const uom = $(row).find('.item-uom').val();
    const unitPrice = $(row).find('.unit-price').val();
    const discount = $(row).find('.item-discount').val();

    if (!product || qty === '' || uom === '' || unitPrice === '' || discount === '') {
      return {
        isValid: false,
        subtotal: 0,
        discountAmount: 0,
        total: 0
      };
    }

    const qtyNumber = Number(qty) || 0;
    const unitPriceNumber = parseRupiah(unitPrice);
    const discountPercent = Math.min(Math.max(Number(discount) || 0, 0), 100);

    const subtotal = qtyNumber * unitPriceNumber;
    const discountAmount = subtotal * (discountPercent / 100);
    const total = subtotal - discountAmount;

    return {
      isValid: true,
      subtotal,
      discountAmount,
      total
    };
  }

  function updateInvoiceSummary() {
    let invoiceSubtotal = 0;
    let invoiceDiscount = 0;
    let invoiceTotal = 0;

    $('.invoice-repeater [data-repeater-item]').each(function () {
      const calculation = getInvoiceRowCalculation(this);

      if (!calculation.isValid) return;

      invoiceSubtotal += calculation.subtotal;
      invoiceDiscount += calculation.discountAmount;
      invoiceTotal += calculation.total;
    });

    $('.invoice-subtotal').text(formatCurrency(invoiceSubtotal));
    $('.invoice-discount').text(formatCurrency(invoiceDiscount));
    $('.invoice-total').text(formatCurrency(invoiceTotal));
  }

  function resetInvoiceSummary() {
    $('.invoice-subtotal').text(formatCurrency(0));
    $('.invoice-discount').text(formatCurrency(0));
    $('.invoice-total').text(formatCurrency(0));
  }

  // export pdf
  $(document).on('click', '.invoice-pdf', function () {
    const id = $(this).data('id');

    window.open(`${baseUrl}invoices/${id}/pdf`, '_blank');
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Invoice');
    editingId = null;
    $(btnSubmit).html('Submit');
  });

  const invoiceRepeater = $('.invoice-repeater').repeater({
    initEmpty: false,

    show: function () {
      resetInvoiceItemRow(this);
      resetProductSelect(this);
      initUnitPrice(this);
      initDiscount(this);
      initLineTotal(this);

      $(this).slideDown(function () {
        updateDeleteButtonState();
        updateInvoiceSummary();
      });
    },

    hide: function (deleteElement) {
      if (getInvoiceItemRows().length <= 1) {
        showToast('danger', 'Invoice must have at least 1 item');
        return;
      }

      $(this).slideUp(function () {
        deleteElement();
        updateDeleteButtonState();
        updateInvoiceSummary();
      });
    }
  });

  updateDeleteButtonState();
  updateInvoiceSummary();

  function formatRupiahFromDecimal(value) {
    return formatRupiah(Math.round(Number(value) || 0));
  }

  function setProductSelectValue(row, item) {
    const productSelect = $(row).find('.select-product');

    if (!item.product_id) return;

    const productText =
      item.product?.name || item.product?.product_name || item.product?.title || item.product_name || item.product_id;

    const option = new Option(productText, item.product_id, true, true);

    productSelect.append(option).trigger('change');
  }

  function formatPercent(value) {
    const number = Number(value);

    if (Number.isNaN(number)) return '';

    return number.toString();
  }

  function fillInvoiceItems(items) {
    const invoiceItems = items.length ? items : [{}];

    invoiceRepeater.setList(invoiceItems.map(() => ({})));

    $('.invoice-repeater [data-repeater-item]').each(function (index) {
      const item = invoiceItems[index] || {};

      $(this)
        .find('.item-qty')
        .val(item.quantity ?? '');
      $(this)
        .find('.item-uom')
        .val(item.uom ?? '');
      $(this)
        .find('.unit-price')
        .val(item.unit_price ? formatRupiahFromDecimal(item.unit_price) : '');
      $(this)
        .find('.item-discount')
        .val(item.discount !== undefined && item.discount !== null ? formatPercent(item.discount) : '');
      $(this)
        .find('.line-total')
        .val(item.line_total ?? 0);
      $(this)
        .find('.line-total-text')
        .text(formatCurrency(item.line_total ?? 0));

      resetProductSelect(this);
      initUnitPrice(this);
      initDiscount(this);
      initLineTotal(this);

      setProductSelectValue(this, item);

      calculateLineTotal(this);
    });

    updateDeleteButtonState();
    updateInvoiceSummary();
  }

  // edit record
  $(document).on('click', '.edit-record', function () {
    const id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // changing the title of modal
    modalTitle.html('Edit Existing Invoice');
    $(btnSubmit).html('Save');

    $.get(`${baseUrl}invoices/${id}/edit`, function (data) {
      editingId = id;

      customerName.value = data.customer_name || '';
      customerPhone.value = data.customer_phone || '';
      customerAddress.value = data.customer_address || '';

      if (data.payment_terms) {
        $(paymentTerms).val(data.payment_terms).trigger('change');
      }

      fillReference.value = data.reference || '';

      issuedDate._flatpickr.setDate(data.issued_date || null);
      validUntil._flatpickr.setDate(data.valid_until || null);

      fillInvoiceItems(data.items || []);
    });
  });

  FormValidation.formValidation(formInvoice, {
    fields: {
      customer_name: {
        validators: {
          notEmpty: {
            message: 'Name is required'
          },
          stringLength: {
            min: 4,
            message: 'Name must be at least 4 characters long'
          }
        }
      },
      customer_phone: {
        validators: {
          notEmpty: {
            message: 'Phone number is required'
          },
          stringLength: {
            min: 11,
            message: 'Phone number must be at least 10 characters long'
          }
        }
      },
      customer_address: {
        validators: {
          notEmpty: {
            message: 'Adress is required'
          }
        }
      },
      payment_terms: {
        validators: {
          notEmpty: {
            message: 'Payment terms must be selected'
          }
        }
      },
      reference: {
        validators: {
          notEmpty: {
            message: 'Reference is required'
          }
        }
      },
      issued_date: {
        validators: {
          notEmpty: {
            message: 'Date must be selected'
          }
        }
      },
      valid_until: {
        validators: {
          notEmpty: {
            message: 'Date must be selected'
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
    if (!hasValidInvoiceItem()) {
      showToast('info', 'Please add at least 1 invoice item');
      return;
    }

    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    let url = editingId ? `${baseUrl}invoices/${editingId}` : `${baseUrl}invoices`;
    let method = editingId ? 'PATCH' : 'POST';

    $.ajax({
      data: $(formInvoice).serialize(),
      url: url,
      type: method,
      success: function (res) {
        Loading.remove();
        dt_invoices.draw(false);
        modalInvoice.modal('hide');

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

  modalInvoice.on('hidden.bs.modal', function () {
    formInvoice.reset();
    editingId = null;
    $(formInvoice).find('select').val('').trigger('change');
    issuedDate._flatpickr.clear(false);
    validUntil._flatpickr.clear(false);
    resetInvoiceSummary();
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
          url: `${baseUrl}invoices/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_invoices.draw(false);
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
        showToast('info', 'The invoice is not deleted!');
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
          url: `${baseUrl}invoices/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_invoices.draw(false);
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
        showToast('info', 'The invoice is not restored!');
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
          url: `${baseUrl}invoices/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_invoices.draw(false);
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
        showToast('info', 'The invoice is not deleted!');
      }
    });
  });
});
