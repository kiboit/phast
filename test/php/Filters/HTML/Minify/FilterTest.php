<?php
namespace Kibo\Phast\Filters\HTML\Minify;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testMinify() {
        $html = '
            <html>
                <head>
                    <title>Hello,  World!</title>
                </head>
                <body>
                    <h1>
                        Here
                        we
                        go!
                    </h1>
                    <textarea>  And  some  more  </TEXTAREA>
                    Minify  this
                    <pre>  This is <b>v  e  r  y</b> cool  </pre>
                </body>
            </html>
        ';
        $actual = $this->applyFilter($html, true);
        $expected = ' <html> <head> <title>Hello, World!</title> </head> <body> <h1> Here we go! </h1> <textarea>  And  some  more  </TEXTAREA> Minify this <pre>  This is <b>v  e  r  y</b> cool  </pre> </body> </html>';
        $this->assertStringStartsWith($expected, $actual);
    }

}
