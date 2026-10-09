<?php

echo "hello";


error_reporting(E_ALL);

ini_set("display_errors", 1);

//echo phpinfo();
//$mysqlconn->query($not_safe);
$dbuser = "dmc186";
$dbpass = "SERXdJ2w"; 
$dsn = "mysql:host=i4-cs-db1.up.ist.psu.edu;dbname=dmc186";
$pdo = new PDO($dsn,$dbuser, $dbpass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$id = $_GET['id'];

//define
$safe_sql = "SELECT * FROM `eti_user` WHERE id = :id_from_url";

//prepare
$statement = $pdo->prepare($safe_sql);

//bind
$statement->bindValue("id_from_url", $id);

//execute
$statement->execute();
while ($row = $statement->fetch()) {
    echo "{$row['first_name']} {$row['last_name']} <br>";
}
?>