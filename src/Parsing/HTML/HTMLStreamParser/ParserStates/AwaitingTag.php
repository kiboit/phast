<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserState;
use Masterminds\HTML5\Elements;

class AwaitingTag extends ParserState {

    public function startTag($name, $attributes, $startOffset, $endOffset, $selfClosing = false) {
        $this->addNonTagElement($startOffset - 1);

        $tag = new Tag($name, $attributes);
        $tag->setOriginalString(
            $this->inputStream->getSubString($startOffset, $endOffset)
        );
        echo "Found-tag: $startOffset-$endOffset\n";
        echo "Text: " . $tag->toString() . "\n\n";

        if ($name == 'script' || $name == 'style') {
            $newState = new ConstructingTextContainingTag($this->parser, $tag);
            $this->parser->setState($newState);
        } else {
            $this->htmlStream->addElement($tag);
            $this->parser->setCaretPosition($endOffset + 1);
        }

        return Elements::element($name);
    }

    public function endTag($name, $startOffset, $endOffset) {
        $this->addNonTagElement($startOffset - 1);
        $tag = new ClosingTag($name);
        $tag->setOriginalString(
            $this->inputStream->getSubString($startOffset, $endOffset)
        );
        echo "Add-end: $startOffset-$endOffset\n";
        echo "Text: " . $tag->toString() . "\n\n";
        $this->htmlStream->addElement($tag);
        $this->parser->setCaretPosition($endOffset + 1);
    }

    public function eof() {
        $this->addNonTagElement();
    }

    private function addNonTagElement($currentStartOffset = null) {
        $caretOffset = $this->parser->getCaretPosition();
        if (is_null($currentStartOffset)) {
            $text = $this->inputStream->getSubString($caretOffset);
        } else {
            $text = $this->inputStream->getSubString($caretOffset, $currentStartOffset);
        }
        echo "Add-text: $caretOffset-$currentStartOffset\n";
        echo "Text: $text\n\n";
        if (empty ($text)) {
            return;
        }
        $textElement = new Element();
        $textElement->setOriginalString($text);
        $this->htmlStream->addElement($textElement);
        $this->parser->setCaretPosition($caretOffset + strlen($text));
    }


}
