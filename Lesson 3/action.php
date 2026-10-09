<!DOCTYPE html>
<?php
$first_name = $_POST('first');
$last_name = $_POST('last');
$email = $_POST('email');
?>

<html>
<head>
    <meta charset="UTF-8" />
    <title>ETI 461 Lesson 3</title>
</head>
<body>
    This is the action page.

    <p>Here are the variable values</p>
<?php 
echo "First name is {$first_name}<br>";
echo "Last name is {$last_name}<br>";
echo "Email is {$email}<br>";
?>
</body>
</html>