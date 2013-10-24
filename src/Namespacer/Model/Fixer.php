<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2012-2013 Zend Technologies USA Inc. (http://www.zend.com)))
 */

namespace Namespacer\Model;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class Fixer
{
    /** 
     * @var Map 
     */
    protected $map;

    /**
     * @param Map $map 
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    /**
     * Fix all files in the directory according to the composed map
     * 
     * @param string $directory 
     */
    public function fix($directory)
    {
        $transformations = $this->map->getClassTransformations();
        $rdi = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($rdi) as $file) {
            if ($file->getExtension() != 'php') {
                continue;
            }

            $this->fixNamespacesFromMap($file->getRealPath(), $transformations);
        }
    }

    /**
     * Fixes namespaces in a given file based on the transformations obtained from the map
     * 
     * @param mixed $realPath 
     * @param array|\Traversable $transformations
     * @throws RuntimeException if the file does not exist
     */
    protected function fixNamespacesFromMap($realPath, $transformations)
    {
        if (!file_exists($realPath)) {
            throw new RuntimeException('File not found');
        }

        $src = file_get_contents($realPath);

        foreach ($transformations as $old => $new) {
            $src = str_replace($old, $new, $src);
        }

        file_put_contents($realPath, $src);
    }
}
