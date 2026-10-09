<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";

/*
 * for the date formatting on check in and check out dates - 
 * It's important to note the different interpretation of - and / in the date. 
 * If you use a - php will determine it to be DD-MM, if you use a / php will 
 * determine it to be MM-DD. This is from a comment on StackOverflow 
 * https://stackoverflow.com/questions/3288257/how-to-convert-mm-dd-yyyy-to-yyyy-mm-dd
 */

$state      = isset($_GET['state']) ? $_GET['state'] : "";
$ppn        = isset($_GET['pricepernight']) ? $_GET['pricepernight'] : "";
$max_guests = isset($_GET['guests']) ? $_GET['guests'] : "";
$check_in   = isset($_GET['check_in']) ? $_GET['check_in'] : "";
$check_out   = isset($_GET['check_out']) ? $_GET['check_out'] : "";
$sql_check_in = date("Y-m-d", strtotime($check_in));
$sql_check_out = date("Y-m-d", strtotime($check_out));

$sql_images = "SELECT  FROM `Property_Images`";

$sql_reservations = "
SELECT PropertyID FROM `Reservations`
WHERE 1=1
AND (   (CheckInDate BETWEEN '{$sql_check_in}' AND '{$sql_check_out}')
    OR (CheckOutDate BETWEEN '{$sql_check_in}' AND '{$sql_check_out}')
    )";

$sql = "SELECT * FROM `Properties` WHERE 1 = 1";
if ( strlen($state) ) {
    // 7.1.a
    $sql .= " AND State = '$state'";
}
if ( strlen($ppn) ) {
    // 7.1.b
    $sql .= " AND PricePerNight <= $ppn";
}
if ( strlen($max_guests) ) {
    // 7.1.c
    $sql .= " AND MaxGuests >= $max_guests";
}
// 7.1.d
if (strlen( trim($check_in)) && strlen(trim($check_out)) ) {
  $sql .= " AND `PropertyID` NOT IN ($sql_reservations) ";
}

//echo "{$sql} <br> ";
$result = $mysqlconn->query($sql);

require "header.php";
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
<div class="container">

<div class="row">Search Results:</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="col-4">&nbsp;</div>
  <div class="col-4">
    <label for="state" class="form-label">Dates</label>
    <input value="<?php echo $check_in; ?>" type="text" id="check_in" name="check_in" class="form-control" placeholder="Check In (mm/dd/yyyy)">
    <input value="<?php echo $check_out; ?>" type="text" id="check_out" name="check_out" class="form-control" placeholder="Check Out (mm/dd/yyyy)">
  </div>
  <div class="col-4">&nbsp;</div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="col-4">&nbsp;</div>
  <div class="col-4">
    <label for="state" class="form-label">State</label>
    <input value="<?php echo $state; ?>" type="text" id="state" name="state" class="form-control" placeholder="Search String">
  </div>
  <div class="col-4">&nbsp;</div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="col-4">&nbsp;</div>
  <div class="col-4">
    <label for="pricepernight" class="form-label">Price Per Night</label>
    <input value="<?php echo $ppn; ?>" type="text" id="pricepernight" name="pricepernight" class="form-control" placeholder="Search String">
  </div>
  <div class="col-4">&nbsp;</div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="col-4">&nbsp;</div>
  <div class="col-4">
    <label for="guests" class="form-label">Guests</label>
    <input value="<?php echo $max_guests; ?>" type="text" id="guests" name="guests" class="form-control" placeholder="Search String">
  </div>
  <div class="col-4">&nbsp;</div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="col-4">&nbsp;</div>
  <div class="col-4">
    <button class="btn btn-primary" type="submit">Search</button>
  </div>
  <div class="col-4">&nbsp;</div>
</div>



</div>
</form>

<?php
if ($result->num_rows > 0) {
    $index = 0;
    echo "<div classs=\"container\">";
    while ($row = $result->fetch_assoc()) {
        $default_img = "SELECT * FROM `Property_Images` WHERE PropertyID = {$row['PropertyID']} AND ImageURL LIKE './unsplash_images/default/%'"; 
        $res = $mysqlconn->query($default_img);
        $img = mysqli_fetch_assoc($res)['ImageURL'] ?? null;
        if ($index % 4 == 0) {
            echo "<div class=\"row text-center\">";
        } 
        //echo "<br>";
        echo "
            <div class=\"col\">
                {$row['PropertyName']} <br>
                <a href=\"property.php?pid={$row['PropertyID']}\"><img src=\"{$img}\" alt=\"\" height=\"200\" width=\"150\"></a>
            </div>";
        if (($index + 1) % 4 == 0) {
            echo '</div>';
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