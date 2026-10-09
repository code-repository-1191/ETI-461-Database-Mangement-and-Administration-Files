<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

// A: Query
$sql = "SELECT Semester, Year,
        JSON_EXTRACT(SemesterInfo, '$.registration_deadline') AS RegistrationDeadline,
        JSON_EXTRACT(SemesterInfo, '$.weather') AS Weather,
        JSON_EXTRACT(SemesterInfo, '$.holidays') AS Holidays
        FROM Semesters 
        ORDER BY Year, Semester";

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
        <th>Semester</th>
        <th>Year</th>
        <th>Registration Deadline</th>
        <th>Holidays</th>
        <th>Weather</th>
    </tr>
<?php
if ($result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $holidays = implode(", ", json_decode($row['Holidays']));
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['Semester'] . "</td>";
            echo "<td>" . $row['Year'] . "</td>";
            echo "<td>" . $row['RegistrationDeadline'] . "</td>";
            echo "<td>" . $holidays . "</td>";
            echo "<td>" . $row['Weather'] . "</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>