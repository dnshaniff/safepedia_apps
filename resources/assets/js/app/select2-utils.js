(function ($) {
  // helpers
  window.getDropdownParent = function ($el) {
    const $m = $el.closest('.modal');
    return $m.length ? $m : $el.parent();
  };
  window.ensureWrappedOnce = function ($el) {
    if (!$el.parent().hasClass('position-relative')) {
      $el.wrap('<div class="position-relative"></div>');
    }
  };
  window.isInitialized = function ($el) {
    return $el.data('select2') != null;
  };
  window.destroySelect2 = function ($el) {
    if (isInitialized($el)) $el.select2('destroy');
  };
  window.setValue = function ($el, value, text) {
    let item = null;
    if (typeof value === 'object' && value?.id != null && value?.text != null) {
      item = value;
    } else if (value != null && text != null) {
      item = { id: value, text };
    }
    if (!item) return;
    const exists = $el.find(`option[value="${item.id}"]`).length > 0;
    if (!exists) $el.append(new Option(item.text, item.id, true, true));
    $el.val(String(item.id)).trigger('change');
  };

  // initStatic
  window.initStatic = function (
    $el,
    { placeholder = 'Select...', allowClear = true, disableSearch = false, data = [], value = null } = {}
  ) {
    if (!$el?.length) return;
    destroySelect2($el);
    let html = '<option></option>';
    for (const it of data) html += `<option value="${String(it.id)}">${it.text}</option>`;
    $el.html(html);
    ensureWrappedOnce($el);
    $el.select2({
      placeholder,
      allowClear,
      dropdownParent: getDropdownParent($el),
      minimumResultsForSearch: disableSearch ? Infinity : 0
    });
    if (value != null) {
      if (typeof value === 'object') setValue($el, value);
      else $el.val(String(value)).trigger('change');
    }
  };

  // initDropdownPaged
  window.initDropdownPaged = function (
    $el,
    { url, placeholder = 'Select...', perPage = 15, hideSearch = false, delay = 200 } = {}
  ) {
    if (!$el?.length || !url) return;
    destroySelect2($el);
    ensureWrappedOnce($el);
    $el.select2({
      placeholder,
      dropdownParent: getDropdownParent($el),
      minimumResultsForSearch: hideSearch ? Infinity : 0,
      minimumInputLength: 0,
      ajax: {
        url,
        dataType: 'json',
        delay,
        data: p => ({ q: p.term || '', page: p.page || 1, per: perPage }),
        processResults: resp => ({ results: resp.results || [], pagination: { more: !!resp.more } }),
        cache: true
      },
      language: { searching: () => 'Loading...', noResults: () => 'No data' },
      templateResult: i => (i.loading ? 'Loading...' : i.text || ''),
      templateSelection: i => i.text || i.id
    });
  };

  // initAjax
  window.initAjax = function (
    $el,
    {
      url,
      placeholder = 'Type to search...',
      allowClear = true,
      minInput = 2,
      delay = 250,
      mapParams = p => ({ q: p.term || '', page: p.page || 1 }),
      mapResult = it => it,
      value = null,
      findByIdUrl = null
    } = {}
  ) {
    if (!$el?.length || !url) return;
    destroySelect2($el);
    ensureWrappedOnce($el);
    $el.select2({
      placeholder,
      allowClear,
      dropdownParent: getDropdownParent($el),
      minimumInputLength: minInput,
      ajax: {
        url,
        dataType: 'json',
        delay,
        data: mapParams,
        processResults: resp => ({ results: (resp.results || []).map(mapResult), pagination: { more: !!resp.more } }),
        cache: true
      },
      language: {
        inputTooShort: () => `Type at least ${minInput} characters`,
        searching: () => 'Searching...',
        noResults: () => 'No results'
      },
      templateResult: i => (i.loading ? 'Searching...' : i.text || ''),
      templateSelection: i => i.text || i.id
    });
    if (value) {
      if (typeof value === 'object' && value.id != null) {
        setValue($el, value);
      } else if (findByIdUrl) {
        $.getJSON(findByIdUrl, { id: value })
          .done(d => setValue($el, d?.id != null ? d : d?.data))
          .fail(e => console.error('Select2 preload failed:', e));
      }
    }
  };

  // showToast
  window.showToast = function (status, message) {
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
  };
})(window.jQuery);
