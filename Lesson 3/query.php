<!DOCTYPE html>
<?php
$dbserver = "mysql.up.ist.psu.edu";
$dbuser = "ajd7226";
$dbpass = "3pPngvMK";
$dbname = "ajd7226";

$mysqlconn = new mysqli($dbserver,$dbuser, $dbpass, $dbname);

$query = "SELECT * FROM `eti_user_post`";

$result = $mysqlconn->query($query);
?>

<html>
<head>
    <meta charset="UTF-8" />
    <title>ETI 461 Query Example</title>
</head>
<body>
<?php

while ($row = $result->fetch_array()) {
    echo "{$row[0]} | ";
    echo "{$row[1]} | ";
    echo "{$row[2]} | ";
    echo "<br>";
}

?>

</body>
</html>