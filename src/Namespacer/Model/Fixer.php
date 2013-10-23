<?php

namespace Namespacer\Model;

class Fixer {

	/** @var \Namespacer\Model\Map */
    protected $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

	public function fix($directory)
	{
		$rdi = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        foreach (new \RecursiveIteratorIterator($rdi) as $file) {
            /** @var $file \SplFileInfo */
            if ($file->getExtension() != 'php') {
                continue;
            }

            $this->fixNamespacesFromMap($file->getRealPath());
        }
	}	

	protected function fixNamespacesFromMap($realPath)
	{
		$transformations = $this->map->getClassTransformations();
		if (!file_exists($realPath)) {
			throw new Exception('File not found');
		}

		$src = file_get_contents($realPath);
		foreach ($transformations as $old => $new) {
			$src = str_replace($old, $new, $src);
		}	

		file_put_contents($realPath, $src);
	}
}