<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
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
    abstract protected function getDefaultBinName();

    /**
     * @return string
     */
    abstract protected function getCmdArgs();

    /**
     * ExternalAppImageFilter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function getCacheSalt(array $request) {
        try {
            return md5($this->getFullCommand());
        } catch (ImageProcessingException $e) {
            return 'binary-not-found';
        }
    }


    public function transformImage(Image $image, array $request) {
        if (!$this->shouldApply($image)) {
            $this->logger()->info('Will not apply');
            return $image;
        }

        $command = $this->getFullCommand();

        $this->logger()->info('Applying {command}', ['command' => $command]);

        $proc = proc_open($command, [['pipe', 'r'], ['pipe', 'w']], $pipes);

        if (!is_resource($proc)) {
            throw new ImageProcessingException("Could not open process for $command");
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
        $newImage->setType($image->getType());

        return $newImage;
    }

    private function getFullCommand() {
        return $this->getBin() . $this->getCmdArgs();
    }

    private function getBin() {
        $bin = $this->getBinFromConfig();
        if ($bin) {
            return $bin;
        }
        return $this->findBinInEnv();
    }

    private function getBinFromConfig() {
        if (isset ($this->config['binpath'])) {
            if (!file_exists($this->config['binpath'])) {
                throw new ImageProcessingException("Executable not found: " . $this->config['binpath']);
            }
            return $this->config['binpath'];
        }
        return false;
    }

    private function findBinInEnv() {
        $defaultBin = $this->getDefaultBinName();
        $paths = array_merge(explode(':', getenv('PATH')), $this->getSearchPaths());
        foreach ($paths as $path) {
            $bin = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $defaultBin;
            if (@is_executable($bin)) {
                return $bin;
            }
        }
        throw new ImageProcessingException("Executable not found: " . $defaultBin);
    }

    protected function getSearchPaths() {
        return ['/usr/local/bin', '/usr/bin'];
    }
}
