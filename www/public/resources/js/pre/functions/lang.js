/**
 *  Translation functions, following gettext naming conventions (same as the PHP ones)
 *  The source strings are extracted to translations/javascript.pot by tools/translations/update.sh
 *
 *  Translations of the current language are provided by the server in the TRANSLATIONS object (see views/includes/head.inc.php):
 *  - simple strings:  "text": "translation"
 *  - with a context:  "context\u0004text": "translation"
 *  - plural strings:  "singular|plural": "form0|form1|..."
 */

/**
 *  Translate a string
 *  Return the source string if no translation is found
 */
function __(text)
{
    if (typeof TRANSLATIONS !== 'undefined' && TRANSLATIONS[text]) {
        return TRANSLATIONS[text];
    }

    return text;
}

/**
 *  Translate a string with a context, to disambiguate identical strings having different meanings
 *  Return the source string if no translation is found
 */
function _x(text, context)
{
    var id = context + '\u0004' + text;

    if (typeof TRANSLATIONS !== 'undefined' && TRANSLATIONS[id]) {
        return TRANSLATIONS[id];
    }

    return text;
}

/**
 *  Replace %s and %d placeholders of a (translated) string with the specified values, in order
 *  Positional placeholders (%1$s, %2$d...) are also supported, so that a translation can change the order of the values (same as PHP)
 *  e.g. sprintf(__('Delete user %s?'), username)
 */
function sprintf(format, ...values)
{
    var i = 0;

    return format.replace(/%(?:(\d+)\$)?[sd]/g, function (match, position) {
        var index = position !== undefined ? parseInt(position) - 1 : i++;

        return index >= 0 && index < values.length ? values[index] : match;
    });
}

/**
 *  Translate a string with plural forms
 *  Return the source singular or plural string if no translation is found
 *  Plural form selection relies on Intl.PluralRules: 'one' selects the first form, anything else the second one,
 *  which is enough for languages having 2 plural forms (en, fr, ...)
 */
function _n(singular, plural, count)
{
    var id = singular + '|' + plural;

    if (typeof TRANSLATIONS === 'undefined' || !TRANSLATIONS[id]) {
        return count == 1 ? singular : plural;
    }

    var forms = TRANSLATIONS[id].split('|');
    var lang = typeof LANG !== 'undefined' ? LANG : 'en';
    var index = new Intl.PluralRules(lang).select(count) == 'one' ? 0 : 1;

    return forms[Math.min(index, forms.length - 1)];
}
