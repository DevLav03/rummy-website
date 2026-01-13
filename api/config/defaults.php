<?php

// Application default settings

// Error reporting
error_reporting(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Timezone
date_default_timezone_set('Asia/Kolkata');

$settings = [];


// Error handler
$settings['error'] = [
    // Should be set to false for the production environment
    'display_error_details' => true,
    // Should be set to false for the test environment
    'log_errors' => true,
    // Display error details in error log
    'log_error_details' => true,
];

// Logger settings
$settings['logger'] = [
    // Log file location
    'path' => __DIR__ . '/../logs',
    // Default log level
    'level' => \Monolog\Logger::INFO,
];
//$settings['error']['display_error_details'] = true;
//$settings['logger']['level'] = \Monolog\Logger::DEBUG;


// Database settings
$settings['db'] = [
    'driver' => \Cake\Database\Driver\Mysql::class,
    'host' => 'localhost:3307', 
    'database' => '7s_rummy_db',
    'username' => 'root',
    'password' => '', 
    'encoding' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    // Enable identifier quoting
    'quoteIdentifiers' => true,
    // Set to null to use MySQL servers timezone
    'timezone' => null,
    // Disable meta data cache
    'cacheMetadata' => false,
    // Disable query logging
    'log' => false,
    // PDO options
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Convert numeric values to strings when fetching.
        // Since PHP 8.1 integers and floats in result sets will be returned using native PHP types.
        // This option restores the previous behavior.
        PDO::ATTR_STRINGIFY_FETCHES => true,
    ],
];

// Server Database settings
// $settings['db'] = [
//     'driver' => \Cake\Database\Driver\Mysql::class,
//     'host' => '7srummy.com', 
//     'database' => '7s_rummyadmindb',
//     'username' => '7srummydbusr',
//     'password' => '7SrUmoo09#4Jkl', 
//     'encoding' => 'utf8mb4',
//     'collation' => 'utf8mb4_unicode_ci',
//     // Enable identifier quoting
//     'quoteIdentifiers' => true,
//     // Set to null to use MySQL servers timezone
//     'timezone' => null,
//     // Disable meta data cache
//     'cacheMetadata' => false,
//     // Disable query logging
//     'log' => false,
//     // PDO options
//     'flags' => [
//         // Turn off persistent connections
//         PDO::ATTR_PERSISTENT => false,
//         // Enable exceptions
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         // Emulate prepared statements
//         PDO::ATTR_EMULATE_PREPARES => true,
//         // Set default fetch mode to array
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//         // Convert numeric values to strings when fetching.
//         // Since PHP 8.1 integers and floats in result sets will be returned using native PHP types.
//         // This option restores the previous behavior.
//         PDO::ATTR_STRINGIFY_FETCHES => true,
//     ],
// ];


// Email settings * 7S Rummy Email Details change
$settings['email'] = [
    'host' => 'smtp.gmail.com',
    'port'=> '587',
    'username' => 'vivekanandar068@gmail.com',
    'password'=> 'crmdqphcgqmwepyf',
    'from_name' => 'Vinora',
    'from_email' => 'vivekanandar068@gmail.com',
    'smtp_auth' => true,
    'debug' => 0
];


// Console commands
$settings['commands'] = [
    \App\Console\ExampleCommand::class,
    \App\Console\SetupCommand::class,
];

$settings['JWT'] = [
    'JWT_SECRET_KEY' => 'token_secret_key_123@321'
];

return $settings;
