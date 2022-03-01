/*  ==========================================
    GET INPUT AND UPLOAD LABEL ELEMENT
* ========================================== */
let imageFiles = Array();
const $ = window.jQuery;

/*  ==========================================
    SHOW UPLOADED IMAGE
* ========================================== */
function readURL(image) {
        let ulPrev = document.getElementById('fr-images-prev');
        let reader = new FileReader();

        reader.onload = function (e) {
            let li = document.createElement('li');
            let img = '<img src="'+e.target.result+'">';            
            li.innerHTML = img;
            ulPrev.append(li);
        };
        reader.readAsDataURL(image);
}

/*  ==========================================
    SHOW IMAGE PREVIEW
* ========================================== */

function mapImagesAndCreatePreview(input) {
    let fileNames;
    let infoArea = document.getElementById( 'upload-label' );
    if (input.files && input.files[0]) {
        for (var key in input.files) {
          if (input.files.hasOwnProperty(key)) {
            readURL(input.files[key]);
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

function resetvalues(){
    let form = document.getElementById('primaryPostForm');
    let infoArea = document.getElementById( 'upload-label' );
    let ulPrev = document.getElementById('fr-images-prev');
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
    button.textContent = 'Post Report';
    setTimeout(function(){
        id.innerHTML = "";
    },2500);
}

$(document).on('change','#upload', function () {
    var input = document.getElementById( 'upload' );
    mapImagesAndCreatePreview(input);
});

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
        removeAlert(this,result,data);
        resetvalues();
    });
})

// To DO 
/*  ==========================================
    SELECT PRIMARY IMAGE
* ========================================== */

/*  ==========================================
    REMOVE IMAGE FROM PREVIEW IMAGE
* ========================================== */


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

$(document).on('click','#submitUpdate',function(e){
    e.preventDefault();
    let Form = document.getElementById('editReportForm');
    let id = $('#editReportForm').data('post-id');
    let postData = new FormData(Form);
    imageFiles.map(val => {
        postData.append('images[]',val);
    })
    postData.append('action','fr_request');
    postData.append('action_type','update');
    postData.delete('files[]');
    postData.append('postID',id);
    this.textContent = 'Updating...';
    submitData(postData,function(data,result){
        //removeAlert(this,result,data);
        //resetvalues();
        console.log(data);
    });
})

/*  ==========================================
    SHOW EDIT FIELD REPORT PREVIEW IMAGES
* ========================================== */
function showEditPreviewImages(images){
    let ulPrev = $('#editReportForm #fr-images-prev');

    images.map(val => {
        let li = document.createElement('li');
        let img = '<img data-img-id="'+val.id+'" src="'+val.thumbnail+'">';            
        li.innerHTML = img;
        ulPrev.append(li);
    })    
}

/*  ==========================================
    DELETE FIELD REPORT
* ========================================== */
$(document).on('click','span.fr-delete',function(){
    let id = $(this).data('post-id');
    let title = $(this).data('post-title');
    $('.modal#deleteReport #submitDelete').attr('data-post-id',id);
    $('#deleteReport .modal-body').html('<p class="h5">Are you sure you want to delete?</p> <p class="h6">"'+title+'"</p>');
});

$(document).on('click','#submitDelete',function(){
    let id = $(this).data('post-id');
    let postData = new FormData();

    postData.append('action','fr_request');
    postData.append('action_type','delete');
    postData.append('postID',id);

    $(this).text('Deleting...');

    submitData(postData,function(data,result){
        if(result){
            $('#submitDelete').text('Deleted');
            alert('Report is successfully deleted');
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