(function ($){
    $(document).ready( function () {
        $('.berocket_compare_products_styler .colorpicker_field').each(function (i,o){
            $(o).css('backgroundColor', '#'+$(o).data('color'));
            $(o).colpick({
                layout: 'hex',
                submit: 0,
                color: '#'+$(o).data('color'),
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css('backgroundColor', '#'+hex).next().val(hex);
                }
            })
        });
        $(document).on('click', '.berocket_compare_products_styler .theme_default', function (event) {
            event.preventDefault();
            var data = $(this).prev().data('default');
            $(this).prev().prev().css('backgroundColor', '#' + data).colpickSetColor('#' + data);
            $(this).prev().val(data);
        });

        $(document).on('click', '.berocket_compare_products_styler .all_theme_default', function (event) {
            event.preventDefault();
            $table = $(this).parents('table');
            $table.find('.colorpicker_field').each( function( i, o ) {
                $(o).css('backgroundColor', '#' + $(o).next().data('default')).colpickSetColor('#' + $(o).next().data('default'));
                $(o).next().val($(o).next().data('default'));
            });
            $table.find('select').each( function( i, o ) {
                $(o).val($(o).data('default'));
            });
            $table.find('input[type=text]').each( function( i, o ) {
                $(o).val($(o).data('default'));
            });
        });
        $(document).on('change', '.br_ww_display_type', function() {
            $(this).parents('.br_ww_widget_form').find('.br_ww_display_type_').hide();
            $(this).parents('.br_ww_widget_form').find('.br_ww_display_type_'+$(this).val()).show();
        });

        $(document).on('click', '.theme_default', function (event) {
            event.preventDefault();
            $(this).prev().prev().colpickSetColor('#000000').css('backgroundColor', '');
            $(this).prev().val('');
        });
        $(document).on('click', '.br_show_hide_table', function() {
            $(this).find('.fa').removeClass('fa-chevron-down').removeClass('fa-chevron-up');
            if( $(this).is('.display_block') ) {
                $(this).parents('table').find('tbody').hide();
                $(this).removeClass('display_block').find('.fa').addClass('fa-chevron-down');
            } else {
                $(this).parents('table').find('tbody').show();
                $(this).addClass('display_block').find('.fa').addClass('fa-chevron-up');
            }
        });
    });
})(jQuery);