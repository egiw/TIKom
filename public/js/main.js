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
    $.pjax.submit(e, '#pjax-container');
});

$(document).ready(function() {
    $("#nav-toggle").click(function() {
        $("#main-navigation").slideToggle(200);
    });
});