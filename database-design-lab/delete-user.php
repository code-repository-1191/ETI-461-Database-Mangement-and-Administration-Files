<?php
require "../includes/db_conn.php";

// pretty terrible to delte based on a GET variable
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
	$id = $_GET['id'];
	// write a sql statement to delete the user from the database that
	// had the user id passed to this page. Do NOT forget to include your
	// WHERE clause or you will delete all the users from the table.
	// set the value of this sql statement to the php variable $sql
	$sql = "DELETE FROM eti_user WHERE id = $id";
	$mysqlconn->query($sql);

}
?>
<html>
<head>
	<title>ETI 461 Delete User</title>
</head>
    <body><h1>ETI 461</h1>
<a href="index.php">Listing</a>

</body>
</html>