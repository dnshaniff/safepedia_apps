document.addEventListener("DOMContentLoaded",function(L){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}});const b=$(".datatables-brands"),g=$("#modalBrand"),p=g.find(".modal-title");let c;b&&(c=new DataTable(b,{processing:!0,serverSide:!0,ajax:{url:`${baseUrl}brands`},columns:[{data:"fake_id"},{data:"name"},{data:"created_at"},{data:"updated_at"},{data:"id"}],columnDefs:[{orderable:!1,targets:[0,1,2,3,-1]},{searchable:!0,targets:[1]},{targets:1,responsivePriority:1,render:function(a,o,t){let e=a,n=t.file_path?`${baseUrl}storage/${t.file_path}`:null;return`
              <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                  ${n?`
              <a href="${n}" data-fancybox="brand-${t.id}">
                <img
                  src="${n}"
                  alt="${e}"
                  class="rounded border"
                  style="width: 42px; height: 42px; object-fit: contain;"
                />
              </a>
            `:`
              <div
                class="d-flex align-items-center justify-content-center rounded bg-label-secondary"
                style="
                  width: 42px;
                  height: 42px;
                  font-size: 16px;
                  font-weight: 700;
                "
              >
                ${e.charAt(0).toUpperCase()}
              </div>
            `}
                </div>
                <div class="d-flex align-items-center">
                  <span class="fw-medium text-heading mb-0" style=" line-height: 1;" />
                    ${e}
                  </span>
                </div>
              </div>
            `}},{targets:2,render:function(a,o,t){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return`
              <div class="d-flex flex-column">
                <span class="text-muted">${t.creator}</span>
                <span class="fw-medium">${new Date(a).toLocaleString("en-GB",e)}</span>
              </div>
            `}},{targets:3,render:function(a,o,t){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return t.deleted_at!==null?`
                <div class="d-flex flex-column">
                  <span class="text-muted">${t.deleter}</span>
                  <span class="fw-medium">${new Date(t.deleted_at).toLocaleString("en-GB",e)}</span>
                </div>
              `:`
                <div class="d-flex flex-column">
                  <span class="text-muted">${t.editor}</span>
                  <span class="fw-medium">${new Date(a).toLocaleString("en-GB",e)}</span>
                </div>
              `}},{targets:-1,title:"Actions",render:function(a,o,t,e){return t.deleted_at!==null?`
                <span class="text-nowrap">
                  <button class="btn btn-icon me-2 restore-record" data-id="${a}">
                    <i class="bx bx-recycle"></i>
                  </button>
                  <button class="btn btn-icon force-record" data-id="${a}">
                    <i class="bx bx-trash"></i>
                  </button>
                </span>
              `:`
              <span class="text-nowrap">
                <button class="btn btn-icon me-2 edit-record" data-id="${a}" data-bs-target="#modalBrand" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-icon delete-record" data-id="${a}">
                  <i class="bx bx-trash-alt"></i>
                </button>
              </span>
            `}}],scrollCollapse:!0,fixedHeader:{header:!0,headerOffset:70},fixedColumns:{leftColumns:1},order:[[]],layout:{topStart:{rowClass:"row m-3 my-0 justify-content-between",features:[{pageLength:{menu:[10,25,50,100],text:"Show_MENU_ entries"}}]},topEnd:{features:[{search:{placeholder:"Search Brand",text:"_INPUT_"}},{buttons:[{text:"Create New",className:"add-new btn btn-primary mb-3 mb-md-0",attr:{"data-bs-toggle":"modal","data-bs-target":"#modalBrand"}}]}]},bottomStart:{rowClass:"row mx-3 justify-content-between",features:["info"]},bottomEnd:"paging"},language:{paginate:{next:'<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',previous:'<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',first:'<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',last:'<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'}},createdRow:function(a,o){o.deleted_at!==null&&$(a).addClass("bg-danger-subtle")}})),setTimeout(()=>{[{selector:".dt-buttons .btn",classToRemove:"btn-secondary"},{selector:".dt-search",classToAdd:"me-3"},{selector:".dt-search .form-control",classToRemove:"form-control-sm"},{selector:".dt-length",classToAdd:"mb-0 mb-md-5"},{selector:".dt-length .form-select",classToRemove:"form-select-sm"},{selector:".dt-buttons",classToAdd:"mb-0 w-auto"},{selector:".dt-layout-start",classToAdd:"mt-0 px-5"},{selector:".dt-layout-end",classToAdd:"justify-content-md-between justify-content-center d-flex",classToRemove:"justify-content-between d-md-flex"},{selector:".dt-layout-table",classToRemove:"row mt-2"},{selector:".dt-layout-full",classToRemove:"col-md col-12",classToAdd:"table-responsive"}].forEach(({selector:o,classToRemove:t,classToAdd:e})=>{document.querySelectorAll(o).forEach(n=>{t&&t.split(" ").forEach(s=>n.classList.remove(s)),e&&e.split(" ").forEach(s=>n.classList.add(s))})})},100);const r=document.getElementById("formBrand"),x=r.querySelector("#name"),h=r.querySelector("#file_upload"),u=r.querySelector("#logoPreviewWrapper"),m=r.querySelector("#logoPreview"),f=r.querySelector("#logoPreviewLink"),w=r.querySelector('button[type="submit"]');let d=null,l=null,i=null;h.addEventListener("change",function(a){const o=a.target.files[0];if(!o){i&&(m.src=i,f.href=i,u.classList.remove("d-none"));return}if(!o.type.startsWith("image/")){showToast("danger","Only image files are allowed"),h.value="";return}l&&URL.revokeObjectURL(l),l=URL.createObjectURL(o),m.src=l,f.href=l,u.classList.remove("d-none")}),$(".add-new").on("click",function(){p.html("Create New Brand"),d=null,$(w).html("Submit")}),$(document).on("click",".edit-record",function(){const a=$(this).data("id"),o=$(".dtr-bs-modal.show");o.length&&o.modal("hide"),p.html("Edit Existing Brand"),$(w).html("Save"),$.get(`${baseUrl}brands/${a}/edit`,function(t){d=a,x.value=t.name||"",i=t.file_path?`${baseUrl}storage/${t.file_path}`:null,i?(m.src=i,f.href=i,u.classList.remove("d-none")):(m.src="",f.href="",u.classList.add("d-none"))})}),FormValidation.formValidation(r,{fields:{name:{validators:{notEmpty:{message:"Brand name is required"},stringLength:{min:4,message:"Brand name must be at least 4 characters long"}}},file_upload:{validators:{file:{extension:"png",type:"image/png",maxSize:4*1024*1024,message:"Please select a valid image file (png) less than 4MB"}}}},plugins:{trigger:new FormValidation.plugins.Trigger,bootstrap5:new FormValidation.plugins.Bootstrap5({eleValidClass:"",rowSelector:".mb-3"}),submitButton:new FormValidation.plugins.SubmitButton,autoFocus:new FormValidation.plugins.AutoFocus},init:a=>{a.on("plugins.message.placed",o=>{o.element.parentElement.classList.contains("input-group")&&o.element.parentElement.insertAdjacentElement("afterend",o.messageElement)})}}).on("core.form.valid",function(){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white});const a=new FormData(r);d&&a.append("_method","PATCH");let o=d?`${baseUrl}brands/${d}`:`${baseUrl}brands`;$.ajax({data:a,url:o,type:"POST",processData:!1,contentType:!1,success:function(t){Loading.remove(),c.draw(!1),g.modal("hide"),showToast(t.status,t.message)},error:function(t,e,n){let s=t.responseJSON;if(s){if(Loading.remove(),showToast(s.status,s.message),s.errors)for(let v in s.errors)s.errors[v].forEach(y=>{console.log(`${v}: ${y}`)})}else Loading.remove(),showToast("danger","An unexpected error occurred")}})}),g.on("hidden.bs.modal",function(){r.reset(),d=null,l&&URL.revokeObjectURL(l),m.src="",f.href="",u.classList.add("d-none"),l=null,i=null}),$(document).on("click",".delete-record",function(){var a=$(this).data("id"),o=$(".dtr-bs-modal.show");o.length&&o.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(t){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),t.value?$.ajax({method:"DELETE",url:`${baseUrl}brands/${a}`,success:function(e){Loading.remove(),showToast(e.status,e.message),c.draw(!1)},error:function(e){var n,s;Loading.remove(),showToast(((n=e.responseJSON)==null?void 0:n.status)||"danger",((s=e.responseJSON)==null?void 0:s.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The brand is not deleted!"))})}),$(document).on("click",".restore-record",function(){var a=$(this).data("id"),o=$(".dtr-bs-modal.show");o.length&&o.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, restore it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(t){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),t.value?$.ajax({method:"POST",url:`${baseUrl}brands/${a}/restore`,success:function(e){Loading.remove(),showToast(e.status,e.message),c.draw(!1)},error:function(e){var n,s;Loading.remove(),showToast(((n=e.responseJSON)==null?void 0:n.status)||"danger",((s=e.responseJSON)==null?void 0:s.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The brand is not restored!"))})}),$(document).on("click",".force-record",function(){var a=$(this).data("id"),o=$(".dtr-bs-modal.show");o.length&&o.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, permanent delete!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(t){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),t.value?$.ajax({method:"DELETE",url:`${baseUrl}brands/${a}/force`,success:function(e){Loading.remove(),showToast(e.status,e.message),c.draw(!1)},error:function(e){var n,s;Loading.remove(),showToast(((n=e.responseJSON)==null?void 0:n.status)||"danger",((s=e.responseJSON)==null?void 0:s.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The brand is not deleted!"))})})});
