<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";

// 2.1
$sql_images = "SELECT * FROM `Property_Images`";
// 2.2
$sql_amenities = "SELECT DISTINCT(AmenityName) FROM `Property_Amenities`";
$amenities_result = $mysqlconn->query($sql_amenities);
if ( isset($_GET['Amenity']) ) {
    // feels like a sloppy way to add '' and , around each amenity
    // to sanitize it for sql query, but works
    $filter_amenity = "'" . implode("', '", $_GET['Amenity']) . "'";
    
    //echo "{$filter_amenity}<br>";

    $property_sql = "SELECT * 
        FROM `Properties` 
        WHERE PropertyID IN (
            SELECT Distinct(PropertyID) FROM `Property_Amenities` WHERE AmenityName IN ({$filter_amenity})
        )";
}
else {
    // 2.3
    $property_sql = "SELECT * FROM `Properties`";
}
//echo "{$property_sql}<br>";
$result = $mysqlconn->query($property_sql);

require "header.php";

if ($result->num_rows > 0) {
    $index = 0;
    echo "
    <div classs=\"container\">
        <div class=\"row text-center\">
            <div class=\"col-md-3 themed-grid-col\">&nbsp;</div>
            <div class=\"col-md-6 themed-grid-col\"><h3>Showing {$result->num_rows} results <span class=\"text-body-secondary\">(Filter by Amenities) </h3></span></div>
            <div class=\"col-md-3 themed-grid-col\">&nbsp;</div>
        </div>
        <div class=\"row text-center\">
            <div class=\"col-md-2 themed-grid-col\">&nbsp;</div>
            <div class=\"col-md-10 themed-grid-col\">
            <form action=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "\" method=\"get\" >
        ";
        // loop over the Amenities and if the one listed is one selected, mark it
        while ($row = $amenities_result->fetch_assoc()) {
            // 2.4 
            $amenity_count_sql = "SELECT COUNT(*) as rental_count FROM `Property_Amenities` WHERE AmenityName = '{$row['AmenityName']}'";
            $r = $mysqlconn->query($amenity_count_sql);
            $data = $r->fetch_assoc();
            // test to see if Amenity has been submitted (if not just set to empty array)
            // if the current amenity is in the list of Amenities selected then mark the checkbox checked
            $a = ( isset($_GET['Amenity']) ? $_GET['Amenity'] : array() ); 
            $checked = in_array($row['AmenityName'], $a) ? " checked" : "";
            echo "<div class=\"form-check form-check-inline\">
                <input class=\"form-check-input\" {$checked} name=\"Amenity[]\" type=\"checkbox\" value=\"{$row['AmenityName']}\" id=\"{$row['AmenityName']}\">
                <label class=\"form-check-label\" for=\"{$row['AmenityName']}\">{$row['AmenityName']} ({$data['rental_count']})</label>
            </div>";
        }
    echo "
        
            <div class=\"row\">&nbsp;</div>
            <button class=\"btn btn-primary\" type=\"submit\">Filter</button>
            </form>
        </div>";
    while ($row = $result->fetch_assoc()) {
        $default_img = "SELECT * FROM `Property_Images` WHERE PropertyID = {$row['PropertyID']} AND ImageURL LIKE './unsplash_images/default/%'"; 
        $res = $mysqlconn->query($default_img);
        if ($res->num_rows > 0) {
            $single_row = mysqli_fetch_assoc($res);
            $img = $single_row['ImageURL'];
        }
        else {
            $img = "./unsplash_images/placeholder.png";
        }
        
        if ($index % 4 == 0) {
            echo "<div class=\"row text-center\">";
        } 
        //echo "<br>";
        echo "
            <div class=\"col\">
                {$row['PropertyName']} <br>
                {$row['City']}, {$row['State']} <br>
                <a href=\"property.php?pid={$row['PropertyID']}\"><img src=\"{$img}\" alt=\"\" height=\"200\" width=\"150\"></a>
            </div>";
        if (($index + 1) % 4 == 0) {
            echo '</div><div class="row">&nbsp;</div>';
        }  
        $index++;
    }
    // Close the last row if it's not closed
    if ($result->num_rows % 4 != 0) {
        echo '</div>';
    }
    echo "</div>";
}

require "footer.php";
?>