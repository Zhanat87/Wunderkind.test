<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>async tasks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="container">
    <br/><br/><br/>
    <div class="row">
        <div class="col-md-2 col-md-offset-2">
            <button class="btn btn-success startMovingFilesB">
                start moving files
            </button>
            <br/>
            <div class="error hide errorDiv"></div>
            <div class="warning hide warningDiv"></div>
            <div class="fileCounter">count of moving files: <b></b></div>
        </div>
        <div class="col-md-5 hide progressBarDiv">
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span class="sr-only">0% Complete</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/sockjs-client/0.3.4/sockjs.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>