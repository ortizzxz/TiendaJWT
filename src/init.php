<?php
use Routes\Routes;
require_once '../vendor/autoload.php';
require_once '../Config/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


Routes::index();
?>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/styles.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/cart.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/order.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/auth.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/category.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/product.css">

</head>