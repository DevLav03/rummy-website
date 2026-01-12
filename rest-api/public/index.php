<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Access, Origin, Content-Type, X-Auth-Token');
(require __DIR__ . '/../config/bootstrap.php')->run();
