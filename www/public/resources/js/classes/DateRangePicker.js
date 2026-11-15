class DateRangePicker
{
    /**
     * Return the predefined ranges, computed from the current date
     * Called again each time a relative range needs to be recomputed (e.g. charts auto-update)
     * @returns {object} - Predefined ranges, label => [start, end]
     */
    presets()
    {
        return {
            [__('Last 24 Hours')]: [moment().subtract(24, 'hours'), moment()],
            [__('Today')]: [moment().startOf('day'), moment().endOf('day')],
            [__('Yesterday')]: [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
            [__('Last 7 Days')]: [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
            [__('Last 30 Days')]: [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
            [__('Last 3 Months')]: [moment().subtract(3, 'month').startOf('month'), moment().endOf('month')],
            [__('Last 6 Months')]: [moment().subtract(6, 'month').startOf('month'), moment().endOf('month')],
            [__('Last Year')]: [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        };
    }

    /**
     * Return the date range picker locale, translated in the current language
     * Day and month names are provided by the browser (Intl), in the current language
     * @returns {object}
     */
    locale()
    {
        const lang = typeof LANG !== 'undefined' ? LANG : 'en';
        const weekday = new Intl.DateTimeFormat(lang, { weekday: 'short' });
        const month = new Intl.DateTimeFormat(lang, { month: 'long' });
        const capitalize = (text) => text.charAt(0).toUpperCase() + text.slice(1);

        // 2023-01-01 is a Sunday, the date range picker expects days starting from Sunday
        const daysOfWeek = [...Array(7).keys()].map(i => capitalize(weekday.format(new Date(2023, 0, 1 + i))));
        const monthNames = [...Array(12).keys()].map(i => capitalize(month.format(new Date(2023, i, 1))));

        return {
            format: 'DD-MM-YYYY',
            applyLabel: __('Apply'),
            cancelLabel: __('Cancel'),
            customRangeLabel: __('Custom Range'),
            daysOfWeek: daysOfWeek,
            monthNames: monthNames,
            // Week starts on Monday, except in English
            firstDay: lang == 'en' ? 0 : 1
        };
    }

    /**
     * Convert an input element into a date range picker
     * @param {string} element - The selector of the input element to convert
     * @param {string} preset - The label of the predefined range to select by default (default: none, start and end dates are set to now)
     * @param {boolean} timePicker - Enable the time picker (default: true)
     * @param {boolean} predefinedRanges - Enable the predefined ranges (default: true)
     */
    convert(element, preset = null, timePicker = true, predefinedRanges = true)
    {
        const presets = this.presets();
        var start = moment();
        var end = moment();

        if (preset && presets[preset]) {
            start = presets[preset][0];
            end = presets[preset][1];
        }

        function cb(start, end, label) {
            var text = '';

            // Add a "value" attribute to the element with the selected date range in "YYYY-MM-DD - YYYY-MM-DD" format
            if (timePicker) {
                $(element).attr('value', start.format('YYYY-MM-DD HH:mm') + ' - ' + end.format('YYYY-MM-DD HH:mm'));
            } else {
                $(element).attr('value', start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }

            // Add "start" and "end" attributes with the selected dates as timestamps, and a "range" attribute with the selected range label
            // (label of a predefined range, or "Custom Range")
            $(element).attr('start', start.unix());
            $(element).attr('end', end.unix());
            $(element).attr('range', label);

            // Format <p> inner text to a more human readable format "DD-MM-YYYY - DD-MM-YYYY"

            // If a predefined range is selected, display its label
            if (presets[label]) {
                text = label;
            // If the start and the end date are the same, only display one date with times if enabled, otherwise display the range
            } else if (start.isSame(end, 'day')) {
                if (timePicker) {
                    if (start.isSame(end, 'second')) {
                        text = start.format('DD-MM-YYYY HH:mm');
                    } else {
                        text = sprintf(__('From %s to %s'), start.format('DD-MM-YYYY HH:mm'), end.format('HH:mm'));
                    }
                } else {
                    text = start.format('DD-MM-YYYY');
                }
            } else {
                if (timePicker) {
                    text = sprintf(__('From %s to %s'), start.format('DD-MM-YYYY HH:mm'), end.format('DD-MM-YYYY HH:mm'));
                } else {
                    text = sprintf(__('From %s to %s'), start.format('DD-MM-YYYY'), end.format('DD-MM-YYYY'));
                }
            }

            $(element).find('p').html(text);
            $(element).find('p').attr('title', text);

            // Trigger change event on the element to allow other scripts to react to the date range change
            $(element).trigger('change');
        }

        // Default configuration
        var config = {
            startDate: start,
            endDate: end,
            locale: this.locale()
        };

        // Add time picker if enabled
        if (timePicker) {
            config.timePicker = true;
            config.timePicker24Hour = true;
            // config.timePickerSeconds = true;
            config.locale.format = 'DD-MM-YYYY HH:mm';
        }

        // Add predefined ranges if enabled
        if (predefinedRanges) {
            config.ranges = presets;
        }

        // Convert the input element into a date range picker
        $(element).daterangepicker(config, cb);
    }
}
