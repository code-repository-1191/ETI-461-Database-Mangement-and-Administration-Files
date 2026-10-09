<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

// A: Query
$sql = "SELECT InstructorID, CONCAT(FirstName, ' ', LastName) AS Name, Email, Department,
        JSON_EXTRACT(InstructorProfile, '$.website') AS Website,
        JSON_EXTRACT(InstructorProfile, '$.office_hours') AS OfficeHours,
        JSON_EXTRACT(InstructorProfile, '$.research_interests') AS ResearchInterests
        FROM Instructors 
        ORDER BY LastName";

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
        <th>Department</th>
        <th>Office Hours</th>
        <th>Website</th>
        <th>Research Interests</th>
    </tr>

<?php
if ($result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $oh = json_decode($row['OfficeHours'], true);
        $research = implode(", ", json_decode($row['ResearchInterests']));
        // B: OUTPUT query results
        echo "<tr>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['Department'] . "</td>";
            echo "<td>";
            foreach ($oh as $k => $v) {
                echo "$k: $v<br>";
            }
            echo "</td>";
            echo "<td>" . $row['Website'] . "</td>";
            echo "<td>{$research}</td>";
        echo "</tr>";
    }
}
?>

</body>
</html>