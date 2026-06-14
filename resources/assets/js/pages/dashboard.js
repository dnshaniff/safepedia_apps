'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
 const chartPeriod = document.getElementById('chartPeriod');
const chartEl = document.querySelector('#constructionStatisticsChart');

chartPeriod.value = new Date().toISOString().slice(0, 7);

let constructionChart = null;

function renderChart(categories = [], data = []) {
  if (constructionChart) {
    constructionChart.destroy();
  }

  constructionChart = new ApexCharts(chartEl, {
    series: [
      {
        name: 'Construction',
        data: data.map(value => Number(value) || 0)
      }
    ],
    chart: {
      type: 'bar',
      height: 350,
      parentHeightOffset: 0,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '55%'
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      width: 0
    },
    colors: [config.colors.primary],
    grid: {
      borderColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.1)'
    },
    xaxis: {
      categories: categories.map(value => String(value)),
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        style: {
          colors: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.5)',
          fontSize: '12px'
        }
      }
    },
    yaxis: {
      min: 0,
      labels: {
        style: {
          colors: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.5)',
          fontSize: '12px'
        },
        formatter: value => `${parseInt(value || 0)}`
      }
    },
    tooltip: {
      y: {
        formatter: value => `${parseInt(value || 0)} Construction`
      }
    },
    noData: {
      text: 'No data available'
    }
  });

  constructionChart.render();
}

function loadChart(period) {
  $.ajax({
    url: `${baseUrl}dashboard-chart`,
    type: 'GET',
    data: { period },
    success: function (res) {
      const categories = Array.isArray(res.categories) ? res.categories : [];
      const data = Array.isArray(res.series) ? res.series : [];

      renderChart(categories, data);
    },
    error: function (xhr) {
      console.error('Chart load error:', xhr.responseJSON || xhr);
      renderChart([], []);
    }
  });
}

chartPeriod.addEventListener('change', function () {
  loadChart(this.value);
});

renderChart([], []);
loadChart(chartPeriod.value);


  function formatCurrency(value) {
    const number = Number(value) || 0;

    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number);
  }

  const datatableConstructions = $('.datatables-constructions');

  let dt_constructions;

  if (datatableConstructions) {
    dt_constructions = new DataTable(datatableConstructions, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}dashboard-index`
      },
      columns: [
        { data: 'construction_number' },
        { data: 'warehouse_name' },
        { data: 'grand_total_budget' },
        { data: 'approval' },
        { data: 'status' },
        { data: 'created_at' },
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5]
        },
        {
          searchable: true,
          targets: [0, 1]
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
