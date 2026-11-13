<?php

namespace Controllers\App\Lang;

/**
 *  Symfony PO loader with msgctxt support
 *  Symfony parses contexts but discards them, so two strings with the same msgid and a different context
 *  would overwrite each other. Context is merged into the id, using the same EOT separator as gettext.
 */
class PoFileLoader extends \Symfony\Component\Translation\Loader\PoFileLoader
{
    protected function loadResource(string $resource): array
    {
        $content = file_get_contents($resource);

        // msgctxt "context"
        // msgid "text"
        // becomes:
        // msgid "context\004text"
        $content = preg_replace('/^msgctxt "(.*)"\R+msgid "/m', 'msgid "$1\\\\004', $content);

        return parent::loadResource('data://text/plain;base64,' . base64_encode($content));
    }
}
