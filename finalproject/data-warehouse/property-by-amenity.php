<?php
require "../db_conn.php";

$sql = "SELECT p.property_name, pa.amenity_name
FROM Dim_Properties p
JOIN Dim_Property_Amenities pa ON p.property_id = pa.property_id
WHERE pa.amenity_name = 'Wi-Fi'";

$result = $mysqlconn->query($sql);


require "../header.php";

echo "<div classs=\"container\">";
    echo "<div class=\"row\">";

while ($row = $result->fetch_assoc()) {
    echo "<div class=\"col-md-3\">&nbsp;</div>
            <div class=\"col-md-6\">{$row['property_name']} => {$row['amenity_name']}</div>
            <div class=\"col-md-3\">&nbsp;</div>";
}

echo "</div>
</div>";

require "../footer.php";
?>