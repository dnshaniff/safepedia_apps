'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableEmployees = $('.datatables-employees');

  if (datatableEmployees) {
    datatableEmployees.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}offboardings`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'full_name' },
        { data: 'company' },
        { data: 'job_title' },
        { data: 'employment_type' },
        { data: 'last_day' },
        { data: 'hrbp' }
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, -1]
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
          targets: 4,
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
          targets: 5,
          render: function (data, type, full, meta) {
            return `<span class="badge bg-label-danger">${data}</span>`;
          }
        }
      ],
      scrollCollapse: true,
      fixedHeader: { header: true, headerOffset: 70 },
      fixedColumns: { leftColumns: 1 },
      order: [[]],
      pageLength: 20,
      lengthMenu: [20, 50, 75, 100],
      layout: {
        topStart: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
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
});
