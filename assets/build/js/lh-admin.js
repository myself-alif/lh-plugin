(function ($) {
    $(function () {
        const $body = $('body');

        if ($body.hasClass('wp-admin') && $body.hasClass('options-general-php')) {
            const $site_icon_tr_td = $body.find('.form-table .site-icon-section td');

            $site_icon_tr_td.find('#site-icon-preview').hide();
            $site_icon_tr_td.find('#site_icon_hidden_field').hide();
            $site_icon_tr_td.find('.site-icon-action-buttons').hide();
            $site_icon_tr_td.find('p.description').eq(0).hide();

            $site_icon_tr_td.append('<p class="description">You can control the site icon from the <a href="/wp-admin/options-general.php?page=lh-theme-settings-options-general">Lucky Days Theme settings</a> page.</p>');
        }
    });
})(jQuery);
