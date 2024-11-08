<?php
try{
    $host = '127.0.0.1';
    $dbname = 'miproyecto';
    $user = 'root';
    $passwordDB =  '';
    $port = '3306';

    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $passwordDB);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOexception $e){
    die("Error en la conexion a la base de datos: " .  $e->getMessage());

}
?>