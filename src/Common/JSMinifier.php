<?php
namespace Kibo\Phast\Common;

use Kibo\Phast\JSMin\JSMin;

class JSMinifier extends JSMin {
    protected $removeLicenseHeaders;

    public function __construct($input, $removeLicenseHeaders = false) {
        parent::__construct($input);
        $this->removeLicenseHeaders = $removeLicenseHeaders;
    }

    protected function consumeMultipleLineComment() {
        parent::consumeMultipleLineComment();
        if ($this->removeLicenseHeaders) {
            $this->keptComment = preg_replace('~/\*!.*?\*/~s', '', $this->keptComment);
        }
    }
}
