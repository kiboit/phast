<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\Logging\LoggingTrait;

abstract class ExternalAppImageFilter implements ImageFilter {
    use LoggingTrait;

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

    public function transformImage(Image $image, array $request) {
        if (!$this->shouldApply($image)) {
            $this->logger()->info('Will not apply');
            return $image;
        }

        if (!file_exists($this->config['cmdpath'])) {
            throw new ImageProcessingException("Executable not found: " . $this->config['cmdpath']);
        }
        $command = $this->getCommand();

        $this->logger()->info('Applying {command}', ['command' => $command]);

        $proc = proc_open($command, [['pipe', 'r'], ['pipe', 'w']], $pipes);

        if (!is_resource($proc)) {
            throw new ImageProcessingException("Could open process for $command");
        }

        fwrite($pipes[0], $image->getAsString());
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $status = proc_close($proc);

        if ($status != 0) {
            throw new ImageProcessingException(
                "External image processing command failed with status {$status}: {$command}"
            );
        }

        if ($compressed == '') {
            throw new ImageProcessingException(
                "External image processing command did not output anything: {$command}"
            );
        }

        $newImage = new DummyImage();
        $newImage->setImageString($compressed);
        $newImage->setType(Image::TYPE_PNG);

        return $newImage;
    }

}
