<?php

namespace Arek2k\ElasticApmSymfony\Helper;

class StringHelper
{
    public static function match($haystack, $needle, $dash = '*')
    {
        $haystack = (string)$haystack;
        $needle = (string)$needle;

        if (strlen($haystack) !== strlen($needle)) {
            return false;
        }

        for ($i = 0; $i < strlen($haystack); $i++) {
            if ($haystack[$i] != $dash) {
                if ($haystack[$i] !== $needle[$i]) {
                    return false;
                }
            }
        }

        return true;
    }
}
