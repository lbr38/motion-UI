/**
 * Function: print stats between selected dates
 * @param {*} dateStart
 * @param {*} dateEnd
 */
function statsDateSelect(dateStart, dateEnd)
{
    /**
     *  Add specified dates into cookies
     */
    document.cookie = "statsDateStart=" + dateStart + ";max-age=900;";
    document.cookie = "statsDateEnd=" + dateEnd + ";max-age=900;";

    /**
     *  Then reload stats div
     */
    mycontainer.reload('motion/stats/list', false);
}
