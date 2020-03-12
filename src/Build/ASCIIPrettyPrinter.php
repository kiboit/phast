<?php
namespace Kibo\Phast\Build;

use PhpParser\PrettyPrinter;

class ASCIIPrettyPrinter extends PrettyPrinter\Standard {
    protected function escapeString($string, $quote) {
        if (null === $quote) {
            // For doc strings, don't escape newlines
            $escaped = addcslashes($string, "\t\f\v$\\");
        } else {
            $escaped = addcslashes($string, "\n\r\t\f\v$" . $quote . '\\');
        }

        // Escape other control characters
        return preg_replace_callback('/([^\x20-\x7F])(?=([0-7]?))/', function ($matches) {
            $oct = decoct(ord($matches[1]));
            if ($matches[2] !== '') {
                // If there is a trailing digit, use the full three character form
                return '\\' . str_pad($oct, 3, '0', STR_PAD_LEFT);
            }
            return '\\' . $oct;
        }, $escaped);
    }
}
