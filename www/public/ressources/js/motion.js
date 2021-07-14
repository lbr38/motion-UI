/**
 *  Event: Start motion capture
 */
 $(document).on('click','#start-motion-btn',function () {
    startStopMotion('start');
 });

/**
 *  Event: Stop motion capture
 */
 $(document).on('click','#stop-motion-btn',function () {
    startStopMotion('stop');
 });

/**
 * Event: Show motion configuration file
 */
 $(document).on('click','.show-motion-conf-btn',function () {
    var filename = $(this).attr('filename');

    $("div[filename='" + filename + "']").slideToggle('100');
 });

/**
 * Event: Hide motion configuration file
 */
 $(document).on('click','.hide-motion-conf-btn',function () {
    var filename = $(this).attr('filename');

    $("div[filename='" + filename + "']").slideToggle('100');
 });


/**
 *  Send alert configuration
 */
 $(document).on('submit','.motion-configuration-form',function () {
    event.preventDefault();

    var options_array = [];

    /**
     *  Get the name of the configuration file
     */
    var filename = $(this).attr('filename');

    /**
     *  Get all the parameters and their value in the form
     */

    /**
     *  D'abord on compte le nombre d'input de class 'sourceConfForm-optionName' dans ce formulaire
     */
    var countTotal = $(this).find('input[name=option-name]').length

    /**
     *  Chaque paramètre de configuration et leur valeur associée possèdent un id
     *  On récupère le nom du paramètre et sa valeur associée ayant le même id et on push le tout dans un tableau
     */
    if (countTotal > 0) {
        for (let i = 0; i < countTotal; i++) {
            /**
             *  Get parameter status (slider checked or not)
             */
            if ($(this).find('input[name=option-status][option-id=' + i + ']').is(':checked')) {
                var option_status = 'enabled';
            } else {
                var option_status = '';
            }
            /**
             *  Get parameter name and its value
             */
            var option_name = $(this).find('input[name=option-name][option-id=' + i + ']').val();
            var option_value = $(this).find('input[name=option-value][option-id=' + i + ']').val()

            /**
             *  Push all to options_array
             */
            options_array.push(
                {
                    status: option_status,
                    name: option_name,
                    value: option_value
                }
            );
        }
    }

    configure(filename, options_array);

    return false;
 });

/**
 * Ajax : start or stop motion capture
 * @param {*} status
 */
 function startStopMotion(status)
 {
     $.ajax({
            type: "POST",
            url: "controllers/ajax.php",
            data: {
                action: "startStopMotion",
                status: status
            },
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                reloadContentById('motion-start-div');
            },
            error : function (jqXHR, ajaxOptions, thrownError) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                printAlert(jsonValue.message, 'error');
            },
        });
 }

/**
 * Ajax : configure motion config file
 * @param {*} filename
 * @param {*} options_array
 */
 function configure(filename, options_array)
 {
     $.ajax({
            type: "POST",
            url: "controllers/ajax.php",
            data: {
                action: "configureMotion",
                filename: filename,
                options_array: options_array
            },
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                printAlert(jsonValue.message, 'success');
                reloadContentById('configuration-container');
            },
            error : function (jqXHR, ajaxOptions, thrownError) {
                jsonValue = jQuery.parseJSON(jqXHR.responseText);
                printAlert(jsonValue.message, 'error');
            },
        });
 }