
function openPanel(name)
{
    $('.slide-panel-container[slide-panel="' + name + '"]').css({
        visibility: 'visible'
    }).promise().done(function () {
        $('.slide-panel-container[slide-panel="' + name + '"]').find('.slide-panel').animate({
            right: '0'
        }, 150)
    })
}

function closePanel(name = null)
{
    if (name != null) {
        $('.slide-panel-container[slide-panel="' + name + '"]').find('.slide-panel').animate({
            right: '-1000px',
        }).promise().done(function () {
            $('.slide-panel-container[slide-panel="' + name + '"]').css({
                visibility: 'hidden'
            })
        })
    } else {
        $('.slide-panel').animate({
            right: '-1000px',
        }).promise().done(function () {
            $('.slide-panel-container').css({
                visibility: 'hidden'
            })
        })
    }
}

/**
 * Print an alert
 * @param {*} message
 * @param {*} type
 * @param {*} timeout
 */
function printAlert(message, type = null, timeout = 3000)
{
    if (type == null) {
        var alertType = 'alert';
        var icon = 'info';
    }

    if (type == 'success') {
        var alertType = 'alert-success';
        var icon = 'check';
    }

    if (type == 'error') {
        var alertType = 'alert-error';
        var icon = 'error';
        timeout = 4000;
    }

    // Remove any existing alert
    $('#alert').remove();

    $('footer').append(' \
    <div id="alert" class="' + alertType + '"> \
        <div class="flex align-item-center column-gap-8 padding-left-15 padding-right-15"> \
            <img src="/assets/icons/' + icon + '.svg" class="icon" /> \
            <div> \
                <p>' + message + '</p> \
            </div> \
        </div> \
    </div>');

    $('#alert').css({
        visibility: 'visible'
    }).promise().done(function () {
        $('#alert').animate({
            right: '0'
        }, 150)
    })

    if (timeout != 'none') {
        window.setTimeout(function () {
            closeAlert();
        }, timeout);
    }
}

/**
 * Print a confirm box
 * @param {*} message
 * @param {*} confirmBoxFunction1
 * @param {*} confirmBtn1
 * @param {*} confirmBoxFunction2
 * @param {*} confirmBtn2
 */
function confirmBox(message = '', confirmBoxFunction1, confirmBtn1 = 'Delete', confirmBoxFunction2 = null, confirmBtn2 = null, confirmBoxId = null)
{
    /**
     *  If there is already a confirm box with the same id, do nothing
     *  The Id is used to prevent a same confirm box from being re-opened
     */
    if (confirmBoxId != null) {
        if ($('#confirm-box').length > 0) {
            if ($('#confirm-box').attr('confirm-box-id') == confirmBoxId) {
                return;
            }
        }
    }

    // Remove any existing confirm box
    $("#confirm-box").remove();

    // Base html
    var html = '<div id="confirm-box" class="confirm-box"><div class="flex flex-direction-column row-gap-10 padding-left-15 padding-right-15">'

    // If there is a message
    if (message != "") {
        html += '<p class="wordbreakall">' + message + '</p>';
    }

    // Container for buttons
    html += '<div class="flex flex-wrap column-gap-15 row-gap-10">';

    // First function and button
    html += '<div class="confirm-box-btn1 btn-small-red pointer">' + confirmBtn1 + '</div>';

    // Second function and button
    if (confirmBoxFunction2 != null && confirmBtn2 != null) {
        html += '<div class="confirm-box-btn2 btn-small-blue pointer">' + confirmBtn2 + '</div>';
    }

    // Cancel button
    html += '<div class="confirm-box-cancel-btn btn-small-blue pointer">Cancel</div>';

    html += '</div>'

    // Close base html
    html += '</div></div>'

    // Append html to footer
    $('footer').append(html);

    // Set confirm box id if specified
    if (confirmBoxId != null) {
        $('#confirm-box').attr('confirm-box-id', confirmBoxId);
    }

    // Show confirm box
    $('#confirm-box').css({
        visibility: 'visible'
    }).promise().done(function () {
        $('#confirm-box').animate({
            right: '0'
        }, 150)
    })

    // If choice one is clicked
    $('.confirm-box-btn1').click(function () {
        // Execute function 1
        confirmBoxFunction1();
        closeConfirmBox();
    });

    // If choice two is clicked
    $('.confirm-box-btn2').click(function () {
        // Execute function 2
        confirmBoxFunction2();
        closeConfirmBox();
    });

    // If 'cancel' choice is clicked
    $('.confirm-box-cancel-btn').click(function () {
        closeConfirmBox();
    });
}

/**
 *  Close alert and confirm box modal
 */
function closeAlert()
{
    $('#alert').animate({
        right: '-1000px'
    }, 150).promise().done(function () {
        $('#alert').remove();
    });
}

/**
 *  Close confirm box
 */
function closeConfirmBox()
{
    $('#confirm-box').animate({
        right: '-1000px'
    }, 150).promise().done(function () {
        $('#confirm-box').remove();
    });
}

/**
 *  Print a veil on specified element by class name, element must be relative
 *  @param {*} name
 */
function printLoadingVeilByClass(name)
{
    $('.' + name).append('<div class="loading-veil"><img src="/assets/icons/loading.svg" class="icon" /><span class="lowopacity-cst">Loading</span></div>');
}

/**
 *  Find all child elements with class .veil-on-reload and print a veil on them, each element must be relative
 *  @param {*} name
 */
function printLoadingVeilByParentClass(name)
{
    $('.' + name).find('.veil-on-reload').append('<div class="loading-veil"><img src="/assets/icons/loading.svg" class="icon" /><span class="lowopacity-cst">Loading</span></div>');
}

/**
 *  Print a veil on the whole page
 */
function veilBody()
{
    $('body').append('<div class="body-veil"><img src="/assets/icons/motion.svg" /><img src="/assets/icons/loading.svg" /></div>');
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
 * Set cookie value
 * @param {*} cname
 * @param {*} cvalue
 * @param {*} exdays
 */
function setCookie(cname, cvalue, exdays)
{
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;Secure";
}

/**
 *  Return GET parameters as object (array)
 */
function getGetParams()
{
    /**
     *  Get current URL and GET parameters
     */
    let url = new URL(window.location.href)
    let params = new URLSearchParams(url.search);
    let entries = params.entries();

    /**
     *  Parse and convert to object
     *  For each GET param, add key and value to the object
     */
    let array = {}
    for (let entry of entries) { // each 'entry' is a [key, value]
        let [key, val] = entry;

        /**
         *  If key ends with '[]' then it's an array
         */
        if (key.endsWith('[]')) {
            // clean up the key
            key = key.slice(0,-2);
            (array[key] || (array[key] = [])).push(val)
        /**
         *  Else it's a normal parameter
         */
        } else {
            array[key] = val;
        }
    }

    return array;
}

/**
 * Reload panel and execute function if needed
 * @param {*} panel
 * @param {*} myfunction
 */
function reloadPanel(panel, myfunction = null)
{
    /**
     *  Print a loading icon on the bottom of the page
     */
    // printLoading();

    /**
     *  Check if panel has children with class .veil-on-reload
     *  If so print a veil on them
     */
    if ($('.slide-panel-reloadable-div[slide-panel="' + panel + '"]').find('.veil-on-reload').length) {
        printLoadingVeilByClass('veil-on-reload');
    }

    $('.slide-panel-reloadable-div[slide-panel="' + panel + '"]').load(' .slide-panel-reloadable-div[slide-panel="' + panel + '"] > *', function () {
        /**
         *  If myfunction is not null, execute it after reloading
         */
        if (myfunction != null) {
            myfunction();
        }

        /**
         *  Reload opened or closed elements that where opened/closed before reloading
         */
        reloadOpenedClosedElements();
    });

    /**
     *  Hide loading icon
     */
    // hideLoading();
}

/**
 * Ajax: Get and reload container
 * @param {*} container
 */
function reloadContainer(container)
{
    /**
     *  Check if container exists on the current page, else do nothing
     */
    if (!$('.reloadable-container[container="' + container + '"]').length) {
        return;
    }

    /**
     *  Print a loading icon on the bottom of the page
     */
    // printLoading();

    /**
     *  Check if container has children with class .veil-on-reload
     *  If so print a veil on them
     */
    if ($('.reloadable-container[container="' + container + '"]').find('.veil-on-reload').length) {
        printLoadingVeilByClass('veil-on-reload');
    }

    $.ajax({
        type: "POST",
        url: "/ajax/controller.php",
        data: {
            sourceUrl: window.location.href,
            sourceUri: window.location.pathname,
            controller: "general",
            action: "getContainer",
            container: container
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            /**
             *  Replace container with itself, with new content
             */
            $('.reloadable-container[container="' + container + '"]').replaceWith(jsonValue.message);

            /**
             *  Reload opened or closed elements that were opened/closed before reloading
             */
            reloadOpenedClosedElements();
        },
        error: function (jqXHR, textStatus, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
            printAlert(jsonValue.message, 'error');
        },
    });

    /**
     *  Hide loading icon
     */
    // hideLoading();
}

/**
 * Execute an ajax request
 * @param {*} controller
 * @param {*} action
 * @param {*} additionalData
 * @param {*} reloadContainers
 */
function ajaxRequest(controller, action, additionalData = null, printSuccessAlert = true, printErrorAlert = true, reloadContainers = null, execOnSuccess = null, execOnError = null)
{
    /**
     *  Default data
     */
    var data = {
        sourceUrl: window.location.href,
        sourceUri: window.location.pathname,
        controller: controller,
        action: action,
    };

    /**
     *  If additional data is specified, merge it with default data
     */
    if (additionalData != null) {
        data = $.extend(data, additionalData);
    }

    /**
     *  For debug only
     */
    // console.log(data);

    /**
     *  Ajax request
     */
    $.ajax({
        type: "POST",
        url: "/ajax/controller.php",
        data: data,
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            /**
             *  Retrieve and print success message
             */
            jsonValue = jQuery.parseJSON(jqXHR.responseText);

            if (printSuccessAlert) {
                printAlert(jsonValue.message, 'success');
            }

            /**
             *  Reload containers if specified
             */
            if (reloadContainers != null) {
                for (let i = 0; i < reloadContainers.length; i++) {
                    reloadContainer(reloadContainers[i]);
                }
            }

            /**
             *  Execute function(s) if specified
             */
            if (execOnSuccess != null) {
                for (let i = 0; i < execOnSuccess.length; i++) {
                    eval(execOnSuccess[i]);
                }
            }
        },
        error: function (jqXHR, textStatus, thrownError) {
            /**
             *  Retrieve and print error message
             */
            jsonValue = jQuery.parseJSON(jqXHR.responseText);

            if (printErrorAlert) {
                printAlert(jsonValue.message, 'error');
            }
        },
    });
}
