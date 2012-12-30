$.fn.getData = function() {

    var data = {};

    form = $(this).serializeArray();

    for (var i = form.length; i--;) {

        var name = form[i].name;
        var value = form[i].value;
        var index = name.indexOf('[]');

        if (index > -1) {
            name = name.substring(0, index);
            if (!(name in data)) {
                data[name] = [];
            }
            data[name].push(value);
        }
        else {
            data[name] = value;
        }
    }

    return data;
}


$.ajaxSetup({
     beforeSend: function(xhr, settings) {
         function getCookie(name) {
             var cookieValue = null;
             if (document.cookie && document.cookie != '') {
                 var cookies = document.cookie.split(';');
                 for (var i = 0; i < cookies.length; i++) {
                     var cookie = jQuery.trim(cookies[i]);
                     // Does this cookie string begin with the name we want?
                 if (cookie.substring(0, name.length + 1) == (name + '=')) {
                     cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                     break;
                 }
             }
         }
         return cookieValue;
         }
         if (!(/^http:.*/.test(settings.url) || /^https:.*/.test(settings.url))) {
             // Only send the token to relative URLs i.e. locally.
             xhr.setRequestHeader("X-CSRFToken", getCookie('csrftoken'));
         }
     }
});


$(document).ajaxSend(function(event, xhr, settings) {

    function getCookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }

    function sameOrigin(url) {
        // url could be relative or scheme relative or absolute
        var host = document.location.host; // host + port
        var protocol = document.location.protocol;
        var sr_origin = '//' + host;
        var origin = protocol + sr_origin;
        // Allow absolute or scheme relative URLs to same origin
        return (url == origin || url.slice(0, origin.length + 1) == origin + '/') ||
            (url == sr_origin || url.slice(0, sr_origin.length + 1) == sr_origin + '/') ||
            // or any other URL that isn't scheme relative or absolute i.e relative.
            !(/^(\/\/|http:|https:).*/.test(url));
    }

    function safeMethod(method) {
        return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
    }

    if (!safeMethod(settings.type) && sameOrigin(settings.url)) {
        xhr.setRequestHeader("X-CSRFToken", getCookie('csrftoken'));
    }
});


function cookiesEnabled()
{
    var enabled = (navigator.cookieEnabled) ? true : false;

    if (typeof navigator.cookieEnabled == "undefined" && !enabled)
    {
        document.cookie="testcookie";
        enabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;
    }

    return (enabled);
}

$(document).ready(function(){

    if (!cookiesEnabled()) {
        $('#cookies-disabled-alert').show();
    }

    $('.contact').text('contact' + '@' + 'virustotal.com');

    $('#mnu-user-name').click(function (e) {
        var $li = $(this).parent("li").toggleClass('open');
        return false;
    });

    $(document).delegate('a.cancel', 'click', function(event) {

        $(this).closest('.modal').modal('hide');
        event.preventDefault();

    });
    
    $(document).delegate('a.close', 'click', function(event) {

        $(this).closest('.modal').modal('hide');
        event.preventDefault();

    });
    
    

    $(document).delegate('#lnk-password-reset', 'click', function(event) {

        $('#dlg-signin').modal('hide');

        /* we store the original HTML code of the password reset dialog as data asociated
           to its DOM node, check if a backed up code exists and restore it, else back it up */

        var backup = $('#dlg-password-reset').data('backup');

        if (backup) {

            $('#dlg-password-reset').html(backup);

        } else {

            $('#dlg-password-reset').data('backup', $('#dlg-password-reset').html());
        }

        $('#dlg-password-reset').modal('show');
        event.preventDefault();
    });

    $("(span|a)[rel=popover]").popover({
        offset: 10,
        html: true
    }).click(function(e) {
        e.preventDefault()
    });

});

$(window).unload(function(){
/*
    This is just to force Firefox reloading the page when the browser
    back button is clicked, instead of showing the cached page. Just setting
    this empty handler Firefox doesn't cache the page, don't ask why.
*/
})
