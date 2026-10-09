<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 5.1 connect to database 
require "../includes/db_conn.php";
// set autocommit to false
mysqli_autocommit($mysqlconn, FALSE);
// 5.2 START TRANSACTION
 mysqli_begin_transaction($mysqlconn);

try {
    // 5.3
    // Load Students - query to get the students
    $students = $mysqlconn->query("SELECT StudentID, FirstName, LastName, Email, EnrollmentDate FROM Students");
    while ($row = $students->fetch_assoc()) {
        // insert into DimStudents statement
        $sql = "INSERT INTO DimStudents (StudentID, FirstName, LastName, Email, EnrollmentDate) 
        VALUES ('{$row['StudentID']}', '{$row['FirstName']}', '{$row['LastName']}', '{$row['Email']}', '{$row['EnrollmentDate']}')";
        if (!$mysqlconn->query($sql)) {
            throw new Exception("Failed to insert student data: <br>{$sql}<br>" . mysqli_error($mysqlconn));
        }        
    }

    // Load Courses - query to get the courses
    $courses = $mysqlconn->query("SELECT CourseID, CourseName, Credits, Department FROM Courses");
    while ($row = $courses->fetch_assoc()) {
        // insert into DimCourses
        $sql = "INSERT INTO DimCourses (CourseID, CourseName, Credits, Department) 
        VALUES ('{$row['CourseID']}', '{$row['CourseName']}', '{$row['Credits']}', '{$row['Department']}')";
        if (!$mysqlconn->query($sql)) {
            throw new Exception("Failed to insert dimcourses data: <br>{$sql}<br>" . mysqli_error($mysqlconn));
        }
    }

    // Load Instructors - query to get Instructores
    $instructors = $mysqlconn->query("SELECT InstructorID, FirstName, LastName, Email, Department FROM Instructors");
    while ($row = $instructors->fetch_assoc()) {
        // insert into diminstructors
        $sql = "INSERT INTO DimInstructors (InstructorID, FirstName, LastName, Email, Department) 
        VALUES ('{$row['InstructorID']}', '{$row['FirstName']}', '{$row['LastName']}', '{$row['Email']}', '{$row['Department']}')";
        if (!$mysqlconn->query($sql)) {
            throw new Exception("Failed to insert diminstructor data: <br>{$sql}<br>" . mysqli_error($mysqlconn));
        }
    }

    // Load Semesters - query to get Semesters
    $semesters = $mysqlconn->query("SELECT SemesterID, Semester, Year FROM Semesters");
    while ($row = $semesters->fetch_assoc()) {
        // insert into DimSemesters
        $sql = "INSERT INTO DimSemesters (SemesterID, Semester, Year) 
        VALUES ('{$row['SemesterID']}', '{$row['Semester']}', '{$row['Year']}')";
        if (!$mysqlconn->query($sql)) {
            throw new Exception("Failed to insert dimsemester data: <br>{$sql}<br>" . mysqli_error($mysqlconn));
        }
    }

    // Load Fact Table (Course Registrations)
    $registrations = $mysqlconn->query("SELECT r.StudentID, co.CourseID, co.InstructorID, co.SemesterID, r.RegistrationDate, r.Grade 
    FROM Registrations r 
    JOIN CourseOfferings co 
    ON r.OfferingID = co.OfferingID");
    while ($row = $registrations->fetch_assoc()) {
        // sql to insert into FactCourseRegistrations table
        $sql = "INSERT INTO FactCourseRegistrations (StudentID, CourseID, InstructorID, SemesterID, RegistrationDate, Grade) 
        VALUES ('{$row['StudentID']}', '{$row['CourseID']}', '{$row['InstructorID']}', '{$row['SemesterID']}', '{$row['RegistrationDate']}', '{$row['Grade']}')";
        if (!$mysqlconn->query($sql)) {
            throw new Exception("Failed to insert fact registration data: <br>{$sql}<br>" . mysqli_error($mysqlconn));
        }
    }

    // 5.4 COMMIT TRANSACTION
    mysqli_commit($mysqlconn);
    
    echo "ETL Process completed successfully!";

} catch (Exception $e) {
    // 5.5 ROLLBACK
    mysqli_rollback($mysqlconn);
    
    echo "Transaction failed: " . $e->getMessage();
}

?>
}


?>
