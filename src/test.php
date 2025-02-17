<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use APIServices\ApiProductController;

$test = new ApiProductController();
var_dump($test);
