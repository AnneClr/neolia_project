<?php
/**
 * Dispatcher
 * @version 1.0.0
 *  - Instantiate Kernel
 *  - Generate Response object
 *  - Send Http Response to web server
 */

require_once('./../vendor/autoload.php');

use Aelion\Kernel;

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$kernel = Kernel::create();
$response = $kernel->processRequest();
echo $response->send();
