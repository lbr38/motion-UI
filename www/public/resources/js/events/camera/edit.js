/**
 *  Event: enable ONVIF param in camera edit form
 */
$(document).on('click', '.form-param[param-name="onvif-enable"]', function () {
    // If checked, show additional ONVIF fields
    if ($(this).is(':checked')) {
        $('#onvif-fields').show();
    } else {
        $('#onvif-fields').hide();
    }
});

/**
 *  Event: on URL or credentials change in camera edit form, fetch stream info (resolution, framerate, ...)
 *  and fill the corresponding fields
 */
$(document).on('change', '.form-param[param-name="main-stream-device"], .form-param[param-name="secondary-stream-device"], .form-param[param-name="username"], .form-param[param-name="password"]', function () {
    const username = $('.form-param[param-name="username"]').val();
    const password = $('.form-param[param-name="password"]').val();
    var mainStream = $('.form-param[param-name="main-stream-device"]').val();
    var secondaryStream = $('.form-param[param-name="secondary-stream-device"]').val();

    if (mainStream) {
        // Add a veil on the main stream fields and show loading icon
        $('.form-param[param-name="main-stream-resolution"]').css('opacity', '0.5');
        $('.form-param[param-name="main-stream-framerate"]').css('opacity', '0.5');
        $('.main-stream-resolution-loading').removeClass('hide');
        $('.main-stream-framerate-loading').removeClass('hide');

        if (username && password) {
            // Add username and password to the URL
            mainStream = mainStream.replace('rtsp://', 'rtsp://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
            mainStream = mainStream.replace('http://', 'http://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
            mainStream = mainStream.replace('https://', 'https://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
        }

        ajaxRequest(
            // Controller:
            'camera/stream',
            // Action:
            'getInfo',
            // Data:
            {
                url: mainStream
            },
            // Print success alert:
            false,
            // Print error alert:
            false
        ).then(function () {
            // If width and height are returned, set the resolution field
            if (jsonValue.message.width && jsonValue.message.height) {
                $('.form-param[param-name="main-stream-resolution"]').val(jsonValue.message.width + 'x' + jsonValue.message.height);
            }

            // If frame rate is returned, set the framerate field
            if (jsonValue.message.r_frame_rate) {
                // Extract the number before the slash
                const frameRate = jsonValue.message.r_frame_rate.split('/')[0];
                $('.form-param[param-name="main-stream-framerate"]').val(frameRate);
            }
        }).finally(function () {
            // Remove the veil on the main stream fields and hide loading icon
            $('.main-stream-resolution-loading').addClass('hide');
            $('.main-stream-framerate-loading').addClass('hide');
            $('.form-param[param-name="main-stream-resolution"]').css('opacity', '1');
            $('.form-param[param-name="main-stream-framerate"]').css('opacity', '1');
        });
    }

    if (secondaryStream) {
        // Add a veil on the secondary stream fields and show loading icon
        $('.form-param[param-name="secondary-stream-resolution"]').css('opacity', '0.5');
        $('.form-param[param-name="secondary-stream-framerate"]').css('opacity', '0.5');
        $('.secondary-stream-resolution-loading').removeClass('hide');
        $('.secondary-stream-framerate-loading').removeClass('hide');

        if (username && password) {
            // Add username and password to the URL
            secondaryStream = secondaryStream.replace('rtsp://', 'rtsp://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
            secondaryStream = secondaryStream.replace('http://', 'http://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
            secondaryStream = secondaryStream.replace('https://', 'https://' + encodeURIComponent(username) + ':' + encodeURIComponent(password) + '@');
        }

        ajaxRequest(
            // Controller:
            'camera/stream',
            // Action:
            'getInfo',
            // Data:
            {
                url: secondaryStream
            },
            // Print success alert:
            false,
            // Print error alert:
            false
        ).then(function () {
            // If width and height are returned, set the resolution field
            if (jsonValue.message.width && jsonValue.message.height) {
                $('.form-param[param-name="secondary-stream-resolution"]').val(jsonValue.message.width + 'x' + jsonValue.message.height);
            }

            // If frame rate is returned, set the framerate field
            if (jsonValue.message.r_frame_rate) {
                // Extract the number before the slash
                const frameRate = jsonValue.message.r_frame_rate.split('/')[0];
                $('.form-param[param-name="secondary-stream-framerate"]').val(frameRate);
            }
        }).finally(function () {
            // Remove the veil on the secondary stream fields and hide loading icon
            $('.secondary-stream-resolution-loading').addClass('hide');
            $('.secondary-stream-framerate-loading').addClass('hide');
            $('.form-param[param-name="secondary-stream-resolution"]').css('opacity', '1');
            $('.form-param[param-name="secondary-stream-framerate"]').css('opacity', '1');
        });
    }
});
