<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
/*
Purpose: Identify the most active students based on course enrollments.
Use Case: Helps identify high-performing students and potential candidates for academic programs.
*/
$query = "SELECT st.FirstName, st.LastName, COUNT(f.RegistrationID) AS TotalCoursesEnrolled
          FROM DimStudents st
          JOIN FactCourseRegistrations f ON st.StudentID = f.StudentID
          GROUP BY st.StudentID, st.FirstName, st.LastName
          ORDER BY TotalCoursesEnrolled DESC";
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
        <td>Total Courses Enrolled</td>
    </tr>
<?php

if ($result->num_rows) {
    while ($row = $result->fetch_array()) {
        echo "<tr>";
        echo "<td>{$row['FirstName']} {$row['LastName']}</td>";
        echo "<td>{$row['TotalCoursesEnrolled']}</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>