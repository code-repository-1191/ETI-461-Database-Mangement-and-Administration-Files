<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

if (!isset($_GET['CourseID'])) {
    header("location:students.php");
    exit();
}
$courseID = (int)$_GET['CourseID'];

// A: Query
$sql = "SELECT i.LastName AS Instructor, co.Schedule, co.RoomNumber AS Room,
        JSON_EXTRACT(co.OfferingDetails, '$.class_format') AS ClassFormat,
        CONCAT(s.Semester, ' ', s.Year) AS Semester,
        JSON_EXTRACT(co.OfferingDetails, '$.final_exam.date') AS FinalDate,
        JSON_EXTRACT(co.OfferingDetails, '$.final_exam.location') AS FinalLocation,
        JSON_EXTRACT(co.OfferingDetails, '$.guest_lecturers') AS GuestLecturers
        FROM CourseOfferings co
        JOIN Instructors i ON co.InstructorID = i.InstructorID
        JOIN Semesters s ON co.SemesterID = s.SemesterID";

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

<h3>Course Details for: <?php echo $courseID; ?></h3>
<table>
    <tr>
        <th>Instructor</th>
        <th>Schedule</th>
        <th>Room</th>
        <th>Format</th>
        <th>Semester</th>
        <th>Final Details</th>
        <th>Other Speakers</th>
    </tr>
<?php
if ($result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $guests = implode(", ", json_decode($row['GuestLecturers']));
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['Instructor'] . "</td>";
            echo "<td>" . $row['Schedule'] . "</td>";
            echo "<td>" . $row['Room'] . "</td>";
            echo "<td>" . $row['ClassFormat'] . "</td>";
            echo "<td>" . $row['Semester'] . "</td>";
            echo "<td>" . $row['FinalDate'] . " at " . $row['FinalLocation'] . "</td>";
            echo "<td>" . $guests . "</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>