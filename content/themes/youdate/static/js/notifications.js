$(document).ready(function () {
    var $categories = $('.notification-categories'),
        $pjax = $('#pjax-notifications'),
        baseUrl = $categories.attr('data-url');

    $categories.on('change', 'input', function(event) {
        var $categoriesCount = $categories.find('input[type=checkbox]'),
            $checkedCategories = $categories.find('input[type=checkbox]:checked'),
            url;

        if ($categoriesCount.length == $checkedCategories.length) {
            url = baseUrl;
        } else {
            var categories = [];
            $.each($checkedCategories, function(i, category) {
                categories.push($(category).attr('name'));
            });
            url = baseUrl + '?filters=' + categories.join(',');
        }

        $.pjax({
            url        : url,
            container  : '#pjax-notifications',
            data       : {},
            push       : true,
            replace    : false,
            timeout    : 10000,
            "scrollTo" : false
        })
    });
});
