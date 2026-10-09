<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
$query = "SELECT c.CourseID, c.CourseName, c.Credits, c.Department
        FROM `Courses` c
        ORDER BY CourseName";
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
        <th>CourseID</th>
        <th>Name</th>
        <th>Credits</th>
        <th>Department</th>
        <th>View Offering</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['CourseID']}</td>";
      echo "<td>{$row['CourseName']}</td>";
      echo "<td>{$row['Credits']}</td>";
      echo "<td>{$row['Department']}</td>";
      echo "<td><a href=\"course-offering.php?CourseID={$row['CourseID']}\">View Offerings</a></td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>