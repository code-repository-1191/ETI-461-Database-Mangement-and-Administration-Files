<?php
error_reporting (E_ALL);
ini_set("display_errors", 1);

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

// this if statement tells you that the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$id = $_POST['id'];
	
	$fname = $_POST['first_name'];
	$lname = $_POST['last_name'];
	$email = $_POST['email'];
	$username = $_POST['username'];
	$password = $_POST['password'];
    // Write an sql statement that will update the first_name, last_name, email, username, and password columns with the 
    // php variables above $fname, $lname, $email, $username, $password (respectively) for the user we are update. 
    // Do NOT forget to include your where clause so that we only update the user with the $id that is present on this page
	// set the sql statement equal to the php variable $sql

    $sql = "UPDATE `eti_user` 
    SET 
    first_name = '{$fname}', 
    last_name = '{$lname}', 
    email = '{$email}', 
    username = '{$username}', 
    password = '{$password}'
    WHERE id = {$id}";
    
    echo "<br>";
	echo "$sql";
	echo "<br>";
	
	$mysqlconn->query($sql);

}
// the form hasn't been submitted so we will get the data for this user from the database.
else {
    $id = $_GET['id'];
    // write a sql statement to select a single row based on the 
    // php variable $id from the eti_user table. This requires
    // using the WHERE clause assign the select statement or you will update all the users in the table
    // to a php variable with the name $sql
    $sql = "SELECT * FROM `eti_user` WHERE id = {$id}";
    $result = $mysqlconn->query($sql);
    $row = $result->fetch_assoc();
    $id = $row['id'];
    $fname = $row['first_name'];
    $lname = $row['last_name'];
    $email = $row['email'];
    $username = $row['username'];
    $password = $row['password'];
}

?>

<html>
<head>
	<title>ETI 461 CRUD</title>
</head>
    <body><h1>ETI 461 - Update User</h1>


    <a href="read-users.php">User Listing</a><br>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div>
	<label for="first_name">First Name</label>
	<input type="text" name="first_name" id="first_name" value="<?php echo $fname; ?>">
</div>

<div>
	<label for="last_name">Last Name</label>
	<input type="text" name="last_name" id="last_name" value="<?php echo $lname; ?>">
</div>

<div>
	<label for="email">Email</label>
	<input type="email" name="email" id="email" value="<?php echo $email; ?>">
</div>

<div>
	<label for="username">Username</label>
	<input type="text" name="username" id="username" value="<?php echo $username; ?>">
</div>

<div>
	<label for="password">Password</label>
	<input type="password" name="password" id="password" value="<?php echo $password; ?>">
</div>

<div>
	<input type="submit" value="Update User">
</div>
</form>

</body>
</html>