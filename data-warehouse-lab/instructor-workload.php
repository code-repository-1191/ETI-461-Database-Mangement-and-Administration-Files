<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
/*
Purpose: Identify which instructors are handling the most courses.
Use Case: Helps optimize faculty workload distribution.
*/
$query = "SELECT i.FirstName, i.LastName, COUNT(f.RegistrationID) AS TotalRegistrations
          FROM DimInstructors i
          JOIN FactCourseRegistrations f ON i.InstructorID = f.InstructorID
          GROUP BY i.InstructorID, i.FirstName, i.LastName
          ORDER BY TotalRegistrations DESC";
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
        <td>Name</td>
        <td>Total Registrations</td>
    </tr>
<?php

if ($result->num_rows) {
    while ($row = $result->fetch_array()) {
        echo "<tr>";
        echo "<td>{$row['FirstName']} {$row['LastName']}</td>";
        echo "<td>{$row['TotalRegistrations']}</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>