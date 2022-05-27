<?php

$path = (defined("PATH")) ? PATH : "";
$config = json_decode(file_get_contents($path . "utils/config.json"), true);

/*
 * SQL-Verbindung
 */
define("DATABASE_HOST", $config['db_host']);
define("DATABASE_NAME", $config['db_name']);
define("DATABASE_PASSWORD", $config['db_password']);
define("DATABASE_PORT", $config['db_port']);
define("DATABASE_USER", $config['db_user']);