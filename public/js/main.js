$.pjax.defaults.timeout = 0;
$(document).pjax("a", "#pjax-container");
$(document).on('pjax:start', function(e) {
    $("#ajax-loader").css("visibility", "visible");
});

$(document).on('pjax:end', function(e) {
    $("#ajax-loader").css("visibility", "hidden");
});
$(document).on('submit', 'form[data-pjax]', function(e) {
    $.pjax.submit(e, '#pjax-container');
});