function openSlide(id)
{
    $(id).animate({
        width: '100vw'
    }).show();
}

function closeSlide(id)
{
    $(id).animate({
        width: '0'
    }).hide('100');
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
 *  Print alert or error message
 *  @param {string} message
 *  @param {string} type
 *  @param {int} timeout
 */
function printAlert(message, type = null, timeout = 2500)
{
    $('#newalert').remove();

    if (type == null) {
        $('footer').append('<div id="newalert" class="alert">' + message + '</div>');
    }
    if (type == "error") {
        $('footer').append('<div id="newalert" class="alert-error">' + message + '</div>');
    }
    if (type == "success") {
        $('footer').append('<div id="newalert" class="alert-success">' + message + '</div>');
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
 * @param {*} myfunction
 * @param {*} confirmBox
 */
function deleteConfirm(message, myfunction, confirmBox = 'Delete')
{
    /**
     *  D'abord on supprime toute alerte déjà active et qui ne serait pas fermée
     */
     $("#newConfirmAlert").remove();

    var $content = '<div id="newConfirmAlert" class="confirmAlert"><span class="confirmAlert-message">' + message + '</span><div class="confirmAlert-buttons-container"><span class="pointer btn-doConfirm">' + confirmBox + '</span><span class="pointer btn-doCancel">Cancel</span></div></div>';

    $('footer').append($content);

    /**
     *  Si on clique sur le bouton 'Delete'
     */
    $('.btn-doConfirm').click(function () {
        /**
         *  Exécution de la fonction passée en paramètre
         */
        myfunction();

        /**
         *  Puis suppression de l'alerte
         */
        $("#newConfirmAlert").slideToggle(150, function () {
            $("#newConfirmAlert").remove();
        });
    });

    /**
     *  Si on clique sur le bouton 'Annuler'
     */
    $('.btn-doCancel').click(function () {
        /**
         *  Suppression de l'alerte
         */
        $("#newConfirmAlert").slideToggle(150, function () {
            $("#newConfirmAlert").remove();
        });
    });
}

/**
 *  Event: acquit motion-UI update log and close window
 */
$(document).on('click','#update-continue-btn',function () {
    /**
     *  Acquit and close window
     */
    continueUpdate();

    /**
     *  Reload current page
     */
    setTimeout(function () {
        window.location = window.location.href.split("?")[0];
    }, 500);
});

/**
 * Ajax: acquit motion-UI update log and close window
 */
function continueUpdate()
{
    $.ajax({
        type: "POST",
        url: "controllers/general/ajax.php",
        data: {
            action: "continueUpdate"
        },
        dataType: "json",
        success: function (data, textStatus, jqXHR) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
        },
        error : function (jqXHR, textStatus, thrownError) {
            jsonValue = jQuery.parseJSON(jqXHR.responseText);
        },
    });
}