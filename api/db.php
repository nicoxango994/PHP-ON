<?php
$servername = "byfj7hqfmyfpkpm2vtef-mysql.services.clever-cloud.com";
$dbname = "byfj7hqfmyfpkpm2vtef";
$username = "uhbb4yjyfoe2iai2";
$password = "A8eRZGeUTAb9IcDIJdKR";

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(['message' => "Conexion exitosa"]);
} 
catch(PDOException $e) 
{
    echo json_encode(['message' => "Connection failed: " . $e->getMessage()]);
    exit;
}
?>