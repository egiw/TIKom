<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require 'vendor/autoload.php';

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/src'), $isDevMode);

$conn = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'dbname' => 'Blog'
);

$em = EntityManager::create($conn, $config);

function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}