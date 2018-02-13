<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Parser;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserState;

class ConstructingTextContainingTag extends ParserState {

    /**
     * @var Tag
     */
    private $tag;

    /**
     * AwaitingText constructor.
     * @param Parser $parser
     * @param Tag $tag
     */
    public function __construct(Parser $parser, Tag $tag) {
        parent::__construct($parser);
        $this->tag = $tag;
    }

    public function text($cdata) {
        $this->tag->setTextContent($cdata);
    }

    public function endTag($name, $startOffset, $endOffset) {
        if ($name == $this->tag->getTagName()) {
            $this->finishTag($startOffset, $endOffset);
        }
        $this->parser->setState(new AwaitingTag($this->parser));
    }

    /**
     * @return Tag
     */
    public function getTag() {
        return $this->tag;
    }

    private function finishTag($startOffset, $endOffset) {
        $closingTag = $this->inputStream->getSubString($startOffset, $endOffset);
        $newTag = $this->tag->withClosingTag($closingTag);
        $this->parser->getHtmlStream()->addElement($newTag);
        $lastOffset = $this->parser->getLastInsertedByteOffset();
        $this->parser->setLastInsertedByteOffset($lastOffset + strlen($newTag->toString()) - 1);
    }

}
