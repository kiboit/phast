<?php

namespace Kibo\Phast\FileSystem;

class FileSystemAccessor {

    /**
     * @param $path
     * @return bool|string
     * @see realpath()
     */
    public function realpath($path) {
        return realpath($path);
    }

    /**
     * @param $filename
     * @return bool|string
     * @see file_get_contents()
     */
    public function file_get_contents($filename) {
        return @file_get_contents($filename);
    }

}
