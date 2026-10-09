<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
// A: Query
$sql = "SELECT CourseID, CourseName, Credits, Department,
        JSON_EXTRACT(CourseMetadata, '$.prerequisites') AS Prerequisites,
        JSON_EXTRACT(CourseMetadata, '$.syllabus_link') AS Syllabus
        FROM Courses 
        ORDER BY CourseName";

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
        <th>Prereqs</th>
        <th>Credits</th>
        <th>Department</th>
        <th>Syllabus</th>
        <th>View</th>
    </tr>
<?php
if ($result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $prereqs = json_decode($row['Prerequisites'], true);
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['CourseName'] . "</td>";
            echo "<td>" . implode(", ", $prereqs) . "</td>";
            echo "<td>" . $row['Credits'] . "</td>";
            echo "<td>" . $row['Department'] . "</td>";
            echo "<td>" . $row['Syllabus'] . "</td>";
            echo "<td><a href=\"course-detail.php?CourseID=" . $row['CourseID'] . "\">Course Details</a></td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>