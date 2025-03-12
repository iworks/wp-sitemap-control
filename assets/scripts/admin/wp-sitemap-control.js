/*! WP Sitemap Control - 1.1.1
 * https://github.com/iworks/wp-sitemap-control
 * Copyright (c) 2025; * Licensed GPL-3.0 */
jQuery(document).ready(function($) {
    $('#wpsmc_post_type, #wpsmc_taxonomy').on('change', function() {
        if ($(this).hasClass('loaded')) {
            var $this = $(this);
            var $container = $(this).closest('table');
            if ($(this).is(':checked')) {
                $('input[type=checkbox]', $container).each(function() {
                    if ($this.attr('id') !== $(this).attr('id')) {
                        $($('span', $(this).parent())[1]).trigger('click');
                    }
                });
            } else {
                $('input[type=checkbox]', $container).each(function() {
                    if ($this.attr('id') !== $(this).attr('id')) {
                        $($('span', $(this).parent())[0]).trigger('click');
                    }
                });
            }
        }
        $(this).addClass('loaded');
    });
});