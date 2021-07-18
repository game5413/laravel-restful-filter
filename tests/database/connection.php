<?php

require __DIR__.'/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => dirname(__FILE__).'/test.db'
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();
