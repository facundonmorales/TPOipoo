<?php

require_once '../BDD/config.php';

$data = $database->select('personajes', 'id');

echo json_encode($data);
