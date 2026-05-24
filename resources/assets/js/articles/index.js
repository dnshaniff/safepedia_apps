'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const datatableArticles = $('.datatables-articles'),
    modalArticle = $('#modalArticle'),
    modalTitle = modalArticle.find('.modal-title');

  let dt_articles;

  if (datatableArticles) {
    dt_articles = new DataTable(datatableArticles, {
      processing: true,
      serverSide: true,
      ajax: {
        url: `${baseUrl}articles`
      },
      columns: [
        { data: 'fake_id' },
        { data: 'title' },
        { data: 'project_at' },
        { data: 'status' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'id' }
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
          responsivePriority: 1,
          render: function (data, type, row) {
            let titleArticle = data;
            let thumbnail = row.thumbnail ?? null;

            let thumbnailOutput = thumbnail
              ? `
              <a href="${thumbnail}" data-fancybox="brand-${row.id}">
                <img
                  src="${thumbnail}"
                  alt="${titleArticle}"
                  class="rounded border"
                  style="width: 42px; height: 42px; object-fit: contain;"
                />
              </a>
            `
              : `
              <div
                class="d-flex align-items-center justify-content-center rounded bg-label-secondary"
                style="
                  width: 42px;
                  height: 42px;
                  font-size: 16px;
                  font-weight: 700;
                "
              >
                ${titleArticle.charAt(0).toUpperCase()}
              </div>
            `;

            return `
              <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                  ${thumbnailOutput}
                </div>
                <div class="d-flex align-items-center">
                  <span class="fw-medium text-primary cursor-pointer show-record mb-0" data-id="${row.id}" style=" line-height: 1;" />
                    ${titleArticle}
                  </span>
                </div>
              </div>
            `;
          }
        },
        {
          targets: 2,
          render: function (data, type, row) {
            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${data}</span>
                <span class="fw-medium">${row.location}</span>
              </div>
            `;
          }
        },
        {
          targets: 3,
          render: function (data, type, row) {
            const articleStatus = data === 'draft' ? 'DRAFT' : 'PUBLISHED',
              statusClass = articleStatus === 'DRAFT' ? 'bg-label-info' : 'bg-label-success';

            return '<span class="badge ' + statusClass + '">' + articleStatus + '</span>';
          }
        },
        {
          targets: 4,
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
          targets: 5,
          render: function (data, type, row) {
            const options = {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            };

            if (row.deleted_at !== null) {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.deleter}</span>
                  <span class="fw-medium">${new Date(row.deleted_at).toLocaleString('en-GB', options)}</span>
                </div>
              `;
            } else {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.editor}</span>
                  <span class="fw-medium">${new Date(data).toLocaleString('en-GB', options)}</span>
                </div>
              `;
            }
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
                <button class="btn btn-icon me-2 edit-record" data-id="${data}" data-bs-target="#modalArticle" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                placeholder: 'Search Article',
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
                    'data-bs-target': '#modalArticle'
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

  const formArticle = document.getElementById('formArticle'),
    fillTitle = formArticle.querySelector('#title'),
    projectDate = formArticle.querySelector('#project_at'),
    fillLocation = formArticle.querySelector('#location'),
    selectStatus = formArticle.querySelector('#status'),
    dropzoneElement = formArticle.querySelector('#articleDropzone'),
    btnSubmit = formArticle.querySelector('button[type="submit"]');

  let editingId = null,
    selectedThumbnail = null,
    removedImages = [];

  const quillToolbar = [['bold', 'italic', 'underline', 'strike'], [{ list: 'ordered' }]];
  const contentEditor = new Quill('#content-editor', {
    bounds: '#content-editor',
    placeholder: 'Type Something...',
    modules: {
      syntax: true,
      toolbar: quillToolbar
    },
    theme: 'snow'
  });
  const hiddenContent = document.getElementById('content');
  contentEditor.on('text-change', function () {
    hiddenContent.value = contentEditor.root.innerHTML;
  });

  initStatic($(selectStatus), {
    placeholder: 'Select an option',
    disableSearch: true,
    data: [
      { id: 'draft', text: 'Draft' },
      { id: 'published', text: 'Published' }
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

  initFP(projectDate);

  Dropzone.autoDiscover = false;

  const previewTemplate = `
  <div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message">
          <span data-dz-errormessage></span>
        </div>
        <div class="thumbnail-badge d-none">
          <span class="badge bg-primary"><i class="bx bxs-star me-1"></i>Thumbnail</span>
        </div>
        <div class="thumbnail-overlay">
          <button type="button" class="btn btn-sm btn-light set-thumbnail">Set As Thumbnail</button>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>
  `;

  const myDropzone = new Dropzone(dropzoneElement, {
    url: '#',
    autoProcessQueue: false,
    maxFiles: 5,
    maxFilesize: 4,
    acceptedFiles: '.png,.jpg,.jpeg,.webp',
    addRemoveLinks: true,
    previewTemplate: previewTemplate
  });

  myDropzone.on('addedfile', function (file) {
    const preview = file.previewElement;
    const button = preview.querySelector('.set-thumbnail');
    const badge = preview.querySelector('.thumbnail-badge');

    if (!editingId && myDropzone.files.length === 1) {
      badge.classList.remove('d-none');
      file.isThumbnail = true;
      selectedThumbnail = file;
    }

    button.addEventListener('click', function () {
      myDropzone.files.forEach(f => {
        f.isThumbnail = false;
        const el = f.previewElement.querySelector('.thumbnail-badge');

        el.classList.add('d-none');
      });

      file.isThumbnail = true;
      selectedThumbnail = file;

      badge.classList.remove('d-none');
    });
  });

  myDropzone.on('removedfile', function (file) {
    if (file.existing && file.id) {
      removedImages.push(file.id);
    }

    file.isThumbnail = false;

    if (file.previewElement) {
      const removedBadge = file.previewElement.querySelector('.thumbnail-badge');

      if (removedBadge) {
        removedBadge.classList.add('d-none');
      }
    }

    if (selectedThumbnail === file) {
      selectedThumbnail = null;

      myDropzone.files.forEach(f => {
        f.isThumbnail = false;

        const badge = f.previewElement?.querySelector('.thumbnail-badge');

        badge?.classList.add('d-none');
      });

      if (myDropzone.files.length > 0) {
        const firstFile = myDropzone.files[0];

        firstFile.isThumbnail = true;

        selectedThumbnail = firstFile;

        firstFile.previewElement.querySelector('.thumbnail-badge').classList.remove('d-none');
      }
    }
  });

  // create record
  $('.add-new').on('click', function () {
    modalTitle.html('Create New Article');
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
    modalTitle.html('Edit Existing Article');
    $(btnSubmit).html('Save');

    // get data
    $.get(`${baseUrl}articles/${id}/edit`, function (data) {
      editingId = id;
      fillTitle.value = data.title || '';
      contentEditor.root.innerHTML = data.content;
      fillLocation.value = data.location || '';
      projectDate._flatpickr.setDate(data.project_at || null);

      if (data.status) {
        $(selectStatus).val(data.status).trigger('change');
      }

      myDropzone.removeAllFiles(true);

      data.images.forEach((image, index) => {
        const mockFile = {
          name: image.file_name,
          size: image.file_size,
          accepted: true,
          existing: true,
          id: image.id
        };

        myDropzone.emit('addedfile', mockFile);

        myDropzone.emit('thumbnail', mockFile, `/storage/${image.file_path}`);

        myDropzone.emit('complete', mockFile);

        myDropzone.files.push(mockFile);

        mockFile.previewElement.querySelector('.dz-remove').dataset.id = image.id;

        if (image.is_primary) {
          mockFile.isThumbnail = true;
          selectedThumbnail = mockFile;

          mockFile.previewElement.querySelector('.thumbnail-badge').classList.remove('d-none');
        }
      });
    });
  });

  FormValidation.formValidation(formArticle, {
    fields: {
      title: {
        validators: {
          notEmpty: {
            message: 'Title is required'
          },
          stringLength: {
            min: 4,
            message: 'Title must be at least 4 characters long'
          }
        }
      },
      content: {
        validators: {
          callback: {
            message: 'Content is required',
            callback: function () {
              const text = contentEditor.getText().trim();

              return text.length > 0;
            }
          }
        }
      },
      project_at: {
        validators: {
          notEmpty: {
            message: 'Date must be selected'
          }
        }
      },
      location: {
        validators: {
          notEmpty: {
            message: 'Location is required'
          }
        }
      },
      status: {
        validators: {
          notEmpty: {
            message: 'Status must be selected'
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
    const formData = new FormData(formArticle);

    let url = editingId ? `${baseUrl}articles/${editingId}` : `${baseUrl}articles`;

    if (editingId) {
      formData.append('_method', 'PATCH');
    }

    removedImages.forEach((id, index) => {
      formData.append(`removed_images[${index}]`, id);
    });

    const activeFiles = myDropzone.files;

    if (activeFiles.length === 0) {
      showToast('info', 'At least one article image is required');

      return;
    }

    activeFiles.forEach((file, index) => {
      if (!file.existing) {
        formData.append(`images[${index}]`, file);
      }

      if (selectedThumbnail === file) {
        formData.append('thumbnail_index', index);
      }
    });

    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    $.ajax({
      data: formData,
      url: url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (res) {
        Loading.remove();
        dt_articles.draw(false);
        modalArticle.modal('hide');

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

  modalArticle.on('hidden.bs.modal', function () {
    formArticle.reset();
    editingId = null;
    $(formArticle).find('select').val('').trigger('change');
    projectDate._flatpickr.clear(false);

    myDropzone.removeAllFiles(true);
    myDropzone.files = [];
    $(dropzoneElement).find('.dz-preview').remove();
    selectedThumbnail = null;
    removedImages = [];

    contentEditor.setContents([]);
    hiddenContent.value = '';
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
          url: `${baseUrl}articles/${id}`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_articles.draw(false);
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
        showToast('info', 'The article is not deleted!');
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
          url: `${baseUrl}articles/${id}/restore`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_articles.draw(false);
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
        showToast('info', 'The article is not restored!');
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
          url: `${baseUrl}articles/${id}/force`,
          success: function (res) {
            Loading.remove();
            showToast(res.status, res.message);
            dt_articles.draw(false);
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
        showToast('info', 'The article is not deleted!');
      }
    });
  });

  const modalShow = $('#modalShow'),
    modalBody = modalShow.find('.modal-body');

  $(document).on('click', '.show-record', function () {
    const id = $(this).data('id');

    Loading.circle({
      backgroundColor: 'rgba(' + window.Helpers.getCssVar('black-rgb') + ', 0.7)',
      svgSize: '60px',
      svgColor: config.colors.white
    });

    $.get(`${baseUrl}articles/${id}/edit`, function (data) {
      let imagesHtml = `<div class="d-flex flex-wrap gap-3">`;

      data.images.forEach(image => {
        imagesHtml += `
          <a href="/storage/${image.file_path}" data-fancybox="article-images" class="d-block">
            <img src="/storage/${image.file_path}" class="rounded border" style=" width: 160px; height: 110px; object-fit: cover;" >
          </a>
        `;
      });
      imagesHtml += `
        </div>
      `;

      modalBody.html(`
        <div class="col-12 mb-3">
          <h3 class="mb-1">
            ${data.title}
          </h3>
          <div class="text-muted">
            ${data.location}
          </div>
        </div>

        <div class="col-12 mb-4">
          <div class="d-flex gap-2">
            <span class="badge bg-label-primary text-capitalize">
              ${data.status}
            </span>
            <span class="badge bg-label-secondary">
              ${new Intl.DateTimeFormat('en-GB', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
              }).format(new Date(data.project_at))}
            </span>
          </div>
        </div>

        <div class="col-12 mb-4">
          ${data.content}
        </div>
        <div class="col-12">
          <div class="row">
            ${imagesHtml}
          </div>
        </div>
      `);

      modalShow.modal('show');

      Loading.remove();
    });
  });

  modalShow.on('hidden.bs.modal', function () {
    modalBody.empty();
  });
});
