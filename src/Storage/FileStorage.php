<?php

namespace WebProfiler\Storage;

use DirectoryIterator;
use RuntimeException;

class FileStorage extends \DebugBar\Storage\FileStorage implements Storage
{
    public function rotate(int $maxKeep): void
    {
        $files = [];

        /** @var DirectoryIterator $file */
        foreach (new DirectoryIterator($this->dirname) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $files[] = [
                'time' => $file->getMTime(),
                'path' => $file->getPathname(),
            ];
        }

        // Sort the files, newest first
        usort($files, function ($a, $b) {
            return $a['time'] > $b['time'];
        });

        $totalFiles = count($files);

        for ($i = $maxKeep - 1; $i < $totalFiles; $i++) {
            $file = $files[$i];
            $path = $file['path'];

            if (false === unlink($path)) {
                throw new RuntimeException(sprintf('Could not delete %s', $path));
            }
        }
    }
}
