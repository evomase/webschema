<?php
//Core classes
spl_autoload_register(function ($class) {
    $class = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';

    if (!file_exists($class)) {
        return;
    }

    include "$class";
});

//Unit test classes
spl_autoload_register(function ($class) {
    $class = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    if (!file_exists($class)) {
        return;
    }

    include "$class";
});