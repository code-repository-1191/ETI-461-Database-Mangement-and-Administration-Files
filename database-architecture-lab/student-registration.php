<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
if (!isset($_GET['StudentID'])) {
    header("location:index.php");
    exit();
}
$StudentID = $_GET['StudentID'];

$query = "SELECT s.lastname, r.grade, t.semester, t.year, c.coursename, c.credits, i.lastname as Instructor
FROM `Students` s, `Registrations` r, `Semesters` t, `Courses` c, `Instructors` i, `CourseOfferings` co
WHERE s.StudentID = r.StudentID
AND r.OfferingID = co.OfferingID
AND co.CourseID = c.CourseID
AND co.SemesterID = t.SemesterID
AND co.InstructorID = i.InstructorID
AND s.StudentID = {$StudentID}
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
        <th>Student Last Name</th>
        <th>Semester</th>
        <th>Year</th>
        <th>Course</th>
        <th>Credits</th>
        <th>Instructor</th>
        <th>Grade</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['lastname']}</td>";
      echo "<td>{$row['semester']}</td>";
      echo "<td>{$row['year']}</td>";
      echo "<td>{$row['coursename']}</td>";
      echo "<td>{$row['credits']}</td>";
      echo "<td>{$row['Instructor']}</td>";
      echo "<td>{$row['grade']}</td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>