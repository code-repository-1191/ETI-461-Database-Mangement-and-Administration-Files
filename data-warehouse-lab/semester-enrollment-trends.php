<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
/*
Purpose: Track student enrollment trends over semesters.
Use Case: Helps in forecasting course demand for future semesters.
*/
$query = "SELECT s.Semester, s.Year, COUNT(f.RegistrationID) AS TotalEnrollments
          FROM DimSemesters s
          JOIN FactCourseRegistrations f ON s.SemesterID = f.SemesterID
          GROUP BY s.Semester, s.Year
          ORDER BY s.Year DESC, s.Semester DESC";
$result = $mysqlconn->query($query);

?>
<html>
    <head>
        <title>ETI 461</title>
    </head>
    <body>
        <h1>ETI 461 - Data Warehouse Lab</h1>

<h3>Reports</h3>

<div>
    <a href="index.php">Home</a> | 
    <a href="course-enrollment.php">Course Enrollment</a> | 
    <a href="instructor-workload.php">Instructor Workload</a> |
    <a href="semester-enrollment-trends.php">Semester Enrollment Trends</a> | 
    <a href="student-activity.php">Student Activity</a> | 
    <a href="course-dropout.php">Course Dropout</a> | 
    <a href="department-performance.php">Department Performance</a>
</div>
<br><br>
<table>
    <tr>
        <td>Semester</td>
        <td>Year</td>
        <td>Total Enrollments</td>
    </tr>
<?php

if ($result->num_rows) {
    while ($row = $result->fetch_array()) {
        echo "<tr>";
        echo "<td>{$row['Semester']}</td>";
        echo "<td>{$row['Year']}</td>";
        echo "<td>{$row['TotalEnrollments']}</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>