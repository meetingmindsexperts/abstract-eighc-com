<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ url('mme-Logo.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <title>Submit Video Abstract </title>
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
    button#submit{
        background-color: #061b4c;
    background-image: none;
    background: #061b4c;
    background: -webkit-linear-gradient();
    background: linear-gradient();
    border-radius: 5px;
        padding: 10px 30px;
    }
</style>

<body>

<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="full-image">
                <img class="desk" src="{{ url('eighc-eaheader-650.jpg') }}" alt="">
                <img class="mbl" src="{{ url('eighc-eaheader-650.jpg') }}" alt="">
            </div>
            <div class="card">

                <div class="card-header text-center">
                    <h5>Upload Video File</h5>
                </div>

                <div class="card-body">
       @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                <form method="get" action="{{ url('upload') }}" id="form">
                  <!--  <div class="form-group">-->
                  <!--  <label for="email">Submitting Author Name<span style="color:red;">*</span>:</label>-->
                  <!--  <input type="text" class="form-control" name="name" placeholder="Enter Submitting Author Name" id="name" required>-->
                  <!--</div>-->
                              <div class="form-group">
                    <label for="pwd">Email<span style="color:red;">*</span>:</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter the email you used during the abstract submission" id="email" required>
                  </div>
                  <div class="form-group">
                    <label for="pwd">Paper Number<span style="color:red;">*</span>:</label>
                    <input type="number" name="paper_number" class="form-control" placeholder="Enter Paper Number" id="code" required>
                  </div>
                <!--<input type="hidden" name="document" id="document" required>-->
                <!--<div class="card-footer p-4" >-->
                <!--    <video id="videoPreview" src="" controls style="width: 100%; height: auto"></video>-->
                <!--</div>-->
                <button id="submit" class="btn btn-success" type="submit">Next</button>
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
