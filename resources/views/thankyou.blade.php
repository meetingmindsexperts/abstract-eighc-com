<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <title>Video Abstract </title>
</head>

<style>
    .card-footer, .progress {
        display: none;
    }
    .full-image img {
        width:100%;
    }
    .full-image .mbl {
        display:none;
    }
    .full-image .desk {
        display:block;
    }
    
    @media(max-width:480px){
           .full-image .mbl {
        display:block;
    }
    .full-image .desk {
        display:none;
    }
    }
    
    input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}
.btn-success {
    color: #fff;
    background-color: #051948;
    border-color: #051948;
}
    
</style>

<body>

<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="full-image">
                <img class="desk" src="{{ url('banner.jpg') }}" alt="">
                <img class="mbl" src="{{ url('mbl-banner.jpg') }}" alt="">
            </div>
            <div class="card">

                <div class="card-header text-center">
                    <h5>Upload Video File</h5>
                </div>

                <div class="card-body">


                <form method="get" action="{{ url('upload') }}" id="form">
                 <div class="text-center">
                     
<p>Your file has been successfully uploaded. We will review the recording and in case of any adjustments required, you will be notified accordingly.</p>

 

<p>For any further assistance, please contact us at <a href="mailto:info@eghs-acg2023.com">info@eghs-acg2023.com</a></p>
                 <a href="https://abstract.eghs-acg2023.com" class="btn btn-success">Upload Another</a>
                 </div>
               
                </form>

        </div>
            </div>
        </div>
    </div>
</div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Resumable JS -->
<script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
<script>
   $(document).ready (function () {
    $("#submit").on('click',function(){
        if($("#name").val() == ''){
        alert("Please Enter Name");
        }else if($("#code").val() == ''){
        alert("Please Code Name");
    }else if($("#document").val() == ''){
        alert("Please Upload Video");
    } else {
        $("#form").submit();
    }
    });
  });


</script>
<script type="text/javascript">
    let browseFile = $('#browseFile');
    let resumable = new Resumable({
        target: '{{ route('files.upload.large') }}',
        query:{_token:'{{ csrf_token() }}'} ,// CSRF token
        fileType: ['mp4','mkv','flv'],
        headers: {
            'Accept' : 'application/json'
        },
        testChunks: false,
        throttleProgressCallbacks: 5,
    });

    resumable.assignBrowse(browseFile[0]);

    resumable.on('fileAdded', function (file) { // trigger when file picked
        showProgress();
        resumable.upload() // to actually start uploading.
    });

    resumable.on('fileProgress', function (file) { // trigger when file progress update
        updateProgress(Math.floor(file.progress() * 100));
    });

    resumable.on('fileSuccess', function (file, response) { // trigger when file upload complete
        response = JSON.parse(response)
        $('#videoPreview').attr('src', response.path);
        // $('.card-footer').show();
        $("#document").val(response.path);
        $("#form").show();
        $('.progress').find('.progress-bar').css('background-color', 'green');
    });

    resumable.on('fileError', function (file, response) { // trigger when there is any error
        alert('file uploading error.')
    });


    let progress = $('.progress');
    function showProgress() {
        progress.find('.progress-bar').css('width', '0%');
        progress.find('.progress-bar').html('0%');
        progress.find('.progress-bar').removeClass('bg-success');
        progress.show();
    }

    function updateProgress(value) {
        progress.find('.progress-bar').css('width', `${value}%`)
        progress.find('.progress-bar').html(`${value}%`);
    }

    function hideProgress() {
        progress.hide();
    }
</script>
</body>
</html>
