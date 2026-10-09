<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";
$pid = $_GET['pid'];

// using prepared statements select the property and property images there are 4 
// parts to the prepared statement for each query.
// 4.1 
// 1- define
$property = "SELECT * FROM `Properties` WHERE PropertyID = ?";
// 2- prepare
$prop_stmt = $mysqlconn->prepare($property);
// 3- bind
$prop_stmt->bind_param("i", $pid);
// 4- execute
$prop_stmt->execute();

$prop_result = $prop_stmt->get_result();

// using prepared statements select the property images
// 1- define
$images = "SELECT * FROM `Property_Images` WHERE PropertyID = ?"; 
// 2- prepare
$statement = $mysqlconn->prepare($images);
// 3- bind
$statement->bind_param("i", $pid);
// 4- execute
$statement->execute();
$prop_image_result = $statement->get_result();

?>

<?php
require "header.php";

$get_guests = "SELECT UserID, Name FROM `Users` WHERE UserType = 'Guest'";
$guest_result = $mysqlconn->query($get_guests);

$made_up_img_labels = array ("Words on Image", "More Text", "My other label is a laptop sticker");
$made_up_img_descriptions = array("Beautiful view", "Pond or a pool, pond would be good for you.", "Multiple options");
if ($prop_result->num_rows > 0) {
    $index = 0;
    echo "<div classs=\"container\">";
    echo "<div class=\"row\">";
    //while ($row = $result->fetch_assoc()) {
    while ($row = $prop_result->fetch_assoc()) {    
        echo "
            <div class=\"col-md-3\">&nbsp;</div>
            <div class=\"col-md-6\">
                {$row['PropertyName']} | [ <a href=\"update-property.php?property_id={$row['PropertyID']}\">Edit</a> ]<br>
                {$row['Description']} <br>
                {$row['Address']} <br> 
                {$row['City']}, {$row['State']} {$row['ZipCode']} <br> 
                Sleeps: {$row['MaxGuests']} <br> 
                Cost Per Night: \${$row['PricePerNight']} <br>
<form action=\"rent.php\" method=\"post\" >
    <input type=\"hidden\" name=\"property_id\" value=\"{$row['PropertyID']}\">
    <input type=\"hidden\" name=\"pricepernight\" value=\"{$row['PricePerNight']}\">
    <input type=\"text\" id=\"check_in\" name=\"check_in\" class=\"form-control\" placeholder=\"Check In (mm/dd/yyyy)\">
    <input type=\"text\" id=\"check_out\" name=\"check_out\" class=\"form-control\" placeholder=\"Check Out (mm/dd/yyyy)\">
    <select id=\"guest_id\" name=\"guest_id\" class=\"form-select\">
        <option>Select Guest</option>";
          while ( $row = $guest_result->fetch_assoc() ) {
              echo "<option value=\"{$row['UserID']}\">{$row['Name']}</option>\n";
          }
          
    echo "  
        </select>
    <button class=\"btn btn-primary\" type=\"submit\">Rent</button>     
</form>       
            </div>
            <div class=\"col-md-3\">&nbsp;</div>
            <div class=\"row\">&nbsp;</div>
            ";
    }
    echo "</div>
    <div class=\"row\">";
    echo "
    <div class=\"col-md-3\">&nbsp;</div>
    <div id=\"carouselExample\" class=\"col-md-6 carousel slide\">

        <div class=\"carousel-inner\">";
        $index = 0;
        while ( $row = $prop_image_result->fetch_assoc() ) {
            $rando_index = rand(0,2);
            $a = "";
            if ($index == 0) {
                $a = "active";
            }
            echo "
            <div class=\"carousel-item {$a}\">
            <img src=\"{$row['ImageURL']}\" class=\"d-block w-100\" width=\"400\" height=\"400\" alt=\"...\">
            <div class=\"carousel-caption d-none d-md-block\">
                <h5>{$made_up_img_labels[$rando_index]}</h5>
                <p>{$made_up_img_descriptions[$rando_index]}.</p>
              </div>

            </div>";
            $index++;
        }
        echo "
        </div>
        <button class=\"carousel-control-prev\" type=\"button\" data-bs-target=\"#carouselExample\" data-bs-slide=\"prev\">
            <span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span>
            <span class=\"visually-hidden\">Previous</span>
        </button>
        <button class=\"carousel-control-next\" type=\"button\" data-bs-target=\"#carouselExample\" data-bs-slide=\"next\">
            <span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span>
            <span class=\"visually-hidden\">Next</span>
        </button>
    </div>
    <div class=\"col-md-3\">&nbsp;</div>";
}
require "footer.php";
?>
