
var selectedFile = null;
var selectedFileName = null
var currentUpload = null;

/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
(function($) {
    $.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = $.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ? function(s) { return s; } : decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };
})(jQuery);



var timeDiff  =  {
    setStartTime:function (){
        d = new Date();
        time  = d.getTime();
    },

    getDiff:function (){
        d = new Date();
        return (d.getTime()-time);
    }
}

function setLanguage(language) {
    jQuery.cookie('django_language', language);
    window.location.reload();
}

function uploadProgress(evt) {

    if (evt.lengthComputable) {

        var percent = Math.round(evt.loaded * 100 / evt.total);

        $('#upload-progress').css('width', percent + '%');
    }
}

function uploadComplete(evt) {

    $('#upload-progress').css('width', '100%');

    var response_type = this.getResponseHeader('Content-Type');

    if (response_type == 'application/json') {

        var response = $.parseJSON(this.responseText);

        /* wait 1 second before redirection to allow the progress bar
          reach 100% and create a better psychological effect */

        window.setTimeout(function(response){

            window.location.href = '/file/' + response.sha256 + '/analysis/' + response.timestamp + '/';

        }, 1000, response);
    }
    else {

        /* wait 1 second before changing the dialog to allow the progress bar
          reach 100% and create a better psychological effect */

        window.setTimeout(function(response){

            $('#dlg-upload-progress').html(response);
            $('#dlg-upload-progress').show();

        }, 1000, this.responseText );
    }
}

function uploadFailed(evt) {
    alert("There was an error attempting to upload the file.");
}


function cancelUpload(){

    if (currentUpload) {
        currentUpload.abort();
    }
}

function uploadFile(filename, file, sha256) {


    /* send a GET request first to ask if the file
       already exists and get the upload URL */

    var data = {};

    $.ajax({
        type: 'GET',
        async: true,
        url: '/file/upload/',
        dataType: 'json',
        data: data,
        context: {'filename': filename},
        cache: false,
        success: function(response){

            /* if browser have FormData support send the file via XMLHttpRequest
               with upload progress bar, else send it the standard way */

            if ( file && window.FormData) {

                var fd = new FormData();

                fd.append('file', file);
                fd.append('ajax','true');

                /* Due to a bug in AppEngine (http://code.google.com/p/googleappengine/issues/detail?id=5175)
                  we have to send the IP of the user as a param in this post. The server is sending us the IP
                  it saw in the GET request, so we can send it back in the POST. This workaround should be removed
                  when the issue is solved */

                fd.append('remote_addr', response.remote_addr);

                if (sha256)
                    fd.append('sha256', sha256);

                currentUpload = new XMLHttpRequest();

                currentUpload.upload.addEventListener('progress', uploadProgress, false);
                currentUpload.addEventListener('load', uploadComplete, false);
                currentUpload.addEventListener('error', uploadFailed, false);
                currentUpload.open('POST', response.upload_url);
                currentUpload.send(fd);

            } else {

                $('#frm-file').attr('action', response.upload_url);
                $('#frm-file').submit();

                /*  in IE 7 animated GIFs freeze immediately after submit, we need this hack to reload the GIF and make the
                    animation work during the file upload */

                $('#gif-upload-progress-bar span').html('<img style="display:block" src="/static/img/bar.gif">');
            }
        }
    }); // $.ajax()
}


function canUserWorker() {

    if (window.FileReader && window.Worker) {

        var major_version = parseInt(jQuery.browser.version, 10);

        if (jQuery.browser.opera)
          return false;

        if (jQuery.browser.mozilla && major_version >= 8)
          return true;

        if (jQuery.browser.webkit && major_version >= 535)
          return true;
    }

    return false;
}

function scanFile(evt) {

    if (!selectedFileName) {
        return;
    }

    if (selectedFile && selectedFile.size > 32*1024*1024) {
        $('#dlg-file-too-large').modal('show');
        return;
    }

    $('#dlg-upload-progress').modal('show');

    /* if browser has support for File API and Web Workers, calculate hash before upload
       in a separate thread. Opera supports both, but its postMessage implementation doesn't
       allow to pass a File object as a parameter, so we can't send the file to the worker  */

    if (canUserWorker()){

        $('#upload-progress-bar').hide();
        $('#hash-progress').css('width','0%');
        $('#hash-progress-bar').show();

        worker = new Worker('/static/js/sha256.js');

        worker.onmessage = function(e) {

            if (e.data.progress) {

                $('#hash-progress').css('width', e.data.progress + '%');
            }
            else {

                $('#hash-progress-bar').hide();
                $('#upload-progress').css('width','0%');
                $('#upload-progress-bar').show();

                uploadFile(selectedFileName, selectedFile, e.data.sha256);
            }
        };

        worker.postMessage({file: selectedFile});
    }
    else {

        $('#gif-upload-progress-bar').show();
        uploadFile(selectedFileName, null, null);

    }
}

function selectFile(evt) {

    /* update global selectedFile variable */

    if (evt.target.files) {
        selectedFile = evt.target.files[0];
    }

    /* when the hidden file input box changes its content,
       update also the visible GUI element  */

    var pieces = $(this).val().split(/(\\|\/)/g);
    selectedFileName = pieces[pieces.length-1];

    $('#file-name').text(selectedFileName);
    $("#btn-scan-file").focus();
}




jQuery(document).ready(function(){


    $('.action').click(function(event) {
        var id = $(this).attr('id');
        $('input#' + id).select();
    });

    /* file scanning */
    $('#btn-scan-file').click(scanFile);
    $('input#file-choosen').change(selectFile);

    $('.btn.dialog').click(function () {
        $(this).siblings('.loading').show();
        $(this).siblings('.btn').addClass('disabled');
        $(this).addClass('disabled');
    });

});
