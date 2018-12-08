<?php

$loader = require __DIR__.'/vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationRegistry;
use Scanner\ScanCommand;
use Symfony\Component\Console\Application;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$application = (new Application('Scanner'))
    ->add(new ScanCommand('scan'))
    ->getApplication()
    ->setDefaultCommand('scan', true)
;

try {
    $application->run();
} catch (Exception $e) {
    echo 'Error during running Scanner';
}
