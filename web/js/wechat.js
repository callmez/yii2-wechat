+function ($) {

    // 自动为远程modal添加框
    var modalClickEvent = function (e) {
        var $this = $(this);
        var targetId = $this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''));
        var $target = $() // strip for ie7
        if (!$target.length && targetId[0] == '#') {
            targetId = targetId.substring(1, targetId.length);
            var template = '' +
    '<div class="modal fade" id="' + targetId + '" tabindex="-1" role="dialog" aria-labelledby="' + targetId + 'Label">' +
        '<div class="modal-dialog" role="document">' +
            '<div class="modal-content"></div>' +
        '</div>' +
    '</div>';
            $('body').append(template);
        }
        offModalClickEvent.call(this);
    };
    var offModalClickEvent = function() {
        $(this).off('click', modalClickEvent);
    };
    $('html').on('click', '[data-toggle="modal"]', modalClickEvent);

}(jQuery);