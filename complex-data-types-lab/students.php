<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
// A: Query
$sql = "SELECT StudentID, CONCAT(FirstName, ' ', LastName) AS Name, Email, EnrollmentDate,
        JSON_EXTRACT(Information, '$.campus') AS Campus 
        FROM Students ORDER BY LastName";

$result = $mysqlconn->query($sql);
?>
<html>
    <head>
        <title>ETI 461</title>
        <style>table, th, td { border: 1px solid black; border-collapse: collapse; }</style>
    </head>
    <body>
        <h1>ETI 461 - Complex Data Types Lab</h1>

<h3>Reports</h3>

<div>
    <a href="index.php">Home</a> | 
    <a href="students.php">Students</a> | 
    <a href="instructors.php">Instructors</a> | 
    <a href="courses.php">Courses</a> | 
    <a href="semesters.php">Semesters</a>
</div>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Enrollment Date</th>
        <th>Campus</th>
        <th>Schedule</th>
    </tr>
<?php
if ($result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['EnrollmentDate'] . "</td>";
            echo "<td>" . $row['Campus'] . "</td>";
            echo "<td><a href=\"student-schedule.php?StudentID=" . $row['StudentID'] . "\">View Schedule</a></td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>