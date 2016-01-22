<?php

spl_autoload_register(function ($name) {
    $namespace = 'mtoolkit\\evolution\\';
    $className = str_replace($namespace, '', $name);
    $filePath = sprintf(
        '%s%s%s%s',
        __DIR__,
        DIRECTORY_SEPARATOR,
        str_replace('\\', DIRECTORY_SEPARATOR, $className),
        '.php'
    );

    if (class_exists($name) === false && file_exists($filePath) === true) {
        require_once $filePath;
    }
});