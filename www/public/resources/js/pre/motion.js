/**
 *  Ask the server to return the total size of the media files for the selected date
 */
function loadEventDateTotalMediaSize()
{
    if ($('#event-date-total-size').length == 0) {
        return;
    }

    // Get date
    var date = $('#event-date-total-size').attr('event-date');

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'get-event-date-total-media-size',
        // Data:
        {
            date: date
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        console.log('size : ' + jsonValue.message)
        $('#event-date-total-size').html('(' + jsonValue.message + ')');
    }).catch(function () {
        $('#event-date-total-size').html();
    });
}