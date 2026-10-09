<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
$query = "SELECT s.StudentID, s.FirstName, s.LastName, s.Email, s.EnrollmentDate 
        FROM `Students` s
        ORDER BY LastName";
?>
<html>
    <head>
        <title>ETI 461</title>
    </head>
    <body>
        <h1>ETI 461 - Database Architecture Lab</h1>

<div>
    <a href="index.php">List Students</a> | 
    <a href="courses.php">List Courses</a> |
    <a href="instructors.php">List Instructors</a>
</div>

<table border=1 cellpadding=5 >
    <tr>
        <th>StudentID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Enrollment Date</th>
        <th>View Registration</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['StudentID']}</td>";
      echo "<td>{$row['FirstName']} {$row['LastName']}</td>";
      echo "<td>{$row['Email']}</td>";
      echo "<td>{$row['EnrollmentDate']}</td>";
      echo "<td><a href=\"student-registration.php?StudentID={$row['StudentID']}\">View Registration</a></td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>