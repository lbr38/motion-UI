/**
 *  Event: select stats dates
 */
$(document).on('change','.stats-date-input',function () {
    var dateStart = $('.stats-date-input[name=dateStart]').val();
    var dateEnd = $('.stats-date-input[name=dateEnd]').val();

    statsDateSelect(dateStart, dateEnd);
});
