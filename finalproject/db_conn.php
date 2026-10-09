<?php

// 1.1
$dbserver = "mysql.up.ist.psu.edu";

// 1.2 change USERNAME to be whatever your USERNAME is, should be the same as your database name
$dbuser = "ajd7226";

// 1.3 change PASSWORD to whatever password IST created for you when you created your MySQL database
$dbpass = "3pPngvMK";

// 1.4 change USERNAME to your USERNAME which is the same as your database name
$dbname = "ajd7226";

$mysqlconn = new mysqli( $dbserver, $dbuser, $dbpass, $dbname);

if ( $mysqlconn->connect_error ) {
  die("Connection failed: " . $mysqlconn->connect_error);
}


?>