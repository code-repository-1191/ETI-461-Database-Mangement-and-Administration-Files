<?php
require "../includes/db_conn.php";

// pretty terrible to delte based on a GET variable
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
	$user_id = $_GET['id'];
	// write a sql statement to delete the user from the database that
	// had the user id passed to this page. Do NOT forget to include your
	// WHERE clause or you will delete all the users from the table.
	// set the value of this sql statement to the php variable $sql
	$sql = "DELETE FROM eti_user WHERE id = $user_id";
	$mysqlconn->query($sql);
    header("location:index.php");
}
?>