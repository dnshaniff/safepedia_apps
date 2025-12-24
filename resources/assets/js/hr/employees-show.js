'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableAgreements = $('.datatables-agreements'),
    modalAgreement = $('#modalAgreement'),
    modalTitle = modalAgreement.find('.modal-title'),
    currentPath = window.location.pathname;

  let dt_agreements;

  if (datatableAgreements) {
    dt_agreements = datatableAgreements.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: `${currentPath}/employee_agreements`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'agreement_type' },
        { data: 'date' },
        { data: 'notes' },
        { data: 'creator' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          orderable: false,
          searchable: false,
          targets: [0, 1, 2, 3, 4, -1]
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalAgreement" data-bs-toggle="modal" data-bs-dismiss="modal">
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
              buttons: [
                {
                  text: 'Create New',
                  className: 'add-new btn btn-primary mb-3 mb-md-0',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modalAgreement'
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

  const formAgreement = document.getElementById('formAgreement'),
    typeSelect = formAgreement.querySelector('#agreement_type'),
    startDate = formAgreement.querySelector('#start_date'),
    endDate = formAgreement.querySelector('#end_date'),
    effectiveDate = formAgreement.querySelector('#effective_date'),
    notesInput = formAgreement.querySelector('#notes'),
    btnSubmit = formAgreement.querySelector('button[type="submit"]');

  let editingId = null;
  const PERIOD_TYPES = new Set(['Contract', 'Extension']);

  function initFP(el) {
    if (!el) return;
    if (el._flatpickr) return;

    flatpickr(el, {
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d',
      static: true,
      allowInput: false
    });
  }

  initStatic($(typeSelect), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'Contract', text: 'Contract' },
      { id: 'Conversion', text: 'Conversion' },
      { id: 'Extension', text: 'Extension' },
      { id: 'Promotion', text: 'Promotion' },
      { id: 'Resign', text: 'Resign' },
      { id: 'Warning', text: 'Warning' }
    ]
  });

  function setFPEnabled(el, enabled) {
    if (!el) return;

    initFP(el);

    const fp = el._flatpickr;
    const visible = fp?.altInput || el;

    if (!fp) return;

    if (enabled) {
      el.disabled = false;
      el.readOnly = false;
      visible.disabled = false;
      visible.readOnly = false;
    } else {
      fp.clear();
      el.value = '';
      visible.value = '';

      el.disabled = true;
      el.readOnly = true;
      visible.disabled = true;
      visible.readOnly = true;
    }
  }

  initFP(startDate);
  initFP(endDate);
  initFP(effectiveDate);

  function applyAgreementTypeRules(type) {
    type = (type || '').trim();

    if (type === '') {
      setFPEnabled(startDate, false);
      setFPEnabled(endDate, false);
      setFPEnabled(effectiveDate, false);
      return;
    }

    const isPeriod = PERIOD_TYPES.has(type);

    setFPEnabled(startDate, isPeriod);
    setFPEnabled(endDate, isPeriod);
    setFPEnabled(effectiveDate, !isPeriod);
  }

  $(typeSelect).on('change select2:select select2:clear', function () {
    applyAgreementTypeRules($(this).val());
  });

  applyAgreementTypeRules($(typeSelect).val());

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create Agreement');
    editingId = null;
    $(btnSubmit).html('Submit');
  });
});
