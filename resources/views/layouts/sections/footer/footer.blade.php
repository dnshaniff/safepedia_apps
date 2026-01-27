@php
    $containerFooter =
        isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
            ? 'container-xxl'
            : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                ©
                <script>
                    document.write(new Date().getFullYear())
                </script>, Develop by <a
                    href="https://www.larksuite.com/invitation/page/add_contact/?token=3c7j25d3-57b6-4216-aac2-decdf2m1f4j1"
                    target="_blank" class="footer-link d-none d-sm-inline-block">Denis (IT)</a>
            </div>
            {{-- <div class="d-none d-lg-inline-block">
        <a href="http://10.4.1.116/ticket_mis/" target="_blank" class="footer-link d-none d-sm-inline-block">Support</a>
      </div> --}}
        </div>
    </div>
</footer>
{{-- <footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="mb-2 mb-md-0">
        &#169;
        <script>
        document.write(new Date().getFullYear())
        </script>
        , made with ❤️ by <a href="{{ !empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '' }}"
          target="_blank"
          class="footer-link">{{ !empty(config('variables.creatorName')) ? config('variables.creatorName') : '' }}</a>
      </div>
      <div class="d-none d-lg-inline-block">
        <a href="{{ config('variables.adminTemplates') ? config('variables.adminTemplates') : '#' }}"
          class="footer-link me-4" target="_blank">Admin Templates</a>
        <a href="{{ config('variables.licenseUrl') ? config('variables.licenseUrl') : '#' }}" class="footer-link me-4"
          target="_blank">License</a>
        <a href="{{ config('variables.bootstrapDashboard') ? config('variables.bootstrapDashboard') : '#' }}"
          target="_blank" class="footer-link me-4">Bootstrap Dashboard</a>
        <a href="{{ config('variables.documentation') ? config('variables.documentation') . '/laravel-introduction.html' : '#' }}"
          target="_blank" class="footer-link me-4">Documentation</a>
        <a href="{{ config('variables.support') ? config('variables.support') : '#' }}" target="_blank"
          class="footer-link d-none d-sm-inline-block">Support</a>
      </div>
    </div>
  </div>
</footer> --}}
<!--/ Footer-->
