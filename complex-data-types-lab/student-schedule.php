<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

if (!isset($_GET['StudentID']) || !is_numeric($_GET['StudentID'])) {
    header("location:students.php");
    exit();
}
$studentID = (int)$_GET['StudentID'];

// A: Query
$sql = "SELECT s.Year, s.Semester, co.RoomNumber, i.LastName, c.CourseName, co.Schedule, c.Credits, 
        JSON_EXTRACT(r.RegistrationDetails, '$.enrollment_status') AS EnrollmentStatus, r.Grade,
        JSON_EXTRACT(r.RegistrationDetails, '$.special_accommodations') AS Accommodations
        FROM Registrations r
        JOIN CourseOfferings co ON r.OfferingID = co.OfferingID
        JOIN Instructors i ON co.InstructorID = i.InstructorID
        JOIN Courses c ON co.CourseID = c.CourseID
        JOIN Semesters s ON co.SemesterID = s.SemesterID
        ORDER BY s.Year, s.Semester";

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

<h3>Schedule for Student: <?php echo $studentID; ?></h3>
<table>
    <tr>
        <th>Year</th>
        <th>Semester</th>
        <th>Room</th>
        <th>Instructor</th>
        <th>Course</th>
        <th>Schedule</th>
        <th>Credits</th>
        <th>Status</th>
        <th>Grade</th>
    </tr>
<?php
if ($result->num_rows) {

    while ($row = $result->fetch_assoc()) {
        $sa = json_decode($row['Accommodations'], true);
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['Year'] . "</td>";
            echo "<td>" . $row['Semester'] . "</td>";
            echo "<td>" . $row['RoomNumber'] . "</td>";
            echo "<td>" . $row['LastName'] . "</td>";
            echo "<td>" . $row['CourseName'] . "</td>";
            echo "<td>" . $row['Schedule'] . "</td>";
            echo "<td>" . $row['Credits'] . "</td>";
            echo "<td>" . $row['EnrollmentStatus'] . "</td>";
            echo "<td>" . $row['Grade'] . "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td colspan=9>Special Accommodations:";

            foreach ($sa as $a) {
                echo "$a <br>";
            }
            echo "</td>";
        echo "</tr>";
        echo "<tr><td colspan=9>&nbsp;</td></tr>";
    }
}
?>
</table>
</body>
</html>