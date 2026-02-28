/**
 *  Ask the server to return the total size of the media files for the selected date
 */
function loadEventDateTotalMediaSize()
{
    if ($('#event-date-total-size').length == 0) {
        return;
    }

    // Get date
    const date = $('#event-date-total-size').attr('event-date');

    // Get selected cameras from select
    const cameras = $('#events-filter-cameras').val();

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'get-event-date-total-media-size',
        // Data:
        {
            date: date,
            cameras: cameras
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        $('#event-date-total-size').html('(' + jsonValue.message + ')');
    }).catch(function () {
        $('#event-date-total-size').html('Error loading size');
    });
}