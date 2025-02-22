
/**
 *  Open websocket connection with server
 */
function websocket_client()
{
    const server = window.location.hostname;

    /**
     *  Get current origin (http://xxxx:port) then replace 'http' with 'ws'
     *  wss (secured) will automatically be used if the server uses https
     */
    const path = window.location.origin.replace('http', 'ws') + '/ws';
    const socket = new WebSocket(path);

    // Handle connection open
    socket.onopen = function (event) {
        console.log('Websocket connection opened at ' + path);

        // Send connection type to server
        message = JSON.stringify({
            'connection-type': 'browser-client'
        });

        sendMessage(message);
    };

    // Handle received message
    socket.onmessage = function (event) {
        // Parse message
        message = JSON.parse(event.data);

        // If message type is reload-container, then reload container
        if (message.type == 'reload-container') {
            reloadContainer(message.container);
        }
    };

    // Handle connection close
    socket.onclose = function (event) {
        console.log('Websocket connection closed with ' + server);

        // If the connection was closed cleanly
        if (event.wasClean) {
            console.log('Websocket connection with ' + server + ' closed (code=' + event.code + ' reason=' + event.reason + ')');

        // If the connection was closed unexpectedly
        // For example, the server process was killed or network problems occurred
        } else {
            console.log('Websocket connection with ' + server + ' closed unexpectedly');
        }
    };

    function sendMessage(message)
    {
        socket.send(message);
    }
}

/**
 * Get panel by name
 * @param {*} name
 */
function getPanel(name, params = [''], append = true)
{
    return new Promise((resolve, reject) => {
        ajaxRequest(
            // Controller:
            'general',
            // Action:
            'get-panel',
            // Data:
            {
                name: name,
                params: params
            },
            // Print success alert:
            false,
            // Print error alert:
            true
        ).then(function () {
            // Append panel to footer
            if (append === true) {
                $('footer').append(jsonValue.message);
                openPanel(name);
            }
            resolve('Panel retrieved successfully');
        }).catch(function (e) {
            reject('Failed to get panel: ' + e);
        });
    });
}

function openPanel(name)
{
    // If there is another panel opened, the background of the new panel should be transparent to avoid overlay
    if ($('.slide-panel-container').length > 1) {
        var background = '#00000000';
    } else {
        var background = '#0000001f';
    }

    $('.slide-panel-container[slide-panel="' + name + '"]').css({
        visibility: 'visible',
        background: background
    }).promise().done(function () {
        $('.slide-panel-container[slide-panel="' + name + '"]').find('.slide-panel').animate({
            right: '0'
        })
    })
}

function closePanel(name = null)
{
    if (name != null) {
        $('.slide-panel-container[slide-panel="' + name + '"]').find('.slide-panel').animate({
            right: '-1000px',
        }).promise().done(function () {
            // $('.slide-panel-container[slide-panel="' + name + '"]').css({
            //     visibility: 'hidden'
            // })
            $('.slide-panel-container[slide-panel="' + name + '"]').remove();
        })
    } else {
        $('.slide-panel').animate({
            right: '-1000px',
        }).promise().done(function () {
            $('.slide-panel-container').remove();
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
    random = Math.floor(Math.random() * (100000 - 100 + 1) + 100)

    if (type == null) {
        var classes = 'alert ' + random;
        var selector = '.alert.' + random;
        var icon = 'info';
    }

    if (type == 'success') {
        var classes = 'alert-success ' + random;
        var selector = '.alert-success.' + random;
        var icon = 'check';
    }

    if (type == 'error') {
        var classes = 'alert-error ' + random;
        var selector = '.alert-error.' + random;
        var icon = 'error';
        timeout = 4000;
    }

    // Remove any existing alert
    $('.alert').remove();

    $('footer').append(' \
    <div class="' + classes + '"> \
        <div class="flex align-item-center column-gap-8 padding-left-15 padding-right-15"> \
            <img src="/assets/icons/' + icon + '.svg" class="icon-np" /> \
            <div> \
                <p>' + message + '</p> \
            </div> \
        </div> \
    </div>');

    $(selector).css({
        visibility: 'visible'
    }).promise().done(function () {
        $(selector).animate({
            right: '0'
        }, 150)
    })

    if (timeout != null) {
        window.setTimeout(function () {
            closeAlert(selector);
        }, timeout);
    }
}

/**
 * Print a confirm box
 * @param {*} title
 * @param {*} confirmBoxFunction1
 * @param {*} confirmBtn1
 * @param {*} confirmBoxFunction2
 * @param {*} confirmBtn2
 */
function confirmBox(data)
{
    // Confirm box html
    var confirmBoxHtml = '<div id="confirm-box" class="confirm-box">'

    // Confirm box inner content
    var innerHtml = '<div class="flex flex-direction-column row-gap-10 padding-left-15 padding-right-15">'

    // Container for title and message
    innerHtml += '<div>';

    // If there is a title
    if (data.title != "") {
        innerHtml += '<div class="flex justify-space-between">';
        innerHtml += '<h6 class="margin-top-0 margin-bottom-0 wordbreakall">' + data.title.toUpperCase() + '</h6>';
        innerHtml += '<img src="/assets/icons/close.svg" class="icon-large lowopacity confirm-box-cancel-btn" title="Close" />';
        innerHtml += '</div>';
    }

    // If there is a message
    if (!empty(data.message)) {
        innerHtml += '<p class="note">' + data.message + '</p>';
    }

    // Close container for title and message
    innerHtml += '</div>';

    // Container for buttons
    innerHtml += '<div class="grid grid-2 column-gap-15 row-gap-15 margin-top-10">';

    // Loop through data to print each button
    if (!empty(data.buttons)) {
        var id = 0;
        for (const [key, value] of Object.entries(data.buttons)) {
            innerHtml += '<div class="confirm-box-btn btn-auto-' + value.color + '" confirm-btn-id="' + id + '" pointer">' + value.text + '</div>';
            id++;
        }
    }

    // Close container for buttons
    innerHtml += '</div>'

    // Close base html
    innerHtml += '</div>'

    // Append inner html to confirm box container
    confirmBoxHtml += innerHtml;

    // Close confirm box container
    confirmBoxHtml += '</div>'

    /**
     *  If there is already a confirm box with the same id, do not remove it to avoid blinking
     *  but replace its content
     */
    if (!empty(data.id) && $('#confirm-box').length > 0 && $('#confirm-box').attr('confirm-box-id') == data.id) {
        // Replace confirm box inner content
        $('#confirm-box[confirm-box-id="' + data.id + '"]').html(innerHtml);
    } else {
        // Remove any existing confirm box
        $("#confirm-box").remove();

        // Append html to footer
        $('footer').append(confirmBoxHtml);

        // Set confirm box id if specified
        if (!empty(data.id)) {
            $('#confirm-box').attr('confirm-box-id', data.id);
        }

        // Show confirm box
        $('#confirm-box').css({
            visibility: 'visible'
        }).promise().done(function () {
            $('#confirm-box').animate({
                right: '0'
            }, 150)
        });
    }

    // If a button is clicked
    $('.confirm-box-btn').click(function () {
        // Get button id
        var id = $(this).attr('confirm-btn-id');

        // Get function from data
        if (empty(data.buttons[id].callback)) {
            printAlert('Error: no function specified for this button', 'error');
            return;
        }

        // Execute function
        data.buttons[id].callback();

        // Close confirm box unless closeBox is set to false
        if (empty(data.buttons[id].closeBox) || (!empty(data.buttons[id].closeBox) && data.buttons[id].closeBox == true)) {
            closeConfirmBox();
        }
    });

    // If 'cancel' choice is clicked
    $('.confirm-box-cancel-btn').click(function () {
        closeConfirmBox();
    });
}

/**
 *  Close alert and confirm box modal
 */
function closeAlert(selector = '.alert')
{
    $(selector).animate({
        right: '-1000px'
    }, 150).promise().done(function () {
        $(selector).remove();
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
 */
function reloadPanel(panel)
{
    return new Promise((resolve, reject) => {
        /**
         *  Get panel and do not append it to the footer
         */
        getPanel(panel, [''], false).then(function () {
            // Get new panel content
            content = $(jsonValue.message).find('.slide-panel-reloadable-div[slide-panel="' + panel + '"]').html();

            // Replace slide-panel-reloadable-div with new content
            $('.slide-panel-reloadable-div[slide-panel="' + panel + '"]').html(content);

            // Reload opened or closed elements that where opened/closed before reloading
            reloadOpenedClosedElements();

            resolve('');
        }).catch(function (e) {
            reject('Failed to reload panel: ' + e);
        });
    });
}

/**
 * Ajax: Get and reload container
 * @param {*} container
 */
function reloadContainer(container, useMorphdom = true)
{
    return new Promise((resolve, reject) => {
        try {
            /**
             *  If the container to reload does not exist, return
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
            printLoadingVeilByParentClass('reloadable-container[container="' + container + '"]');

            ajaxRequest(
                // Controller:
                'general',
                // Action:
                'getContainer',
                // Data:
                {
                    sourceUrl: window.location.href,
                    sourceUri: window.location.pathname,
                    container: container
                },
                // Print success alert:
                false,
                // Print error alert:
                true,
                // Reload container:
                [],
                // Execute functions on success:
                [
                    // Replace container with itself, with new content
                    "$('.reloadable-container[container=\"" + container + "\"]').replaceWith(jsonValue.message)",
                    // Reload opened or closed elements that were opened/closed before reloading
                    "reloadOpenedClosedElements()"
                ]
            ).then(() => {
                if (useMorphdom) {
                    /**
                     *  Replace with new content using morphdom
                     */
                    morphdom($('.reloadable-container[container="' + container + '"]')[0], jsonValue.message, {
                        /**
                         * Avoid some elements to be updated if they are currently used (e.g. video playing)
                         */
                        onBeforeElUpdated: function (fromEl, toEl) {
                            /**
                             *  Case the element is a video and it is currently playing, do not update it
                             */
                            if (fromEl.tagName === 'VIDEO' && !fromEl.paused) {
                                return false;
                            }

                            /**
                             *  Case the element is a checkbox and it is currently checked, do not update it
                             */
                            if (fromEl.tagName === 'INPUT' && fromEl.type === 'checkbox' && fromEl.checked) {
                                return false;
                            }

                            return true;
                        }
                    });
                } else {
                    /**
                     *  Replace with new content
                     */
                    $('.reloadable-container[container="' + container + '"]').replaceWith(jsonValue.message);
                }

                // Hide loading icon
                // hideLoading();

                // Resolve promise
                resolve('Container reloaded');
            });
        } catch (error) {
            // Reject promise
            reject('Failed to reload container');
        }
    });
}

/**
 * Execute an ajax request
 * @param {*} controller
 * @param {*} action
 * @param {*} additionalData
 * @param {*} printSuccessAlert
 * @param {*} printErrorAlert
 * @param {*} reloadContainers
 * @param {*} execOnSuccess
 * @param {*} execOnError
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

    return new Promise((resolve, reject) => {
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

                /**
                 *  Print success message
                 */
                // Print alert
                if (printSuccessAlert === true) {
                    printAlert(jsonValue.message, 'success');
                }
                // Print to console
                if (printSuccessAlert == 'console') {
                    console.log(jsonValue.message);
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

                resolve('Ajax request executed successfully');
            },

            error: function (jqXHR, textStatus, thrownError) {
                /**
                 *  Retrieve and print error message
                 */
                jsonValue = jQuery.parseJSON(jqXHR.responseText);

                /**
                 *  Print error message
                 */
                // Print alert
                if (printErrorAlert === true) {
                    printAlert(jsonValue.message, 'error');
                }
                // Print to console
                if (printErrorAlert == 'console') {
                    console.log(jsonValue.message);
                }

                /**
                 *  Execute function(s) if specified
                 */
                if (execOnError != null) {
                    for (let i = 0; i < execOnError.length; i++) {
                        eval(execOnError[i]);
                    }
                }

                reject('Failed to execute ajax request');
            },
        });
    });
}

/**
 * Return true if the value is empty
 * @param {*} value
 * @returns
 */
function empty(value)
{
    // Check if the value is null or undefined
    if (value == null) {
        return true;
    }

    // Check if the value is a string and is empty
    if (typeof value === 'string' && value.trim() === '') {
        return true;
    }

    // Check if the value is an empty array
    if (Array.isArray(value) && value.length === 0) {
        return true;
    }

    // Check if the value is an empty object
    if (typeof value === 'object' && Object.keys(value).length === 0) {
        return true;
    }

    // Check if the value is a number and is NaN
    if (typeof value === 'number' && isNaN(value)) {
        return true;
    }

    // If none of the above conditions are met, the value is not empty
    return false;
}

/**
 *  Convert select tag to a select2 by specified element
 *  @param {*} element
 */
function selectToSelect2(element, placeholder = 'Select...', tags = false)
{
    $(element).select2({
        closeOnSelect: false,
        placeholder: placeholder,
        tags: tags,
        minimumResultsForSearch: Infinity, /* disable search box */
        allowClear: true /* add a clear button */
    });
}

/**
 * Print a modal window with specified content
 * @param {*} content
 * @param {*} title
 * @param {*} inPre
 */
function printModalWindow(content, title, inPre = true)
{
    /**
     *  If a modal window is already opened, remove it
     */
    $('.modal-window-container').remove();

    html = '<div class="modal-window-container">'
        + '<div class="modal-window">'
        + '<div class="flex justify-space-between">'
        + '<h4>' + title + '</h4>'
        + '<span class="modal-window-close-btn"><img title="Close" class="close-btn lowopacity" src="/assets/icons/close.svg" /></span>'
        + '</div>'
        + '<div>';
    if (inPre) {
        html += '<pre>' + content + '</pre>';
    } else {
        html += content;
    }

    html += '</div>'
        + '</div>'
        + '</div>';

    $('footer').append(html);
}
