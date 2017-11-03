<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;

class PNGQuantCompressionImageFilter implements ImageFilter {

    /**
     * @var array
     */
    private $config;

    /**
     * PNGCompressionImageFilter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function transformImage(Image $image) {
        if (!$this->config['enabled'] || $image->getType() != Image::TYPE_PNG) {
            return $image;
        }
        $cmd = $this->config['cmdpath'];
        if (isset ($this->config['quality'])) {
            $cmd .= ' --quality=' . $this->config['quality'];
        }
        if (isset ($this->config['speed'])) {
            $cmd .= ' --speed=' . $this->config['speed'];
        }
        $cmd .= ' - ';
        $proc = proc_open($cmd, [['pipe', 'r'], ['pipe', 'w']], $pipes);
        if (!is_resource($proc)) {
            return $image;
        }
        fwrite($pipes[0], $image->getAsString());
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($proc);

        $newImage = new DummyImage();
        $newImage->setImageString($compressed);
        $newImage->setType(Image::TYPE_PNG);
        return $newImage;
    }

}
