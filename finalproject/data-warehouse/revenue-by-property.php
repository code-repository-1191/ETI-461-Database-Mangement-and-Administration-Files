<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../db_conn.php";

//  write SQL to select the property name and total amount 
// of money it has made, as the alias "total_revenue".
// 8.5
$sql = "SELECT p.PropertyName as property_name, SUM(r.TotalPrice) as total_revenue FROM Properties p JOIN Reservations r ON p.PropertyID = r.PropertyID GROUP BY p.PropertyID, p.PropertyName ORDER BY total_revenue DESC";

$result = $mysqlconn->query($sql);

require "../header.php";

echo "<div classs=\"container\">";
    echo "<div class=\"row\">";
while ($row = $result->fetch_assoc()) {
    echo "<div class=\"col-md-3\">&nbsp;</div>
        <div class=\"col-md-6\">{$row['property_name']} => {$row['total_revenue']}</div>
        <div class=\"col-md-3\">&nbsp;</div>";
}
echo "</div>
</div>";
require "../header.php";

?>