$.pjax.defaults.timeout = 0;

$(document).pjax("a", "#pjax-container");

$(document).on('pjax:start', function(e) {
    $('#loading').fadeTo('fast', 1).addClass('active');
});

$(document).on('pjax:error', function(event, response) {
    var parse = $.pjax.options.success;
    parse(response.responseText, response.status, response);
    event.preventDefault();
});

$(document).on('pjax:end', function(e) {
    $('#loading').fadeTo('normal', 0, function() {
        $(this).removeClass('active');
    });
});

$(document).on('submit', 'form[data-pjax]', function(e) {
    $.pjax.submit(e, '#pjax-container', {
        data: new FormData(e.currentTarget),
        cache: false,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function(e) {
                    var percentage = event.loaded / event.total * 100;
                    console.log(percentage);
                }, false);
            }
            return xhr;
        }
    });
});

$(document).ready(function() {
    $("#nav-toggle").click(function() {
        $("#main-navigation").slideToggle(200);
    });
});