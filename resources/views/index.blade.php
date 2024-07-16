<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    button#browseFile {
    background: #061a48;
}
    @media(max-width:480px){
           .full-image .mbl {
        display:block;
    }
    .full-image .desk {
        display:none;
    }
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
                    <h5>Upload Video File
                    </h5>
                </div>

                <div class="card-body">

                    @if(session('success'))
                    <div class="alert alert-success">Abstract
                        Submitted Successfully</div>
                    @endif
                    <table>
                        <tr>
                            <td>Email: </td>
                            <td>{{ $email }}</td>
                        </tr>
                        <tr>
                            <td>Paper Number: </td>
                            <td>{{ $paper }}</td>
                        </tr>
                    </table>
                    <br/>
                      <label for="email">Please Select File to upload only: (MP4,FLV,MKV)<span style="color:red;">*</span></label>
                    <div id="upload-container" class="">
                        <button id="browseFile" class="btn btn-primary">Brows File</button>
                    </div>
                    <div class="progress mt-3" style="height: 25px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">75%</div>
                    </div>


                <form method="post" action="{{ url('store') }}" id="form" style="display: none;">
               {{ csrf_field() }}
                  <!--  <div class="form-group">-->
                  <!--  <label for="email">Name<span style="color:red;">*</span>:</label>-->
                 
                  <!--</div>-->
                  <!--<div class="form-group">-->
                  <!--  <label for="pwd">Code<span style="color:red;">*</span>:</label>-->
                  <!--  <input type="number" name="code" class="form-control" value="@if(isset($_GET['name'])){{ $_GET['code'] }}@endif" placeholder="Enter Code" id="code" required readonly>-->
                  <!--</div>-->
                  <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="paper" value="{{ $paper }}">
                <input type="hidden" name="document" id="document" required>
                <div class="card-footer p-4" >
                    <video id="videoPreview" src="" controls style="width: 100%; height: auto"></video>
                </div>
                <button id="submit" class="btn btn-success" type="submit">Submit</button>
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
        maxFileSize: 501 * 1024 * 1024,
        throttleProgressCallbacks: 5
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
        $('.card-footer').show();
        $("#document").val(response.path);
        $( "button#submit").click();
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
