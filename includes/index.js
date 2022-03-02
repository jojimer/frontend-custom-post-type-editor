// To DO List
/*  ==========================================
    SELECT PRIMARY IMAGE
* ========================================== */

/*  ==========================================
    GET INPUT AND UPLOAD LABEL ELEMENT
* ========================================== */
let imageFiles = Array();
let deletingImageIDs = Array();
const $ = window.jQuery;

/*  ==========================================
    SHOW UPLOADED IMAGE
* ========================================== */
function readURL(image,name,formID) {
        let ulPrev = document.querySelector(formID+" #fr-images-prev");
        let reader = new FileReader();

        reader.onload = function (e) {
            let li = document.createElement('li');
            let img = '<button type="button" data-name="'+name+'" class="close text-danger" aria-label="Close"><span aria-hidden="true">×</span></button>';
            img += '<img src="'+e.target.result+'" title="'+name+'">';
            li.innerHTML = img;
            ulPrev.append(li);
        };
        reader.readAsDataURL(image);
}

/*  ==========================================
    SHOW IMAGE PREVIEW
* ========================================== */
$(document).on('change','#upload', function () {
    mapImagesAndCreatePreview('upload','#primaryPostForm');
});

function mapImagesAndCreatePreview(inputID,formID) {
    let input = document.getElementById(inputID);
    let fileNames;
    let infoArea = document.querySelector(formID+' #upload-label');
    if (input.files && input.files[0]) {
        for (var key in input.files) {
          if (input.files.hasOwnProperty(key)) {
            readURL(input.files[key],input.files[key].name,formID);
            fileNames = (key < 1) ? input.files[key].name+', ' : fileNames+' '+input.files[key].name+', ';
            imageFiles = [...imageFiles,input.files[key]];
          }
        }

        //Show all file names
        fileNames = (input.files.length > 1) ? 'Files: '+fileNames : 'File: '+fileNames;
        if(infoArea.textContent === 'Choose file') {
            infoArea.textContent  = (fileNames.length > 35) ? fileNames.slice(0,35)+'...' : fileNames;
        }
    }
}

/*  ==========================================
    REMOVE IMAGE FROM PREVIEW
* ========================================== */
$(document).on('click','#primaryPostForm #fr-images-prev li button',function(){
    let input = document.getElementById( 'upload' );
    let name = $(this).data('name');
    let li = $(this).parents('li');
    li.remove();
    imageFiles.filter(function(value, index, arr){ 
        if(value.name == name) imageFiles.splice(index,1); 
    });
});

/*  ==========================================
    AJAX SUBMIT DATA
* ========================================== */
function submitData(postData,callback) {
    $.ajax({
 
        type: 'POST', 
        url: fr_crudajax.ajax_url,
        data: postData,
        processData: false,
        contentType: false,
 
        success: function(data, textStatus, XMLHttpRequest) {
            callback(data,true);
            //console.log(data, textStatus, XMLHttpRequest)
        },
 
        error: function(data, textStatus, errorThrown) {
            callback(data,false);
            //console.log(data, textStatus, errorThrown)
        }
 
    });
}

function resetvalues(id){
    let form = document.getElementById(id);
    let infoArea = form.querySelector( '#upload-label' );
    let ulPrev = form.querySelector('#fr-images-prev');
    let allInputs = form.querySelectorAll('.form-control:not(input[type=file])');
    for(let i=0; allInputs.length > i; i++){
        allInputs[i].value = "";
    }
    imageFiles = Array();
    allInputs.value = "";
    infoArea.textContent = "Choose file";
    ulPrev.innerHTML = "";
}

function removeAlert(button,status,data){
    var id = document.getElementById('fr-crud-response');
    var alertStatus = (status) ? "success" : "danger";
    id.innerHTML = '<div class="alert alert-'+alertStatus+'" role="alert">'+data+'</div>';
    button.text('Post Report');
    setTimeout(function(){
        id.innerHTML = "";
    },2500);
}

$(document).on('click','#toggle-fr-form',function () {
    $('#primaryPostForm').toggleClass('d-none');
    $(this).toggleClass('form-is-active');
    $(this).find('.fr-btn-icon:nth-child(2)').toggleClass('d-none');
    $(this).find('.fr-btn-icon:first-child').toggleClass('d-none');
    $(this).find('.fr-btn-text').text(function(i, text){
        return text === " Cancel" ? " Add Report" : " Cancel";
    })

})

$(document).on('click', '#submit_post',function (e) {
    e.preventDefault();
    var Form = document.getElementById('primaryPostForm');
    var postData = new FormData(Form);
    imageFiles.map(val => {
        postData.append('images[]',val);
    })
    postData.append('action','fr_request');
    postData.append('action_type','post');
    postData.delete('files[]');
    this.textContent = 'Uploading...';
    submitData(postData,function(data,result){
        removeAlert($('#submit_post'),result,data);
        resetvalues('primaryPostForm');
    });
})

/*  ==========================================
    LIST AND GRID CONTROL TAB
* ========================================== */
$(document).on('click','#fr-content .nav-link',function(e){
    e.preventDefault();
    $('.fr-content > div').toggleClass('d-none');
    $('#fr-content .nav-link').toggleClass('active');
});

/*  ==========================================
    EDIT FIELD REPORT
* ========================================== */
$(document).on('click','span.fr-edit',function(){
    let id = $(this).data('post-id');
    getReportToEdit(id,function(data){
        let report = JSON.parse(data)
        $('#editReportForm input[name=post_title]').val(report.title);
        $('#editReportForm textarea[name=post_caption]').val(report.excerpt);
        $('#editReportForm').attr('data-post-id',report.ID);
        $('#editReportForm input[name=post_tags]').val(report.tags.map(val => {return val}));
        showEditPreviewImages(report.images);
    });
});

$(document).on('keyup','#editReportForm input[name=post_title],#editReportForm textarea[name=post_caption],#editReportForm input[name=post_tags]',function(){
    $('#nothingChange').val(1);
})

function getReportToEdit(id,callback){
    let postData = new FormData();

    postData.append('action','fr_request');
    postData.append('action_type','get');
    postData.append('postID',id);

    submitData(postData,function(data,result){
        if(result){
            callback(data.slice(0, -1));
        }
    });
}

/*  ==========================================
    UPLOAD EDIT IMAGE PREVIEW
* ========================================== */
$(document).on('change','#upload-update', function () {
    mapImagesAndCreatePreview('upload-update','#editReportForm');
});

/*  ==========================================
    SHOW EDIT FIELD REPORT PREVIEW IMAGES
* ========================================== */
function showEditPreviewImages(images){
    let ulPrev = $('#editReportForm #fr-images-prev');

    images.map((val,i) => {
        let li = document.createElement('li');
        let img = '<button type="button" class="close text-danger" data-img-index="'+i+'" aria-label="Close"><span aria-hidden="true">×</span></button>';
        img += '<img data-img-id="'+val.id+'" src="'+val.thumbnail+'">';            
        li.innerHTML = img;
        ulPrev.append(li);
    })
}

/*  ==========================================
    DELETE EDIT FIELD REPORT PREVIEW IMAGES
* ========================================== */
$(document).on('click','#editReportForm #fr-images-prev li button',function(){
    let index = $(this).data('img-index');
    let li = $(this).parents('li');
    li.remove();
    deletingImageIDs.push(index+1);
});

function resetEditForm(){
    deletingImageIDs = Array();
    imageFiles = Array();
    $('#submitUpdate').text('Updated');
}

/*  ==========================================
    SUBMIT EDIT FIELD REPORT UPDATE
* ========================================== */
$(document).on('click','#submitUpdate',function(e){
    e.preventDefault();
    let Form = document.getElementById('editReportForm');
    let id = document.querySelector('#editReportForm').dataset.postId;
    let postData = new FormData(Form);
    imageFiles.map(val => {
        postData.append('images[]',val);
    })
    postData.append('action','fr_request');
    postData.append('action_type','update');
    postData.append('delete_images',deletingImageIDs);
    postData.delete('files[]');
    postData.append('postID',id);
    this.textContent = 'Updating...';
    submitData(postData,function(data,result){
        if(result){
            resetEditForm();
        }else{
            alert('Something wen\'t wrong while updating, please try again.')
        }
        
        setTimeout(function(){            
            $('#submitUpdate').text('Update Report');
        },2000)
    });
})

$(document).on('click','#editReport .modal-header button.close',function(){
    resetvalues('editReportForm');
    $('input#nothingChange').val(1);
})

/*  ==========================================
    DELETE FIELD REPORT
* ========================================== */
$(document).on('click','span.fr-delete',function(){
    let id = $(this).data('post-id');
    let title = $(this).data('post-title');
    document.querySelector('#submitDelete').dataset.postId = id;
    $('#deleteReport .modal-body').html('<p class="h5">Are you sure you want to delete?</p> <p class="h6">"'+title+'"</p>');
});

$(document).on('click','#submitDelete',function(){
    let id = document.querySelector('#submitDelete').dataset.postId;
    let postData = new FormData();

    postData.append('action','fr_request');
    postData.append('action_type','delete');
    postData.append('postID',id);

    console.log(id);

    $(this).text('Deleting...');

    submitData(postData,function(data,result){
        if(result){
            $('#submitDelete').text('Deleted');
            //alert('Report is successfully deleted');
            resetDeleteModal($('#submitDelete'),id);
        }else{
            alert('Something wen\'t wrong!');
            resetDeleteModal($('#submitDelete'));
        }
    });
})

function resetDeleteModal(button,id = false){
    if(id){        
        $('#deleteReport').modal('toggle');
        $('.report'+id).remove();

        let reportRemaining = document.querySelectorAll('.report-item');
        let nothingToShow = '<div class="col-12 pt-5"><p class="h2 text-center my-5">Nothing to show!</p></div>';
        if(reportRemaining.length === 0) {
            $('#fr-list').append(nothingToShow);
            $('#fr-grid').append(nothingToShow);
        }
    }

    button.text('Yes');
}