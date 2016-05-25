<?php

namespace Namespacer\Model;

use Zend\Code\Scanner\FileScanner;

class LegacyExtension
{
    /** @var \Namespacer\Model\Map */
    protected $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function createLegacyClasses($directory = false)
    {
        $fileRenamings = $this->map->getExtensionMap($directory);

        foreach ($fileRenamings as $legacyPath => $data) {
            $legacyDir = dirname($legacyPath);
            if (!file_exists($legacyDir)) {
                mkdir($legacyDir, 0777, true);
            }
            touch($legacyPath);

            $this->createLegacyFile($legacyPath, $data);
        }
    }

    protected function createLegacyFile($file, $data)
    {
        $tokens = token_get_all(file_get_contents($data['original_file']));

        $contents = '';
        $token = reset($tokens);
        do {
            if (T_TRAIT === $token[0]) {
                $contents .= $token[1] . ' ' . $data['class'];
                $contents .= "\n{\n";
                $contents .= "    use \\" . $data['extends'] . ";";
                $contents .= "\n}\n";
                break;
            }
            if (T_CLASS === $token[0] || T_INTERFACE === $token[0]) {
                $contents .= $token[1] . ' ' . $data['class'];
                $contents .= " extends \\" . $data['extends'];
                $contents .= "\n{\n}\n";
                break;
            } else {
                $contents .= (is_array($token)) ? $token[1] : $token;
            }
        } while ($token = next($tokens));

        file_put_contents($file, $contents);
    }
}
