var timerAfter = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (timers[uniqueId]) {
            clearTimeout (timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

$(document).ready(function() {

    var $webcamAlert = $('.alert-webcam'),
        $webCam = $('.webcam'),
        $webCamWrapper = $('.webcam-wrapper'),
        $webCamIcon = $('.webcam-camera i.fe'),
        $form = $('#verification-form'),
        webCamConfig = {
            width: $webCamWrapper.width(),
            height: $webCamWrapper.height(),
            snap_width: 640,
            snap_height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            force_flash: false,
            flip_horiz: true,
            fps: 30
        };

    if ($webCam.length) {
        Webcam.set(webCamConfig);

        Webcam.on('live', function() {
            $webCamIcon.addClass('hidden');
        });

        Webcam.on('error', function(error) {
            $webcamAlert.removeClass('hidden');
            console.log(error);
        });

        Webcam.attach('.webcam');
        $webCam.width($webCamWrapper.width());
        $webCam.height($webCamWrapper.height());

        $form.on('submit', function(event) {
            var $webCamVideo = $('.webcam video'),
                vHeight = $webCamVideo.height(),
                vWidth = $webCamVideo.width();

            $webCamVideo.hide().css({'width': '640px', 'height': '480px'});

            Webcam.snap(function(dataUri) {
                var rawData = dataUri.replace(/^data\:image\/\w+\;base64\,/, '');
                var imageFormat = '';
                if (dataUri.match(/^data\:image\/(\w+)/)) {
                    imageFormat = RegExp.$1;
                }

                var blob = new Blob([Webcam.base64DecToArr(rawData)], {type: 'image/' + imageFormat} );
                var formData = new FormData();

                formData.append('photo', blob, 'verification_' + $form.attr('data-user-id') + '.' + imageFormat.replace(/e/, ''));
                $.ajax({
                    url: $form.attr('action'),
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        location.reload();
                    },
                    complete: function () {
                        $webCamVideo.css({'width': vWidth + 'px', 'height': vHeight + 'px'}).show();
                    }
                })
            });

            event.preventDefault();
        });

        $(window).on('resize', function() {
            timerAfter(function(){
                Webcam.set({
                    width: $webCamWrapper.width(),
                    height: $webCamWrapper.height()
                });
                $webCam.width($webCamWrapper.width());
                $webCam.height($webCamWrapper.height());
                $('.webcam video').css({ 'width': '100%', 'height': 'auto  !important' });
            }, 500, 'resizeTimer');
        });
    }

});
