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

    public function endTag($name, $originalString) {
        if ($name == $this->tag->getTagName()) {
            $this->parser->getStream()->addElement($this->tag->withClosingTag($originalString));
        }
        $this->parser->setState(new AwaitingTag($this->parser));
    }

    /**
     * @return Tag
     */
    public function getTag() {
        return $this->tag;
    }

}
