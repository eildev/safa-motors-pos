 <!-- Modal -->
 <div class="modal fade" id="exampleModalLongScollable" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
     aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalScrollableTitle">Add Supplier Info</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <form id="signupForm" class="supplierForm row">
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Supplier Name <span
                                 class="text-danger">*</span></label>
                         <input id="defaultconfig" class="form-control supplier_name" maxlength="255" name="name"
                             type="text" onkeyup="errorRemove(this);">
                         <span class="text-danger supplier_name_error"></span>
                     </div>
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Phone Nnumber <span
                                 class="text-danger">*</span></label>
                         <input id="defaultconfig" class="form-control phone" maxlength="39" name="phone"
                             type="tel" onkeyup="errorRemove(this);">
                         <span class="text-danger phone_error"></span>
                     </div>
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Email</label>
                         <input id="defaultconfig" class="form-control email" maxlength="39" name="email"
                             type="email" onkeyup="errorRemove(this);">
                         <span class="text-danger email_error"></span>
                     </div>
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Address</label>
                         <input id="defaultconfig" class="form-control address" maxlength="39" name="address"
                             type="text" onkeyup="errorRemove(this);">
                         <span class="text-danger address_error"></span>
                     </div>
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Bussiness Name</label>
                         <input id="defaultconfig" class="form-control business_name" name="business_name"
                             type="text" onkeyup="errorRemove(this);">
                         <span class="text-danger business_name_error"></span>
                     </div>
                     <div class="mb-3 col-md-6">
                         <label for="name" class="form-label">Supplier Due (সাপ্লায়ার আপানার
                             থেকে পাবে)</label>
                         <input id="defaultconfig" class="form-control due_balance" maxlength="39" name="due_balance"
                             type="number" onkeyup="errorRemove(this);">
                         <span class="text-danger due_balance_error"></span>
                     </div>
                     <input type="hidden" name="supplier_type" value="wholesale">
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-primary save_supplier">Save</button>
             </div>
         </div>
     </div>
 </div>
