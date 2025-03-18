<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host   = "localhost";
$user   = "root"; 
$pass   = ""; 
$dbname = "database_todolist";

$conn = new mysqli($host, $user, $pass, $dbname);

?>
