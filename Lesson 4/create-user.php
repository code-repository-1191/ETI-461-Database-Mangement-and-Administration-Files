<?php
error_reporting (E_ALL);
ini_set("display_errors", 1);

// this if statement tells you that the form has been submitted
$dbserver = "mysql.up.ist.psu.edu";

// change USERNAME to be whatever your USERNAME is, should be the same as your database name
$dbuser = "ajd7226";

// change PASSWORD to whatever password IST created for you when you created your MySQL database
$dbpass = "3pPngvMK"; 

// change USERNAME to your USERNAME which is the same as your database name
$dbname = "ajd7226";

$mysqlconn = new mysqli( $dbserver, $dbuser, $dbpass, $dbname);

if ( $mysqlconn->connect_error ) {
  die("Connection failed: " . $mysqlconn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$fname = $_POST['first_name'];
	$lname = $_POST['last_name'];
	$email = $_POST['email'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	// write a sql INSERT statement for the eti_user table that inserts the variable values $fname, $lname, $email, $username, $password
	// into the eti_user table, the first part of the statement has been started
	$sql = "INSERT INTO `eti_user` (`first_name`, `last_name`, `email`, `username`, `password`) 
	VALUES ('{$fname}', '{$lname}', '{$email}', '{$username}', '{$password}')";
	echo "<br>";
	echo "$sql";
	echo "<br>";
	$mysqlconn->query($sql);

}

?>

<html>
<head>
	<title>ETI 461 Create User</title>
</head>
    <body><h1>ETI 461</h1>

    <a href="read-users.php">User Listing</a><br>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

<div>
	<label for="first_name">First Name</label>
	<input type="text" name="first_name" id="first_name" value="">
</div>

<div>
	<label for="last_name">Last Name</label>
	<input type="text" name="last_name" id="last_name" value="">
</div>

<div>
	<label for="email">Email</label>
	<input type="email" name="email" id="email" value="">
</div>

<div>
	<label for="username">Username</label>
	<input type="text" name="username" id="username" value="">
</div>

<div>
	<label for="password">Password</label>
	<input type="password" name="password" id="password" value="">
</div>

<div>
	<input type="submit" value="Create User">
</div>
</form>

</body>
</html>