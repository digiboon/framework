<?php

/**
 * Plugin Name: Framework
 * Plugin URI: https://digitalbaboon.com/framework
 * Description: Making WordPress theme development less of a bother.
 * Version: 2.5
 * Author: Digital Baboon LLC
 * Author URI: https://digitalbaboon.com
 * License: MIT
 */

if(!version_compare(PHP_VERSION, '7.4.0', '<')) {

	require __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/framework.php';

}