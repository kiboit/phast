<?php


namespace Kibo\Phast\ValueObjects;


use Kibo\Phast\Common\ObjectifiedFunctions;

class PhastJavaScript {

    /**
     * @var string
     */
    private $filename;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * PhastJavaScript constructor.
     * @param string $filename
     * @param ObjectifiedFunctions $funcs
     */
    public function __construct($filename, ObjectifiedFunctions $funcs = null) {
        $this->filename = $filename;
        $this->funcs = $funcs ? $funcs : new ObjectifiedFunctions();
    }


    /**
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @return bool|string
     */
    public function getContents() {
        return $this->funcs->file_get_contents($this->filename);
    }

}
