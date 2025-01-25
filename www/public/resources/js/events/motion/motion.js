/**
 *  Event: view motion process log
 */
$(document).on('click', '#view-motion-log-btn', function () {
    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'get-log',
        // Data:
        {},
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        printModalWindow(jsonValue.message, 'MOTION LOG');
    });
});
