<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\OpeningTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Text;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Parser;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserState;

class ConstructingTextContainingTag extends ParserState {

    /**
     * @var OpeningTag
     */
    private $startTag;

    /**
     * @var string
     */
    private $text = '';

    /**
     * AwaitingText constructor.
     * @param Parser $parser
     * @param OpeningTag $startTag
     */
    public function __construct(Parser $parser, OpeningTag $startTag) {
        parent::__construct($parser);
        $this->startTag = $startTag;
    }

    public function text($cdata) {
        $this->text = $cdata;
    }

    public function endTag($name, $startOffset, $endOffset) {
        if ($name == $this->startTag->getTagName()) {
            $tag = new TextContainingTag(
                $this->startTag,
                new Text($this->text),
                new ClosingTag($startOffset, $endOffset, $name)
            );
            $this->parser->getStream()->addElement($tag);
        }
        $this->parser->setState(new AwaitingTag($this->parser));
    }

    /**
     * @return OpeningTag
     */
    public function getStartTag() {
        return $this->startTag;
    }

}
