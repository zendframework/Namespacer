#!/usr/bin/env php
<?php
/**
 * ZF2 command line tool
 *
 * @link      http://github.com/zendframework/ZFTool for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
include "vendor/autoload.php";


$appConfig = array(
    'modules' => array(
        'Namespacer',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            __DIR__,
            __DIR__ . '/vendor',
        ),
    ),
);

Zend\Mvc\Application::init($appConfig)->run();
