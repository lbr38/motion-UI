function openSlide(id)
{
    $(id).css({
        visibility: 'visible'
    }).promise().done(function () {
        $(id).find('.param-slide').animate({
            right: '0'
        })
    })
}

function closeSlide(id)
{
    $(id).find('.param-slide').animate({
        right: '-2000px',
    }).promise().done(function () {
        $(id).css({
            visibility: 'hidden'
        })
    })
}

/**
 *  Reload <body> content
 */
function reloadBody()
{
    $('body').load(location.href + ' body > *');
}

/**
 *  Reload content of an element, by its Id
 *  @param {string} id
 */
function reloadContentById(id)
{
    $('#' + id).load(location.href + ' #' + id + ' > *');
}

/**
 * Reload content of an element, by its class
 * @param {*} className
 */
function reloadContentByClass(className)
{
    $('.' + className).load(location.href + ' .' + className + ' > *');
}

/**
 *  Print alert or error message
 *  @param {string} message
 *  @param {string} type
 *  @param {int} timeout
 */
function printAlert(message, type = null, timeout = 2500)
{
    $('#newalert').remove();

    if (type == "error") {
        $('footer').append('<div id="newalert" class="alert-error"><div>' + message + '</div></div>');
    }
    if (type == "success") {
        $('footer').append('<div id="newalert" class="alert-success"><div>' + message + '</div></div>');
    }

    if (timeout != 'none') {
        window.setTimeout(function () {
            $('#newalert').fadeTo(1000, 0).slideUp(1000, function () {
                $('#newalert').remove();
            });
        }, timeout);
    }
}

/**
 * Print a confirm alert box before executing specified function
 * @param {*} message
 * @param {*} myfunction1
 * @param {*} confirmBox1
 */
function confirmBox(message, myfunction1, confirmBox1 = 'Delete', myfunction2 = null, confirmBox2 = null)
{
    /**
     *  First, delete all active confirm box if any
     */
    $("#newConfirmAlert").remove();

    /**
     *  Case there is three choices
     */
    if (myfunction2 != null && confirmBox2 != null) {
        var $content = '<div id="newConfirmAlert" class="confirmAlert"><span></span><span>' + message + '</span><div class="confirmAlert-buttons-container"><span class="pointer btn-doConfirm1">' + confirmBox1 + '</span><span class="pointer btn-doConfirm2">' + confirmBox2 + '</span><span class="pointer btn-doCancel">Cancel</span></div></div>';
    /**
     *  Case there is two choices
     */
    } else {
        var $content = '<div id="newConfirmAlert" class="confirmAlert"><span></span><span>' + message + '</span><div class="confirmAlert-buttons-container"><span class="pointer btn-doConfirm1">' + confirmBox1 + '</span><span class="pointer btn-doCancel">Cancel</span></div></div>';
    }

    $('footer').append($content);

    /**
     *  If choice one is clicked
     */
    $('.btn-doConfirm1').click(function () {
        /**
         *  Execute function 1
         */
        myfunction1();

        /**
         *  Then remove alert
         */
        $("#newConfirmAlert").slideToggle(0, function () {
            $("#newConfirmAlert").remove();
        });
    });

    /**
     *  If choice two is clicked
     */
    $('.btn-doConfirm2').click(function () {
        /**
         *  Execute function 2
         */
        myfunction2();

        /**
         *  Then remove alert
         */
        $("#newConfirmAlert").slideToggle(0, function () {
            $("#newConfirmAlert").remove();
        });
    });

    /**
     *  If 'cancel' choice is clicked
     */
    $('.btn-doCancel').click(function () {
        /**
         *  Remove alert
         */
        $("#newConfirmAlert").slideToggle(0, function () {
            $("#newConfirmAlert").remove();
        });
    });
}

/**
 *  Get specified cookie value
 *  @param {*} name
 */
function getCookie(name)
{
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");

    // Loop through the array elements
    for (var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");

        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if (name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }

    // Return null if not found
    return null;
}

/**
 *  Event: hide slided window on escape button press
 */
$(document).keyup(function (e) {
    if (e.key === "Escape") {
        $('.param-slide-container').find('.param-slide').animate({
            right: '-2000px',
        }).promise().done(function () {
            $('.param-slide-container').css({
                visibility: 'hidden'
            })
        })
    }
});