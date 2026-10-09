<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../db_conn.php";

// write SQL selects the average review rating for each property, 
// you need to include the property_name and use an alias of 'average_rating' 
// when selecting the review rating, sort in descending order of rating.
// 8.4
$sql = "SELECT p.PropertyName as property_name, AVG(r.Rating) as average_rating FROM Properties p JOIN Reviews r ON p.PropertyID = r.PropertyID GROUP BY p.PropertyID, p.PropertyName ORDER BY average_rating DESC";

$result = $mysqlconn->query($sql);


require "../header.php";
echo "<div classs=\"container\">";
    echo "<div class=\"row\">";
        
while ($row = $result->fetch_assoc()) {
    echo "
            <div class=\"col-md-3\">&nbsp;</div>
            <div class=\"col-md-6\">
            {$row['property_name']} => {$row['average_rating']} <br>
            </div>
            <div class=\"col-md-3\">&nbsp;</div>";
}
    echo "</div>
    </div>";
require "../footer.php";
?>