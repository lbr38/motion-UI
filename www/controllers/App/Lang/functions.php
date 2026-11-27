<?php
/**
 *  Translation functions, following gettext naming conventions
 *  The source strings are extracted to translations/messages.pot by tools/translations/update.sh
 */

/**
 *  Translate a string
 */
function __(string $text): string
{
    return \Controllers\App\Lang::translate($text);
}

/**
 *  Translate a string with a context, to disambiguate identical strings having different meanings
 */
function _x(string $text, string $context): string
{
    return \Controllers\App\Lang::translate($text, $context);
}

/**
 *  Translate a string with plural forms
 */
function _n(string $singular, string $plural, int $count): string
{
    return \Controllers\App\Lang::translatePlural($singular, $plural, $count);
}
