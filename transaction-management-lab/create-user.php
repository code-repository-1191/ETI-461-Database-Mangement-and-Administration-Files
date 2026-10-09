<?php
require "../includes/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$fname = $_POST['first_name'];
	$lname = $_POST['last_name'];
	$email = $_POST['email'];
	$username = $_POST['username'];
	$password = $_POST['pword'];
	// write a sql INSERT statement for the eti_user table that inserts the variable values $fname, $lname, $email, $username, $password
	// into the eti_user table, the first part of the statement has been started
	$sql = "INSERT INTO `eti_user` (first_name, last_name, email, username, password) VALUES ('{$fname}', '{$lname}', '{$email}', '{$username}', 
	'{$password}')";
	$mysqlconn->query($sql);
	header("location:index.php");
}

?>
 
<html>
<head>
	<title>ETI 461 Create User</title>
</head>

<body>
	<h1>ETI 461: Create User</h1>
<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
</div>
<br>

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
	<label for="pword">Password</label>
	<input type="password" name="pword" id="pword" value="">
</div>

<div>
	<input type="submit" value="Create User">
</div>
</form>

</body>
</html>