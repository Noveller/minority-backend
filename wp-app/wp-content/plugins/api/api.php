<?php
/**
 * @package Akismet
 */
/*
Plugin Name: minority api
*/
require_once 'PostsController.php';
require_once 'PressController.php';

function prefix_register_routes() {
    $classNames = [PostsController::class, PressController::class];

    foreach ($classNames as $className) {
        $instance = new $className;
        $instance->register_routes();
    }
}

add_action('rest_api_init', 'prefix_register_routes');