/*  ==========================================
    GET INPUT AND UPLOAD LABEL ELEMENT
* ========================================== */
var input = document.getElementById( 'upload' );
var infoArea = document.getElementById( 'upload-label' );
var ulPrev = document.getElementById('fr-images-prev');
var imageFiles = Array();

/*  ==========================================
    SHOW UPLOADED IMAGE
* ========================================== */
function readURL(image) {
        var reader = new FileReader();

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

jQuery('#upload').on('change', function () {
    mapImagesAndCreatePreview(input);
});

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
    SELECT PRIMARY IMAGE
* ========================================== */

/*  ==========================================
    REMOVE IMAGE FROM PREVIEW IMAGE
* ========================================== */