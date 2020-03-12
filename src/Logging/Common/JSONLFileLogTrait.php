<?php

namespace Kibo\Phast\Logging\Common;

trait JSONLFileLogTrait {
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $filename;

    /**
     * JSONLFileLogWriter constructor.
     * @param string $dir
     * @param string $suffix
     */
    public function __construct($dir, $suffix) {
        $this->dir = $dir;
        $suffix = preg_replace('/[^0-9A-Za-z_-]/', '', (string) $suffix);
        if (!empty($suffix)) {
            $suffix = '-' . $suffix;
        }
        $this->filename = $this->dir . '/log' . $suffix . '.jsonl';
    }
}
