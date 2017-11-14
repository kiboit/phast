<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;

abstract class ExternalAppImageFilter implements ImageFilter {

    /**
     * @var array
     */
    protected $config;

    /**
     * @param Image $image
     * @return bool
     */
    abstract protected function shouldApply(Image $image);

    /**
     * @return string
     */
    abstract protected function getCommand();

    /**
     * PNGCompressionImageFilter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function transformImage(Image $image) {
        if (!$this->shouldApply($image)) {
            return $image;
        }

        $command = $this->getCommand();

        $proc = proc_open($this->getCommand(), [['pipe', 'r'], ['pipe', 'w']], $pipes);

        if (!is_resource($proc)) {
            return $image;
        }

        fwrite($pipes[0], $image->getAsString());
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $status = proc_close($proc);

        if ($status != 0) {
            throw new \RuntimeException("External image processing command failed with status {$status}: {$command}");
        }

        if ($compressed == '') {
            throw new \RuntimeException("External image processing command did not output anything: {$command}");
        }

        $newImage = new DummyImage();
        $newImage->setImageString($compressed);
        $newImage->setType(Image::TYPE_PNG);

        return $newImage;
    }

}
