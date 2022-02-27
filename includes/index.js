/*  ==========================================
    GET INPUT AND UPLOAD LABEL ELEMENT
* ========================================== */
var imageFiles = Array();

/*  ==========================================
    SHOW UPLOADED IMAGE
* ========================================== */
function readURL(image) {
        let ulPrev = document.getElementById('fr-images-prev');
        let reader = new FileReader();

        reader.onload = function (e) {
            var li = document.createElement('li');
            var img = '<img src="'+e.target.result+'">';            
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
function submitData(postData,button) {
    //console.log(postData.getAll('post_tags'), postData.getAll('post_title'));
    jQuery.ajax({
 
        type: 'POST', 
        url: fr_crudajax.ajax_url, 
        data: postData,
        processData: false,
        contentType: false,
 
        success: function(data, textStatus, XMLHttpRequest) {
            removeAlert(button,true,data)
            resetvalues();
        },
 
        error: function(data, textStatus, errorThrown) {
            //alert(errorThrown);
            removeAlert(button,false,data);
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

jQuery(document).on('change','#upload', function () {
    var input = document.getElementById( 'upload' );
    mapImagesAndCreatePreview(input);
});

jQuery(document).on('click','#toggle-fr-form',function () {
    jQuery('#primaryPostForm').toggleClass('d-none');
    jQuery(this).toggleClass('form-is-active');
    jQuery(this).find('.fr-btn-icon:nth-child(2)').toggleClass('d-none');
    jQuery(this).find('.fr-btn-icon:first-child').toggleClass('d-none');
    jQuery(this).find('.fr-btn-text').text(function(i, text){
        return text === " Cancel" ? " Add Report" : " Cancel";
    })

})

jQuery(document).on('click', '#submit_post',function (e) {
    e.preventDefault();
    var Form = document.getElementById('primaryPostForm');
    var postData = new FormData(Form);
    imageFiles.map(val => {
        postData.append('images[]',val);
    })
    postData.append('action','fr_addpost');
    postData.delete('files[]');
    this.textContent = 'Uploading...';
    submitData(postData,this);
})

/*  ==========================================
    SHOW INITIAL LIST AND GRID CONTENT
* ========================================== */

/*  ==========================================
    LIST AND GRID CONTROL TAB
* ========================================== */
jQuery(document).on('click','#fr-content .nav-link',function(e){
    e.preventDefault();
    jQuery('.fr-content > div').toggleClass('d-none');
    jQuery('#fr-content .nav-link').toggleClass('active');
});

/*  ==========================================
    SELECT PRIMARY IMAGE
* ========================================== */

/*  ==========================================
    REMOVE IMAGE FROM PREVIEW IMAGE
* ========================================== */