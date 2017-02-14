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
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}