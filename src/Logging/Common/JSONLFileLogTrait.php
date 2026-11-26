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

    /**
     * Open the log file for reading and writing, refusing files that this
     * process did not create. The log directory may be shared with other
     * users (it defaults to a directory in the system temp dir), so another
     * user could have placed a file, symlink or hard link at the log path.
     *
     * A new file is created exclusively, with mode 0600. An existing file is
     * only opened if it is a regular file with a single link and mode 0600,
     * owned by the effective user (if the posix extension is available), and
     * it is still the same file after opening it.
     *
     * @param bool $create Create the file if it doesn't exist
     * @return resource|false
     */
    private function openLogFile($create) {
        if ($create) {
            $umask = umask(0077);
            try {
                $fp = @fopen($this->filename, 'x+');
            } finally {
                umask($umask);
            }
            if ($fp) {
                $stat = @fstat($fp);
                if ($stat && $this->isOwnLogFile($stat)) {
                    return $fp;
                }
                fclose($fp);
                return false;
            }
        }

        // fstat() after opening would describe a symlink's target, which may
        // be any 0600 file of ours, and opening a FIFO could block; so check
        // the path itself first, then that we opened that same file.
        $before = @lstat($this->filename);
        if (!$before || !$this->isOwnLogFile($before)) {
            return false;
        }
        $fp = @fopen($this->filename, 'r+');
        if (!$fp) {
            return false;
        }
        $after = @fstat($fp);
        if (!$after
            || $after['dev'] !== $before['dev']
            || $after['ino'] !== $before['ino']
            || !$this->isOwnLogFile($after)
        ) {
            fclose($fp);
            return false;
        }
        return $fp;
    }

    private function isOwnLogFile(array $stat) {
        if (($stat['mode'] & 0170000) !== 0100000) {
            return false;
        }
        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows doesn't report meaningful modes, links or owners.
            return true;
        }
        if (($stat['mode'] & 07777) !== 0600 || $stat['nlink'] !== 1) {
            return false;
        }
        if (function_exists('posix_geteuid') && $stat['uid'] !== posix_geteuid()) {
            return false;
        }
        return true;
    }
}
