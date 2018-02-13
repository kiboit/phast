<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserState;
use Masterminds\HTML5\Elements;

class AwaitingTag extends ParserState {

    public function startTag($name, $attributes, $startOffset, $endOffset, $selfClosing = false) {
        $this->addNonTagElement($startOffset);

        $tag = new Tag($name, $attributes);
        $tag->setOriginalString(
            $this->inputStream->getSubString($startOffset, $endOffset)
        );

        if ($name == 'script' || $name == 'style') {
            $newState = new ConstructingTextContainingTag($this->parser, $tag);
            $this->parser->setState($newState);
        } else {
            $this->htmlStream->addElement($tag);
            $this->parser->setLastInsertedByteOffset($endOffset);
        }

        return Elements::element($name);
    }

    public function endTag($name, $startOffset, $endOffset) {
        $this->addNonTagElement($startOffset);
        $tag = new ClosingTag($name);
        $tag->setOriginalString(
            $this->inputStream->getSubString($startOffset, $endOffset)
        );
        $this->htmlStream->addElement($tag);
        $this->parser->setLastInsertedByteOffset($endOffset);
    }

    public function eof() {
        $this->addNonTagElement();
    }

    private function addNonTagElement($currentStartOffset = null) {
        $lastInsertedOffset = $this->parser->getLastInsertedByteOffset();
        if (is_null($currentStartOffset)) {
            $text = $this->inputStream->getSubString($lastInsertedOffset);
        } else {
            $text = $this->inputStream->getSubString($lastInsertedOffset, $currentStartOffset);
        }
        if (empty ($text)) {
            return;
        }
        $textElement = new Element();
        $textElement->setOriginalString($text);
        $this->htmlStream->addElement($textElement);
        $this->parser->setLastInsertedByteOffset($lastInsertedOffset + strlen($text) - 1);
    }


}
