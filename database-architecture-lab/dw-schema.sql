-- Data Warehouse Schema for Course Registration Star Schema

-- Dimension Tables

CREATE TABLE DimStudents (
    StudentID INT PRIMARY KEY,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Email VARCHAR(100),
    EnrollmentDate DATE
);

CREATE TABLE DimCourses (
    CourseID INT PRIMARY KEY,
    CourseName VARCHAR(100),
    Credits INT,
    Department VARCHAR(50)
);

CREATE TABLE DimInstructors (
    InstructorID INT PRIMARY KEY,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Email VARCHAR(100),
    Department VARCHAR(50)
);

CREATE TABLE DimSemesters (
    SemesterID INT PRIMARY KEY,
    Semester VARCHAR(20),
    Year INT
);

-- Fact Table

CREATE TABLE FactCourseRegistrations (
    RegistrationID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID INT,
    CourseID INT,
    InstructorID INT,
    SemesterID INT,
    RegistrationDate DATE,
    Grade VARCHAR(5),
    FOREIGN KEY (StudentID) REFERENCES DimStudents(StudentID),
    FOREIGN KEY (CourseID) REFERENCES DimCourses(CourseID),
    FOREIGN KEY (InstructorID) REFERENCES DimInstructors(InstructorID),
    FOREIGN KEY (SemesterID) REFERENCES DimSemesters(SemesterID)
);