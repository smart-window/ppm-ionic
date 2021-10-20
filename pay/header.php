<?php  
require __DIR__ . '/vendor/autoload.php';
if (!defined('PAY_PAGE_CONFIG')) {
    define('PAY_PAGE_CONFIG', realpath('data.php'));
}