<html>
    <head><title>get images</title>
</head>
<body>
<?php
require "db_conn.php";
exit;
// Directory you want to scan
$directory = './unsplash_images/other';

// Ensure the directory exists
if (is_dir($directory)) {
    // Open the directory
    if ($handle = opendir($directory)) {
        echo "Files in the directory '$directory':<br>";

        // Loop through the directory entries
        $img_array = array();
        while (false !== ($file = readdir($handle))) {
            // Skip the current (.) and parent (..) directories
            if ($file != '.' && $file != '..') {
                echo $file . "<br>";  // Print the file name
                $img_array[] = $file;
            }
        }
        for ($i=0; $i < count($img_array); $i++) {
            // Generate a random number between 1 and 40
            $randomPropertyId = rand(1, 40);
            $sql = "INSERT INTO `Property_Images` (PropertyID, ImageURL) VALUES({$randomPropertyId}, '{$directory}/{$img_array[$i]}')";
            $mysqlconn->query($sql);
            echo "{$sql} <br>";

        }

        // Close the directory handle
        closedir($handle);
    } else {
        echo "Failed to open the directory.";
    }
} else {
    echo "The specified directory does not exist.";
}
?>
</body>
</html>