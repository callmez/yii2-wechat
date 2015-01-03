jQuery(function($) {

    /**
     * 切换
     *
     * data-switch="name"
     * data-closest="selector"
     * data-value="value"
     *
     * data-switch-target="name"
     * data-value="value"
     */
    $('[data-switch]').each(function() {
        var _this = $(this);
        var name = _this.data('switch');
        if (!name) {
            return;
        }
        var target = $('[data-switch-target="' + name + '"]');
        var closest = _this.data('closest') || target;
        var closestFunction = _this.data('closest') ? 'closest' : 'constructor';
        var selector;
        var processor = function() {
            var val = _this.filter(selector).val() || $(selector, _this).val();
            target
                [closestFunction](closest)
                .hide()
                .end()
                .filter('[data-value="' + val + '"]')
                [closestFunction](closest)
                .show();
        };
        var tag = _this.attr('type') || this.tagName;
        switch(tag.toLowerCase()) {
            case 'select':
                selector = '> option:selected';
                _this.change(processor).change();
            case 'checkbox':
            case 'radio':
                selector = ':checked';
                _this.click(processor).filter(':checked').click();
        }
    });
});