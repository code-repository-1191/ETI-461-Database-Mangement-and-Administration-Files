<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
if (!isset($_GET['InstructorID'])) {
    header("location:index.php");
    exit();
}
$InstructorID = $_GET['InstructorID'];

$query = "SELECT t.semester, t.year, c.coursename, c.credits, i.lastname as Instructor
FROM  `Semesters` t, `Courses` c, `Instructors` i, `CourseOfferings` co
WHERE co.CourseID = c.CourseID
AND co.SemesterID = t.SemesterID
AND co.InstructorID = i.InstructorID
AND i.InstructorID = {$InstructorID}
ORDER BY t.year, t.Semester";
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
        <th>Semester</th>
        <th>Year</th>
        <th>Course</th>
        <th>Credits</th>
        <th>Instructor</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
      echo "<td>{$row['semester']}</td>";
      echo "<td>{$row['year']}</td>";
      echo "<td>{$row['coursename']}</td>";
      echo "<td>{$row['credits']}</td>";
      echo "<td>{$row['Instructor']}</td>";    
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>