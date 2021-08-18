$(document).ready(function() {
    var $errorSummary = $('.error-summary'),
        $photoUpload = $('#photo-upload');

    $photoUpload
        .bind('fileuploaddone', function (e, data) {
            if (data.result && data.result.files) {
                var errors = '';
                $.each(data.result.files, function(i, file) {
                    errors += errorsToString(file)
                });
                if (errors) {
                    $errorSummary.html('<ul>' + errors + '</ul>').show();
                } else {
                    $errorSummary.hide();
                }
            }
        });

    function errorsToString(file) {
        var output = '';
        if (!file.error) {
            return output;
        }
        $.each(file.errors, function(i, error) {
            output += '<li>' + error + '</li>';
        });
        return output;
    }
});
