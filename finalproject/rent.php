<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";
$debug = FALSE;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $property_id = $_POST['property_id'];
        $guest_id = $_POST['guest_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $sql_check_in = date("Y-m-d", strtotime($check_in));
        $sql_check_out = date("Y-m-d", strtotime($check_out));
        // calculation to get total cost
        $datetime1 = new DateTime($sql_check_in);
        $datetime2 = new DateTime($sql_check_out);
        $difference = $datetime1->diff($datetime2);
        $total = $difference->d * $_POST['pricepernight'];
        // insert statement 6.1
        $insert_reservation = "INSERT INTO `Reservations` (PropertyID, GuestID, CheckInDate, CheckOutDate, TotalPrice) VALUES ($property_id, $guest_id, '$sql_check_in', '$sql_check_out', $total)";
        //echo "{$insert_reservation}<br>";

        $mysqlconn->query($insert_reservation);
        if (mysqli_affected_rows($mysqlconn) == -1) {
            throw new Exception("Could not add property.");
        }
        
        $reservation_id = mysqli_insert_id($mysqlconn);

    }
    catch (Exception $e) {        
        echo "Transaction failed: " . $e->getMessage();
        exit();
    }  

    require "header.php";
    
    // get reservation information from inserted reservation
    $reservation_sql = "SELECT * FROM `Reservations` WHERE ReservationID = {$reservation_id}";
    $reservation_result = $mysqlconn->query($reservation_sql);
    $reservation_arr = mysqli_fetch_assoc($reservation_result);
    $total_price = $reservation_arr['TotalPrice'] ?? null;
    $check_in_date = $reservation_arr['CheckInDate'] ?? null;
    $check_out_date = $reservation_arr['CheckOutDate'] ?? null;
    // get default image
    $default_img = "SELECT * FROM `Property_Images` WHERE PropertyID = {$property_id} AND ImageURL LIKE './unsplash_images/default/%'"; 
    $res = $mysqlconn->query($default_img);
    $img = mysqli_fetch_assoc($res)['ImageURL'] ?? null;
    
    // get property information 
    $sql = "SELECT * FROM `Properties` WHERE PropertyID = {$property_id}";
    $result = $mysqlconn->query($sql);
    $result_arr = mysqli_fetch_assoc($result);
    $property_name = $result_arr['PropertyName'] ?? null;
    $description = $result_arr['Description'] ?? null;
    $address = $result_arr['Address'] ?? null;
    $city = $result_arr['City'] ?? null;
    $state = $result_arr['State'] ?? null;
    $zipcode = $result_arr['ZipCode'] ?? null;
    $max_guests = $result_arr['MaxGuests'] ?? null;
}
?>
<div class="alert alert-success" role="alert">
  Your reservation was successful. Details below:
</div>
<div class="container">
<div class="float-end"><img class="img-thumbnail float-end" src="<?php echo $img; ?>" alt="<?php echo $description; ?>" height="200" width="150"></div>
    <div class="row"><?php echo $property_name; ?></div>
    <div class="row"><?php echo $description; ?></div>
    <div class="row"><?php echo $address; ?></div>
    <div class="row"><?php echo "{$city}, {$state}"; ?></div>
    <div class="row"><?php echo $zipcode; ?></div>
    <div class="row">Check In: <?php echo $check_in_date; ?></div>
    <div class="row">Check Out: <?php echo $check_out_date; ?></div>
    <div class="row">Total: $<?php echo $total_price; ?></div>
</div>
<?php

 

require "footer.php"

?>