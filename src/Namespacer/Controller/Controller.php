<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2012-2013 Zend Technologies USA Inc. (http://www.zend.com)))
 */

namespace Namespacer\Controller;

use Namespacer\Model\Fixer;
use Namespacer\Model\Map;
use Namespacer\Model\Mapper;
use Namespacer\Model\Transformer;
use Zend\Mvc\Controller\AbstractActionController;

class Controller extends AbstractActionController
{
    public function createMapAction()
    {
        $mapfile = $this->params()->fromRoute('mapfile');
        $source  = $this->params()->fromRoute('source');
        $map     = array();
        $mapper  = new Mapper();
        $mapdata = $mapper->getMapDataForDirectory($source);
        $content = '<' . '?php return ' . var_export($mapdata, true) . ';';

        file_put_contents($mapfile, $content);
    }

    public function transformAction()
    {
        $mapfile     = $this->params()->fromRoute('mapfile');
        $step        = $this->params()->fromRoute('step');
        $data        = include $mapfile;
        $map         = new Map($data);
        $transformer = new Transformer($map);

        switch ($step) {
            case '3':
                $transformer->modifyContentForUseStatements();
                break;
            case '2':
                $transformer->modifyNamespaceAndClassNames();
                break;
            case '1':
                $transformer->moveFiles();
                break;
            default:
                $transformer->moveFiles();
                $transformer->modifyNamespaceAndClassNames();
                $transformer->modifyContentForUseStatements();
                break;
        }
    }

    public function fixAction()
    {
        $mapfile = $this->params()->fromRoute('mapfile');
        $source  = $this->params()->fromRoute('target');
        $data    = include $mapfile;
        $map     = new Map($data);
        $fixer   = new Fixer($map);

        $fixer->fix($source);
    }
}
