<?php 
require_once("./Librerias/Medoo.php");

use Medoo\Medoo;

$database = new Medoo([
    'type' => 'mysql',
    'host' => 'localhost',
    'database' => 'torneo_duelos',
    'username' => 'root',
    'password' => ''
]);