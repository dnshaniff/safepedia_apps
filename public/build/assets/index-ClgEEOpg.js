document.addEventListener("DOMContentLoaded",function(D){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}});const h=$(".datatables-articles"),b=$("#modalArticle"),v=b.find(".modal-title");let u;h&&(u=new DataTable(h,{processing:!0,serverSide:!0,ajax:{url:`${baseUrl}articles`},columns:[{data:"fake_id"},{data:"title"},{data:"project_at"},{data:"status"},{data:"created_at"},{data:"updated_at"},{data:"id"}],columnDefs:[{orderable:!1,targets:[0,1,2,3,4,5,-1]},{searchable:!0,targets:[1]},{targets:1,responsivePriority:1,render:function(t,s,a){let e=t,o=a.thumbnail??null;return`
              <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                  ${o?`
              <a href="${o}" data-fancybox="brand-${a.id}">
                <img
                  src="${o}"
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
                  <span class="fw-medium text-primary cursor-pointer show-record mb-0" data-id="${a.id}" style=" line-height: 1;" />
                    ${e}
                  </span>
                </div>
              </div>
            `}},{targets:2,render:function(t,s,a){return`
              <div class="d-flex flex-column">
                <span class="text-muted">${t}</span>
                <span class="fw-medium">${a.location}</span>
              </div>
            `}},{targets:3,render:function(t,s,a){const e=t==="draft"?"DRAFT":"PUBLISHED";return'<span class="badge '+(e==="DRAFT"?"bg-label-info":"bg-label-success")+'">'+e+"</span>"}},{targets:4,render:function(t,s,a){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return`
              <div class="d-flex flex-column">
                <span class="text-muted">${a.creator}</span>
                <span class="fw-medium">${new Date(t).toLocaleString("en-GB",e)}</span>
              </div>
            `}},{targets:5,render:function(t,s,a){const e={day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"};return a.deleted_at!==null?`
                <div class="d-flex flex-column">
                  <span class="text-muted">${a.deleter}</span>
                  <span class="fw-medium">${new Date(a.deleted_at).toLocaleString("en-GB",e)}</span>
                </div>
              `:`
                <div class="d-flex flex-column">
                  <span class="text-muted">${a.editor}</span>
                  <span class="fw-medium">${new Date(t).toLocaleString("en-GB",e)}</span>
                </div>
              `}},{targets:-1,title:"Actions",render:function(t,s,a,e){return a.deleted_at!==null?`
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
                <button class="btn btn-icon me-2 edit-record" data-id="${t}" data-bs-target="#modalArticle" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-icon delete-record" data-id="${t}">
                  <i class="bx bx-trash-alt"></i>
                </button>
              </span>
            `}}],scrollCollapse:!0,fixedHeader:{header:!0,headerOffset:70},fixedColumns:{leftColumns:1},order:[[]],layout:{topStart:{rowClass:"row m-3 my-0 justify-content-between",features:[{pageLength:{menu:[10,25,50,100],text:"Show_MENU_ entries"}}]},topEnd:{features:[{search:{placeholder:"Search Article",text:"_INPUT_"}},{buttons:[{text:"Create New",className:"add-new btn btn-primary mb-3 mb-md-0",attr:{"data-bs-toggle":"modal","data-bs-target":"#modalArticle"}}]}]},bottomStart:{rowClass:"row mx-3 justify-content-between",features:["info"]},bottomEnd:"paging"},language:{paginate:{next:'<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',previous:'<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',first:'<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',last:'<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'}},createdRow:function(t,s){s.deleted_at!==null&&$(t).addClass("bg-danger-subtle")}})),setTimeout(()=>{[{selector:".dt-buttons .btn",classToRemove:"btn-secondary"},{selector:".dt-search",classToAdd:"me-3"},{selector:".dt-search .form-control",classToRemove:"form-control-sm"},{selector:".dt-length",classToAdd:"mb-0 mb-md-5"},{selector:".dt-length .form-select",classToRemove:"form-select-sm"},{selector:".dt-buttons",classToAdd:"mb-0 w-auto"},{selector:".dt-layout-start",classToAdd:"mt-0 px-5"},{selector:".dt-layout-end",classToAdd:"justify-content-md-between justify-content-center d-flex",classToRemove:"justify-content-between d-md-flex"},{selector:".dt-layout-table",classToRemove:"row mt-2"},{selector:".dt-layout-full",classToRemove:"col-md col-12",classToAdd:"table-responsive"}].forEach(({selector:s,classToRemove:a,classToAdd:e})=>{document.querySelectorAll(s).forEach(o=>{a&&a.split(" ").forEach(n=>o.classList.remove(n)),e&&e.split(" ").forEach(n=>o.classList.add(n))})})},100);const r=document.getElementById("formArticle"),L=r.querySelector("#title"),g=r.querySelector("#project_at"),C=r.querySelector("#location"),w=r.querySelector("#status"),x=r.querySelector("#articleDropzone"),y=r.querySelector('button[type="submit"]');let d=null,l=null,f=[];const A=[["bold","italic","underline","strike"],[{list:"ordered"}]],m=new Quill("#content-editor",{bounds:"#content-editor",placeholder:"Type Something...",modules:{syntax:!0,toolbar:A},theme:"snow"}),T=document.getElementById("content");m.on("text-change",function(){T.value=m.root.innerHTML}),initStatic($(w),{placeholder:"Select an option",disableSearch:!0,data:[{id:"draft",text:"Draft"},{id:"published",text:"Published"}]});function z(t){t&&flatpickr(t,{altInput:!0,altFormat:"j F, Y",dateFormat:"Y-m-d",static:!0,allowInput:!1})}z(g),Dropzone.autoDiscover=!1;const k=`
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
  `,i=new Dropzone(x,{url:"#",autoProcessQueue:!1,maxFiles:5,maxFilesize:4,acceptedFiles:".png,.jpg,.jpeg,.webp",addRemoveLinks:!0,previewTemplate:k});i.on("addedfile",function(t){const s=t.previewElement,a=s.querySelector(".set-thumbnail"),e=s.querySelector(".thumbnail-badge");!d&&i.files.length===1&&(e.classList.remove("d-none"),t.isThumbnail=!0,l=t),a.addEventListener("click",function(){i.files.forEach(o=>{o.isThumbnail=!1,o.previewElement.querySelector(".thumbnail-badge").classList.add("d-none")}),t.isThumbnail=!0,l=t,e.classList.remove("d-none")})}),i.on("removedfile",function(t){if(t.existing&&t.id&&f.push(t.id),t.isThumbnail=!1,t.previewElement){const s=t.previewElement.querySelector(".thumbnail-badge");s&&s.classList.add("d-none")}if(l===t&&(l=null,i.files.forEach(s=>{var e;s.isThumbnail=!1;const a=(e=s.previewElement)==null?void 0:e.querySelector(".thumbnail-badge");a==null||a.classList.add("d-none")}),i.files.length>0)){const s=i.files[0];s.isThumbnail=!0,l=s,s.previewElement.querySelector(".thumbnail-badge").classList.remove("d-none")}}),$(".add-new").on("click",function(){v.html("Create New Article"),d=null,$(y).html("Submit")}),$(document).on("click",".edit-record",function(){const t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),v.html("Edit Existing Article"),$(y).html("Save"),$.get(`${baseUrl}articles/${t}/edit`,function(a){d=t,L.value=a.title||"",m.root.innerHTML=a.content,C.value=a.location||"",g._flatpickr.setDate(a.project_at||null),a.status&&$(w).val(a.status).trigger("change"),i.removeAllFiles(!0),a.images.forEach((e,o)=>{const n={name:e.file_name,size:e.file_size,accepted:!0,existing:!0,id:e.id};i.emit("addedfile",n),i.emit("thumbnail",n,`/storage/${e.file_path}`),i.emit("complete",n),i.files.push(n),n.previewElement.querySelector(".dz-remove").dataset.id=e.id,e.is_primary&&(n.isThumbnail=!0,l=n,n.previewElement.querySelector(".thumbnail-badge").classList.remove("d-none"))})})}),FormValidation.formValidation(r,{fields:{title:{validators:{notEmpty:{message:"Title is required"},stringLength:{min:4,message:"Title must be at least 4 characters long"}}},content:{validators:{callback:{message:"Content is required",callback:function(){return m.getText().trim().length>0}}}},project_at:{validators:{notEmpty:{message:"Date must be selected"}}},location:{validators:{notEmpty:{message:"Location is required"}}},status:{validators:{notEmpty:{message:"Status must be selected"}}}},plugins:{trigger:new FormValidation.plugins.Trigger,bootstrap5:new FormValidation.plugins.Bootstrap5({eleValidClass:"",rowSelector:".mb-3"}),submitButton:new FormValidation.plugins.SubmitButton,autoFocus:new FormValidation.plugins.AutoFocus},init:t=>{t.on("plugins.message.placed",s=>{s.element.parentElement.classList.contains("input-group")&&s.element.parentElement.insertAdjacentElement("afterend",s.messageElement)})}}).on("core.form.valid",function(){const t=new FormData(r);let s=d?`${baseUrl}articles/${d}`:`${baseUrl}articles`;d&&t.append("_method","PATCH"),f.forEach((e,o)=>{t.append(`removed_images[${o}]`,e)});const a=i.files;if(a.length===0){showToast("info","At least one article image is required");return}a.forEach((e,o)=>{e.existing||t.append(`images[${o}]`,e),l===e&&t.append("thumbnail_index",o)}),Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),$.ajax({data:t,url:s,type:"POST",processData:!1,contentType:!1,success:function(e){Loading.remove(),u.draw(!1),b.modal("hide"),showToast(e.status,e.message)},error:function(e,o,n){let c=e.responseJSON;if(c){if(Loading.remove(),showToast(c.status,c.message),c.errors)for(let E in c.errors)c.errors[E].forEach(_=>{console.log(`${E}: ${_}`)})}else Loading.remove(),showToast("danger","An unexpected error occurred")}})}),b.on("hidden.bs.modal",function(){r.reset(),d=null,$(r).find("select").val("").trigger("change"),g._flatpickr.clear(!1),i.removeAllFiles(!0),i.files=[],$(x).find(".dz-preview").remove(),l=null,f=[],m.setContents([]),T.value=""}),$(document).on("click",".delete-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(a){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),a.value?$.ajax({method:"DELETE",url:`${baseUrl}articles/${t}`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var o,n;Loading.remove(),showToast(((o=e.responseJSON)==null?void 0:o.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The article is not deleted!"))})}),$(document).on("click",".restore-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, restore it!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(a){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),a.value?$.ajax({method:"POST",url:`${baseUrl}articles/${t}/restore`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var o,n;Loading.remove(),showToast(((o=e.responseJSON)==null?void 0:o.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The article is not restored!"))})}),$(document).on("click",".force-record",function(){var t=$(this).data("id"),s=$(".dtr-bs-modal.show");s.length&&s.modal("hide"),Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, permanent delete!",customClass:{confirmButton:"btn btn-primary me-3",cancelButton:"btn btn-label-secondary"},buttonsStyling:!1}).then(function(a){Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),a.value?$.ajax({method:"DELETE",url:`${baseUrl}articles/${t}/force`,success:function(e){Loading.remove(),showToast(e.status,e.message),u.draw(!1)},error:function(e){var o,n;Loading.remove(),showToast(((o=e.responseJSON)==null?void 0:o.status)||"danger",((n=e.responseJSON)==null?void 0:n.message)||"An unexpected error occurred")}}):(Loading.remove(),showToast("info","The article is not deleted!"))})});const p=$("#modalShow"),S=p.find(".modal-body");$(document).on("click",".show-record",function(){const t=$(this).data("id");Loading.circle({backgroundColor:"rgba("+window.Helpers.getCssVar("black-rgb")+", 0.7)",svgSize:"60px",svgColor:config.colors.white}),$.get(`${baseUrl}articles/${t}/edit`,function(s){let a='<div class="d-flex flex-wrap gap-3">';s.images.forEach(e=>{a+=`
          <a href="/storage/${e.file_path}" data-fancybox="article-images" class="d-block">
            <img src="/storage/${e.file_path}" class="rounded border" style=" width: 160px; height: 110px; object-fit: cover;" >
          </a>
        `}),a+=`
        </div>
      `,S.html(`
        <div class="col-12 mb-3">
          <h3 class="mb-1">
            ${s.title}
          </h3>
          <div class="text-muted">
            ${s.location}
          </div>
        </div>

        <div class="col-12 mb-4">
          <div class="d-flex gap-2">
            <span class="badge bg-label-primary text-capitalize">
              ${s.status}
            </span>
            <span class="badge bg-label-secondary">
              ${new Intl.DateTimeFormat("en-GB",{day:"2-digit",month:"long",year:"numeric"}).format(new Date(s.project_at))}
            </span>
          </div>
        </div>

        <div class="col-12 mb-4">
          ${s.content}
        </div>
        <div class="col-12">
          <div class="row">
            ${a}
          </div>
        </div>
      `),p.modal("show"),Loading.remove()})}),p.on("hidden.bs.modal",function(){S.empty()})});
