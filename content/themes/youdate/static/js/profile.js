(function($, viewport){
    $(document).ready(function() {

        var $photos = $('#profile-column-left .sidebar-wrapper'),
            $content = $('#profile-column-content'),
            $sidebar = $('#profile-column-right');

        var updateLayout = function() {
            if (viewport.is('md')) {
                var margin = $content.height() - $photos.height() - 30;
                $sidebar.css({ 'marginTop': -margin + 'px' });
            } else {
                $sidebar.css({ 'marginTop': '0' });
            }
        };

        $(window).resize(
            viewport.changed(function() {
                console.log(viewport.current());
                updateLayout();
            })
        );

        updateLayout();

    });
})(jQuery, ResponsiveBootstrapToolkit);
