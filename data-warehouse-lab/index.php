<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

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


</body>
</html>