$.pjax.defaults.timeout = 0;

$(document).pjax("a", "#pjax-container");

$(document).on('pjax:start', function(e) {
    $("#ajax-loader").css("visibility", "visible");
});

$(document).on('pjax:error', function(event, response) {
    var parse = $.pjax.options.success;
    parse(response.responseText, response.status, response);
    event.preventDefault();
});

$(document).on('pjax:end', function(e) {
    $("#ajax-loader").css("visibility", "hidden");
});

$(document).on('submit', 'form[data-pjax]', function(e) {
    $.pjax.submit(e, '#pjax-container');
});