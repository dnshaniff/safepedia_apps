document.addEventListener("DOMContentLoaded",function(A){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}});const f=$(".datatables-products"),b=$("#modalProduct"),h=b.find(".modal-title");let u;f&&(u=new DataTable(f,{processing:!0,serverSide:!0,ajax:{url:`${baseUrl}products`},columns:[{data:"fake_id"},{data:"name"},{data:"brand"},{data:"status"},{data:"created_at"},{data:"updated_at"},{data:"id"}],columnDefs:[{orderable:!1,targets:[0,1,2,3,4,5,-1]},{searchable:!0,targets:[1]},{targets:1,responsivePriority:1,render:function(t,s,o){let e=t,a=o.thumbnail??null;return`
              <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                  ${a?`
              <a href="${a}" data-fancybox="brand-${o.id}">
                <img
                  src="${a}"
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
                  <span class="fw-medium text-primary cursor-pointer show-record mb-0" data-id="${o.id}" style="line-height: 1;" />
                    ${e}
                  </span>
                </div>
              </div>
            `}},{targets:3,render:function(t,s,o){const e=t==="active"?"ACTIVE":"INACTIVE";return'<span class="badge '+(e==="ACTIVE"?"bg-label-success":"bg-label-danger")+'">'+e+"</span>"}},{targets:4,render:function(t,s,o){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return`
              <div class="d-flex flex-column">
                <span class="text-muted">${o.creator}</span>
                <span class="fw-medium">${new Date(t).toLocaleString("en-GB",e)}</span>
              </div>
            `}},{targets:5,render:function(t,s,o){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return o.deleted_at!==null?`
                <div class="d-flex flex-column">
                  <span class="text-muted">${o.deleter}</span>
                  <span class="fw-medium">${new Date(o.deleted_at).toLocaleString("en-GB",e)}</span>
                </div>
              `:`
                <div class="d-flex flex-column">
                  <span class="text-muted">${o.editor}</span>
                  <span class="fw-medium">${new Date(t).toLocaleString("en-GB",e)}</span>
                </div>
              `}},{targets:-1,title:"Actions",render:function(t,s,o,e){return o.deleted_at!==null?`
                <span class="text-nowrap">
                  <button class="btn btn-icon me-2 restore-record" data-id="${t}">
                    <i class="bx bx-recycle"></i>
                  </button>
                  <button class="btn btn-icon force-record" data-id="${t}">
                    <i class="bx bx-trash"></i>
                  </button>
                </span>
              `:`
              <span class="text-nowrap">
                <button class="btn btn-icon me-2 edit-record" data-id="${t}" data-bs-target="#modalProduct" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-icon delete-record" data-id="${t}">
                  <i class="bx bx-trash-alt"></i>
                </button>
              </span>
            `}}],scrollCollapse:!0,fixedHeader:{header:!0,headerOffset:70},fixedColumns:{leftColumns:1},order:[[]],layout:{topStart:{rowClass:"row m-3 my-0 justify-content-between",features:[{pageLength:{menu:[10,25,50,100],text:"Show_MENU_ entries"}}]},topEnd:{features:[{search:{placeholder:"Search Product",text:"_INPUT_"}},{buttons:[{text:"Create New",className:"add-new btn btn-primary mb-3 mb-md-0",attr:{"data-bs-toggle":"modal","data-bs-target":"#modalProduct"}}]}]},bottomStart:{rowClass:"row mx-3 justify-content-between",features:["info"]},bottomEnd:"paging"},language:{paginate:{next:'<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',previous:'<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',first:'<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',last:'<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'}},createdRow:function(t,s){s.deleted_at!==null&&$(t).addClass("bg-danger-subtle")}})),setTimeout(()=>{[{selector:".dt-buttons .btn",classToRemove:"btn-secondary"},{selector:".dt-search",classToAdd:"me-3"},{selector:".dt-search .form-control",classToRemove:"form-control-sm"},{selector:".dt-length",classToAdd:"mb-0 mb-md-5"},{selector:".dt-length .form-select",classToRemove:"form-select-sm"},{selector:".dt-buttons",classToAdd:"mb-0 w-auto"},{selector:".dt-layout-start",classToAdd:"mt-0 px-5"},{selector:".dt-layout-end",classToAdd:"justify-content-md-between justify-content-center d-flex",classToRemove:"justify-content-between d-md-flex"},{selector:".dt-layout-table",classToRemove:"row mt-2"},{selector:".dt-layout-full",classToRemove:"col-md col-12",classToAdd:"table-responsive"}].forEach(({selector:s,classToRemove:o,classToAdd:e})=>{document.querySelectorAll(s).forEach(a=>{o&&o.split(" ").forEach(n=>a.classList.remove(n)),e&&e.split(" ").forEach(n=>a.classList.add(n))})})},100);const i=document.getElementById("formProduct"),C=i.querySelector("#name"),v=i.querySelector("#brand_id"),w=i.querySelector("#status"),x=i.querySelector("#productDropzone"),y=i.querySelector('button[type="submit"]');let d=null,l=null,p=[];const L=[["bold","italic","underline","strike"],[{list:"ordered"}]],m=new Quill("#description-editor",{bounds:"#description-editor",placeholder:"Type Something...",modules:{syntax:!0,toolbar:L},theme:"snow"}),T=document.getElementById("description");m.on("text-change",function(){T.value=m.root.innerHTML}),initDropdownPaged($(v),{url:"/brands/select",placeholder:"Select an option",perPage:10,hideSearch:!1}),initStatic($(w),{placeholder:"Select an option",disableSearch:!0,data:[{id:"active",text:"Active"},{id:"inactive",text:"Inactive"}]}),Dropzone.autoDiscover=!1;const z=`
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
  `,r=new Dropzone(x,{url:"#",autoProcessQueue:!1,maxFiles:5,maxFilesize:4,acceptedFiles:".png,.jpg,.jpeg,.webp",addRemoveLinks:!0,previewTemplate:z});r.on("addedfile",function(t){const s=t.previewElement,o=s.querySelector(".set-thumbnail"),e=s.querySelector(".thumbnail-badge");!d&&r.files.length===1&&(e.classList.remove("d-none"),t.isThumbnail=!0,l=t),o.addEventListener("click",function(){r.files.forEach(a=>{a.isThumbnail=!1,a.previewElement.querySelector(".thumbnail-badge").classList.add("d-none")}),t.isThumbnail=!0,l=t,e.classList.remove("d-none")})}),r.on("removedfile",function(t){if(t.existing&&t.id&&p.push(t.id),t.isThumbnail=!1,t.previewElement){const s=t.previewElement.querySelector(".thumbnail-badge");s&&s.classList.add("d-none")}if(l===t&&(l=null,r.files.forEach(s=>{var e;s.isThumbnail=!1;const o=(e=s.previewElement)==null?void 0:e.querySelector(".thumbnail-badge");o==null||o.classList.add("d-none")}),r.files.length>0)){const s=r.files[0];s.isThumbnail=!0,l=s,s.previewElement.querySelector(".thumbnail-badge").classList.remove("d-none")}}),$(".add-new").on("click",function(){h.html("Create New Product"),d=null,$(y).html("Submit")}),$(document).on("click",".edit-record",function(){const t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),h.html("Edit Existing Product"),$(y).html("Save"),$.get(`${baseUrl}products/${t}/edit`,function(o){if(d=t,C.value=o.name||"",m.root.innerHTML=o.description,o.brand){const e=new Option(o.brand.name,o.brand.id,!0,!0);$(v).append(e).trigger("change")}o.status&&$(w).val(o.status).trigger("change"),r.removeAllFiles(!0),o.images.forEach((e,a)=>{const n={name:e.file_name,size:e.file_size,accepted:!0,existing:!0,id:e.id};r.emit("addedfile",n),r.emit("thumbnail",n,`/storage/${e.file_path}`),r.emit("complete",n),r.files.push(n),n.previewElement.querySelector(".dz-remove").dataset.id=e.id,e.is_primary&&(n.isThumbnail=!0,l=n,n.previewElement.querySelector(".thumbnail-badge").classList.remove("d-none"))})})}),FormValidation.formValidation(i,{fields:{name:{validators:{notEmpty:{message:"Product name is required"},stringLength:{min:4,message:"Product name must be at least 4 characters long"}}},description:{validators:{callback:{message:"Content is required",callback:function(){return contentEditor.getText().trim().length>0}}}},brand_id:{validators:{notEmpty:{message:"Brand must be selected"}}},status:{validators:{notEmpty:{message:"Status must be selected"}}}},plugins:{trigger:new FormValidation.plugins.Trigger,bootstrap5:new FormValidation.plugins.Bootstrap5({eleValidClass:"",rowSelector:".mb-3"}),submitButton:new FormValidation.plugins.SubmitButton,autoFocus:new FormValidation.plugins.AutoFocus},init:t=>{t.on("plugins.message.placed",s=>{s.element.parentElement.classList.contains("input-group")&&s.element.parentElement.insertAdjacentElement("afterend",s.messageElement)})}}).on("core.form.valid",function(){const t=new FormData(i);let s=d?`${baseUrl}products/${d}`:`${baseUrl}products`;d&&t.append("_method","PATCH"),p.forEach((e,a)=>{t.append(`removed_images[${a}]`,e)});const o=r.files;if(o.length===0){showToast("info","At least one product image is required");return}o.forEach((e,a)=>{e.existing||t.append(`images[${a}]`,e),l===e&&t.append("thumbnail_index",a)}),Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),$.ajax({data:t,url:s,type:"POST",processData:!1,contentType:!1,success:function(e){Loading.remove(),u.draw(!1),b.modal("hide"),showToast(e.status,e.message)},error:function(e,a,n){let c=e.responseJSON;if(c){if(Loading.remove(),showToast(c.status,c.message),c.errors)for(let E in c.errors)c.errors[E].forEach(k=>{console.log(`${E}: ${k}`)})}else Loading.remove(),showToast("danger","An unexpected error occurred")}})}),b.on("hidden.bs.modal",function(){i.reset(),d=null,$(i).find("select").val("").trigger("change"),m.setContents([]),T.value="",r.removeAllFiles(!0),r.files=[],$(x).find(".dz-preview").remove(),l=null,p=[]}),$(document).on("click",".delete-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(o){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),o.value?$.ajax({method:"DELETE",url:`${baseUrl}products/${t}`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var a,n;Loading.remove(),showToast(((a=e.responseJSON)==null?void 0:a.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The product is not deleted!"))})}),$(document).on("click",".restore-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, restore it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(o){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),o.value?$.ajax({method:"POST",url:`${baseUrl}products/${t}/restore`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var a,n;Loading.remove(),showToast(((a=e.responseJSON)==null?void 0:a.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The product is not restored!"))})}),$(document).on("click",".force-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, permanent delete!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(o){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),o.value?$.ajax({method:"DELETE",url:`${baseUrl}products/${t}/force`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var a,n;Loading.remove(),showToast(((a=e.responseJSON)==null?void 0:a.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The product is not deleted!"))})});const g=$("#modalShow"),S=g.find(".modal-body");$(document).on("click",".show-record",function(){const t=$(this).data("id");Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),$.get(`${baseUrl}products/${t}/edit`,function(s){let o='<div class="d-flex flex-wrap gap-3">';s.images.forEach(e=>{o+=`
          <a href="/storage/${e.file_path}" data-fancybox="article-images" class="d-block">
            <img src="/storage/${e.file_path}" class="rounded border" style=" width: 160px; height: 110px; object-fit: cover;" >
          </a>
        `}),o+=`
        </div>
      `,S.html(`
        <div class="col-12 mb-3">
          <h3 class="mb-1">
            ${s.name}
          </h3>
        </div>

        <div class="col-12 mb-4">
          <div class="d-flex gap-2">
            <span class="badge bg-label-primary">
              ${s.brand.name}
            </span>
            <span class="badge bg-label-secondary text-capitalize">
              ${s.status}
            </span>
          </div>
        </div>

        <div class="col-12 mb-4">
          ${s.description}
        </div>
        <div class="col-12">
          <div class="row">
            ${o}
          </div>
        </div>
      `),g.modal("show"),Loading.remove()})}),g.on("hidden.bs.modal",function(){S.empty()})});
