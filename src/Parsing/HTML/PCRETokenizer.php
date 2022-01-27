<?php

namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Comment;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Junk;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class PCRETokenizer {
    private $mainPattern = '~
        # Allow duplicate names for subpatterns
        (?J)

        (
            @@COMMENT |
            @@SCRIPT |
            @@STYLE |
            @@CLOSING_TAG |
            @@TAG
        )
    ~Xxsi';

    private $attributePattern = '~
        @attr
    ~Xxsi';

    private $subroutines = [
        'COMMENT' => '
            <!--.*?-->
        ',
        'SCRIPT' => "
            (?= <script[\s>]) @@TAG
            (?'body' .*? )
            (?'closing_tag' </script/?+(?:\s[^a-z>]*+)?+> )
        ",
        'STYLE' => "
            (?= <style[\s>]) @@TAG
            (?'body' .*? )
            (?'closing_tag' </style/?+(?:\s[^a-z>]*+)?+> )
        ",
        'TAG' => "
            < @@tag_name \s*+ @@attrs? @tag_end
        ",
        'tag_name' => "
            [^\s>]++
        ",
        'attrs' => '
            (?: @attr )*+
        ',
        'attr' => "
            \s*+
            @@attr_name
            (?: \s*+ = \s*+ @attr_value )?
        ",
        'attr_name' => "
            [^\s>][^\s>=]*+
        ",
        'attr_value' => "
            (?|
                \"(?'attr_value'[^\"]*+)\" |
                ' (?'attr_value' [^']*+) ' |
                (?'attr_value' [^\s>]*+)
            )
        ",
        'tag_end' => "
            \s*+ >
        ",
        'CLOSING_TAG' => '
            </ @@tag_name [^>]*+ >
        ',
    ];

    public function __construct() {
        $this->mainPattern = $this->compilePattern($this->mainPattern, $this->subroutines);
        $this->attributePattern = $this->compilePattern($this->attributePattern, $this->subroutines);
    }

    public function tokenize($data) {
        $offset = 0;

        while (preg_match($this->mainPattern, $data, $match, PREG_OFFSET_CAPTURE, $offset)) {
            if ($match[0][1] > $offset) {
                $element = new Junk();
                $element->originalString = substr($data, $offset, $match[0][1] - $offset);
                yield $element;
            }
            if (!empty($match['COMMENT'][0])) {
                $element = new Comment();
                $element->originalString = $match[0][0];
            } elseif (!empty($match['TAG'][0])
                      || !empty($match['SCRIPT'][0])
                      || !empty($match['STYLE'][0])
            ) {
                $attributes = $match['attrs'][0] === '' ? [] : $this->parseAttributes($match['attrs'][0]);
                $element = new Tag($match['tag_name'][0], $attributes);
                $element->originalString = $match['TAG'][0];
                if (isset($match['body'][1]) && $match['body'][1] != -1) {
                    $element->setTextContent($match['body'][0]);
                    $element = $element->withClosingTag($match['closing_tag'][0]);
                }
            } elseif (!empty($match['CLOSING_TAG'][0])) {
                $element = new ClosingTag($match['tag_name'][0]);
                $element->originalString = $match[0][0];
            } else {
                throw new RuntimeException("Unhandled match:\n" . JSON::prettyEncode($match));
            }
            yield $element;
            $offset = $match[0][1] + strlen($match[0][0]);
        }

        if ($offset < strlen($data)) {
            $element = new Junk();
            $element->originalString = substr($data, $offset);
            yield $element;
        }
    }

    private function parseAttributes($str) {
        $matches = $this->repeatMatch($this->attributePattern, $str);

        foreach ($matches as $match) {
            yield
                $match['attr_name'][0] =>
                isset($match['attr_value'][0]) ? html_entity_decode($match['attr_value'][0], ENT_QUOTES, 'UTF-8') : '';
        }
    }

    private function repeatMatch($pattern, $subject) {
        $offset = 0;

        while (preg_match($pattern, $subject, $match, PREG_OFFSET_CAPTURE, $offset)) {
            yield $match;
            $offset = $match[0][1] + strlen($match[0][0]);
        }

        if ($offset < strlen($subject) - 1) {
            throw new RuntimeException('Unmatched part of subject: ' . substr($subject, $offset));
        }
    }

    /**
     * Replace subroutines in patterns
     */
    private function compilePattern($pattern, array $subroutines) {
        return preg_replace_callback('/@(@?)(\w+)/', function ($match) use ($subroutines) {
            $capture = !empty($match[1]);
            $ref = $match[2];

            if (!isset($subroutines[$ref])) {
                throw new RuntimeException(
                    "Unknown pattern '$ref' used, or circular reference"
                );
            }

            $subroutine = $subroutines[$ref];
            unset($subroutines[$ref]);

            $replace = $this->compilePattern($subroutine, $subroutines);

            if ($capture) {
                $replace = "(?'$ref'$replace)";
            } else {
                $replace = "(?:$replace)";
            }

            return $replace;
        }, $pattern);
    }
}
