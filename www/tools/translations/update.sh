#!/bin/bash
#
# Extract translatable strings from the source code to translations/<domain>.pot,
# then merge them into every translations/<domain>.<lang>.po file.
#
# Domains:
#   - messages:   PHP code (controllers, views)
#   - javascript: JS code (public/resources/js), sent to the browser
#
# Requires gettext tools (xgettext, msgmerge), available in the motionui container:
#   docker exec motionui /var/www/motionui/tools/translations/update.sh
#
# To add a new language (e.g. German):
#   msginit --no-translator --locale=de --input=translations/messages.pot --output=translations/messages.de.po
#   msginit --no-translator --locale=de --input=translations/javascript.pot --output=translations/javascript.de.po

set -euo pipefail

cd "$(dirname "$0")/../.."

XGETTEXT_OPTIONS=(
    --files-from=-
    --from-code=UTF-8
    --keyword
    --keyword=__
    --keyword=_x:1,2c
    --keyword=_n:1,2
    --add-location=file
    --sort-by-file
    --package-name=Motion-UI
    --package-version="$(cat version)"
    --msgid-bugs-address=https://github.com/lbr38/motion-UI/issues
)

find controllers views config -name '*.php' | sort | xgettext "${XGETTEXT_OPTIONS[@]}" \
    --language=PHP \
    --output=translations/messages.pot

find public/resources/js -name '*.js' -not -path '*/libs/*' | sort | xgettext "${XGETTEXT_OPTIONS[@]}" \
    --language=JavaScript \
    --output=translations/javascript.pot

for domain in messages javascript; do
    for po in translations/"$domain".*.po; do
        [ -f "$po" ] || continue
        msgmerge --quiet --update --backup=none "$po" translations/"$domain".pot
        echo "Updated $po: $(msgfmt --statistics --output-file=/dev/null "$po" 2>&1)"
    done
done
