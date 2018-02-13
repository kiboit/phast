<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Text;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Parser;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserState;

class ConstructingTextContainingTag extends ParserState {

    /**
     * @var Tag
     */
    private $startTag;

    /**
     * @var string
     */
    private $text = '';

    /**
     * AwaitingText constructor.
     * @param Parser $parser
     * @param Tag $startTag
     */
    public function __construct(Parser $parser, Tag $startTag) {
        parent::__construct($parser);
        $this->startTag = $startTag;
    }

    public function text($cdata) {
        $this->text = $cdata;
    }

    public function endTag($name, $originalString) {
        if ($name == $this->startTag->getTagName()) {
            $closing = new ClosingTag($name);
            $tag = new TextContainingTag(
                $this->startTag,
                $closing,
                new Text($this->text)
            );
            $this->parser->getStream()->addElement($tag);
        }
        $this->parser->setState(new AwaitingTag($this->parser));
    }

    /**
     * @return Tag
     */
    public function getStartTag() {
        return $this->startTag;
    }

}
