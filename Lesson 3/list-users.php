<!DOCTYPE html>
<?php
$dbserver = "mysql.up.ist.psu.edu";
$dbuser = "ajd7226";
$dbpass = "3pPngvMK";
$dbname = "ajd7226";

$mysqlconn = new mysqli($dbserver,$dbuser, $dbpass, $dbname);

$query = "SELECT first_name, last_name, registration_date, username, password, id FROM `eti_user`";

$result = $mysqlconn->query($query);
?>

<html>
<head>
    <meta charset="UTF-8" />
    <title>List Users</title>
</head>
<body>

<?php
while ($row = $result->fetch_assoc()) {
    echo "{$row['id']} | ";
    echo "{$row['first_name']} | ";
    echo "{$row['last_name']} | ";
    echo "{$row['username']} | ";
    echo "{$row['registration_date']} | ";
    echo "<br>";
}
?>

</body>
</html>