<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SVG TO SASS</title>
    <style>
        #editor {
            width: 100%;
            height: 400px;
        }
        #result{
            width: 100%;
            height: 400px;
        }
        #form{
            width: 100%;
            min-height: 400px;
            border: dashed 3px #204d74;
            border-radius: 5px;
        }


    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


    <link rel="stylesheet" href="lib/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <script src="lib/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="lib/dropzone.css">
    <script src="lib/dropzone.js" type="text/javascript" charset="utf-8"></script>


</head>
<body>
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <h3>Here is the Sass template for the file conversion:</h3>
        <span>You can use the following variable @@_IMAGE_NAME_@@, @@_IMAGE_DATA_@@</span>
    </div>
    <div class="col-md-5">
        <h3>Here is file uploader:</h3>
        The names of your svg files is going to be use as the class name
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <div id="editor" class="form-control">
.@@_IMAGE_NAME_@@ {
    @extend %@@_IMAGE_NAME_@@;
}

%@@_IMAGE_NAME_@@{
    background-image: url(@@_IMAGE_DATA_@@);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
    width: 100%;
    height: 100%;
}
</div>
    </div>
    <div class="col-md-5">
        <div id="form" class="dropzone">
            <div class="dz-message"><br>
                <h3>Drop files here or click to upload.</h3>
                <span class="note">(You can only upload SVG file.)</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h3>Here is your sass result:</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="well" id="result"></div>
    </div>
</div>

<script src="lib/ace-1.2.8/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="lib/ace-1.2.8/src-min-noconflict/theme-github.js" type="text/javascript" charset="utf-8"></script>
<script src="lib/ace-1.2.8/src-min-noconflict/mode-sass.js" type="text/javascript" charset="utf-8"></script>
<script>

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/github");
    var SassMode = ace.require("ace/mode/sass").Mode;
    editor.session.setMode(new SassMode());

    var result = ace.edit("result");
    result.setTheme("ace/theme/github");
    result.session.setMode(new SassMode());
    result.setReadOnly(true);

    var dropzone = $("#form").dropzone({
        url: "/fileUpload.php",
        maxFiles: 100,
        paramName: 'photo',
        addRemoveLinks: true,
        sending: function(file, xhr, formData) {
            formData.append('config', editor.getValue());
            formData.append('file', 'file');
        },
        init: function() {
            this.on("success", function(file, response) {
                div = document.getElementById('result');
                $.each(response, function( index, value ) {
                    result.setValue(result.getValue()+ value.sass);
                });
            });
        },
        uploadMultiple: true,
        acceptedFiles: '.svg'
    });

</script>
</body>
</html>