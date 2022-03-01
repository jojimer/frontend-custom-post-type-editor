<!-- Create Post Form -->
<form class="px-4 mb-5" id="editReportForm" method="POST" enctype="multipart/form-data">
   <div class="row">
      <div id="fr-crud-response" class="my-3"></div>
      <div class="col-7">
            <div class="mb-3">
            <!-- field used for the title -->
            <label for="postTitle" class="form-label">Report Title</label>
            <input type="text" class="form-control required" name="post_title" id="postTitle" value="">
            </div>
            <div class="mb-3">
              <!-- Field for caption -->
              <label for="postCaption" class="form-label">Report Caption</label>
              <textarea class="form-control" name="post_caption" id="post-caption" cols="30" rows="10"></textarea>
            </div>
      </div>
      <div class="col-5">
         <div class="mb-3">
            <!-- field used for the tags-->
            <label for="postTags" class="form-label">Tags <small>(Separate Tags with commas)</small></label>
            <input type="text" name="post_tags" class="form-control required" id="postTags" value="">
         </div>
            <!-- Upload image input-->
            <label for="UploadImages" class="form-label">Upload Images</label>
            <div class="content-images-preview">
               <ul id="fr-images-prev"></ul>
            </div>
            <div id="fr-Upload" class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
               <input id="upload" type="file" class="form-control border-0" accept="image/png, image/gif, image/jpeg" multiple name="files[]">
               <label id="upload-label" for="upload" class="font-weight-light text-muted">Choose file</label>
               <div class="input-group-append">
                    <label for="upload" class="btn btn-light m-0 rounded-pill px-4"> <i class="fas fa-cloud-upload-alt mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted">Choose file</small></label>
               </div>
            </div>
      </div>
   </div>
   <!-- Security field -->
   <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
   <input id="post_author" type="hidden" name="author" value="<?php echo wp_get_current_user()->ID; ?>">
   <input id="post_ID" type="hidden" name="post_id" value="">
</form>