<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";
$debug = FALSE;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    // Start transaction
    mysqli_begin_transaction($mysqlconn);
    $hostID      = $_POST['HostID'];
    $pname       = $_POST['PropertyName'];
    $description = $_POST['Description'];
    $street      = $_POST['Address'];
    $city        = $_POST['City'];
    $state       = $_POST['State'];
    $zip         = $_POST['ZipCode'];
    $ppn         = $_POST['PricePerNight'];
    $max         = $_POST['MaxGuests'];

    // complete the SQL statement to insert property 
    // 3.1 
    $sql_insert_properties = "INSERT INTO `Properties` 
     (`HostID`, `PropertyName`, `Description`, `Address`, `City`, `State`, `ZipCode`, `PricePerNight`, `MaxGuests`)
     VALUES ($hostID, '$pname', '$description', '$street', '$city', '$state', '$zip', $ppn, $max)";
    if ($debug) {
      echo "{$sql_insert_properties} <br>";
    }
    $mysqlconn->query($sql_insert_properties);
    if (mysqli_affected_rows($mysqlconn) == -1) {
      throw new Exception("Could not add property.");
    }
    
    $property_id = mysqli_insert_id($mysqlconn);

    // loop over the images uploaded for the property and insert them in the 
    // the property_images table, and upload them to the appropriate folder.
    // first image is considered the default image, no real good reason for that
    $total = count($_FILES['propertyImages']['name']);
    for ($i=0; $i < $total; $i++) {
      $tmp = $_FILES['propertyImages']['tmp_name'][$i];
      if ($debug) {
        echo "{$tmp}<br> ";
      }

      if ($tmp) {
        $uploadOk = 1;
        $img_upload_dir = ($i == 0) ? "default" : "other";
        $target_file = "./unsplash_images/{$img_upload_dir}/{$_FILES['propertyImages']['name'][$i]}";
        $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"  ) {
          echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
          $uploadOk = 0;
        }
        if ($uploadOk) {
          
          $sql_insert_images = "INSERT INTO `Property_Images` (`PropertyID`, `ImageURL`) VALUES({$property_id}, '{$target_file}')";
          if ($debug) {
            echo "{$target_file}<br>";
            echo "{$sql_insert_images}<br>";
          }
          
          
          $mysqlconn->query($sql_insert_images);
          if (mysqli_affected_rows($mysqlconn) == -1) {
            throw new Exception("Could not add Property Image.");
          }

          if (! move_uploaded_file($_FILES['propertyImages']['tmp_name'][$i], $target_file) ) {
            throw new Exception("Could not upload Property Image");
          }
        }
      }
    }
    // loop over the property amenities and insert them in the property_amenities table
    $amenities = explode(",", $_POST['AmenityName']);
    for ($i=0; $i < count($amenities); $i++) {
      $amenity_sql = "INSERT INTO `Property_Amenities` (`PropertyID`, `AmenityName`) VALUES ({$property_id}, '{$amenities[$i]}')";
      if ($debug) {
        echo "{$amenity_sql} <br>";
      }
      $mysqlconn->query($amenity_sql);
      if (mysqli_affected_rows($mysqlconn) == -1) {
        throw new Exception("Could not add amenity.");
      }
    }
    mysqli_commit($mysqlconn);
  }
  catch (Exception $e) {
    // An exception has been thrown
    // Rollback the transaction
    mysqli_rollback($mysqlconn);
    echo "Transaction failed: " . $e->getMessage();
  }
}

$get_hosts = "SELECT UserID, Name FROM `Users` WHERE UserType = 'Host'";
$hosts_result = $mysqlconn->query($get_hosts);
require "header.php";
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<fieldset>
    <legend class="text-center">Create Property</legend>

<!-- Host -->    
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="HostID" class="form-label">Host</label>
        <select id="HostID" name="HostID" class="form-select">
        <option>Select Host</option>
          <?php
          while ( $row = $hosts_result->fetch_assoc() ) {
              echo "<option value=\"{$row['UserID']}\">{$row['Name']}</option>\n";
          }
          ?>
          
        </select>
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Property Name -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="disabledTextInput" class="form-label">Property Name</label>
        <input value="Getaway getaway" type="text" id="PropertyName" name="PropertyName" class="form-control" placeholder="Property Name">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>
    
<!-- Description -->    
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <div class="form-floating">
          <textarea class="form-control" placeholder="Leave a description of the property here." name="Description" id="Description" rows="5" cols="60">This is the greatest place ever! Has all the stuff</textarea>
          <label for="floatingTextarea">Property Description</label>
        </div>
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Street Address -->  
    <div class="row">&nbsp;</div>          
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="Address" class="form-label">Street address</label>
        <input value="11 Canterbury Road" type="text" class="form-control" id="Address" name="Address" placeholder="Street Address">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- City -->    
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="City" class="form-label">City</label>
        <input value="Manalapan" type="text" class="form-control" id="City" name="City" placeholder="City">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- State -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="State" class="form-label">State</label>
        <input value="NJ" type="text" class="form-control" id="State" name="State" placeholder="State">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Zip Code -->  
    <div class="row">&nbsp;</div>  
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="ZipCode" class="form-label">Zip Code</label>
        <input value = "07726" type="text" class="form-control" id="ZipCode" name="ZipCode" placeholder="16802">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Price Per Night -->   
    <div class="row">&nbsp;</div> 
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="PricePerNight" class="form-label">Price Per Night</label>
        <div class="input-group mb-3">
          <span class="input-group-text">$</span>
          <input value="500" type="text" class="form-control" id="PricePerNight" name="PricePerNight" placeholder="$250" aria-label="Amount (to the nearest dollar)">
          <span class="input-group-text">.00</span>
        </div>

      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Max Guests -->
    <div class="row">&nbsp;</div>          
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="MaxGuests" class="form-label">Max Guests</label>
        <input value="5" type="text" class="form-control" id="MaxGuests" name="MaxGuests" placeholder="3">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Property Images -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="propertyImages" class="form-label">Property Images</label>
        <input class="form-control" type="file" name="propertyImages[]" id="propertyImages" multiple>
      </div>
      <div class="col-4">&nbsp;</div> 
    </div>

<!-- Amenities -->    
  <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <div class="form-floating">
          <textarea class="form-control" placeholder="List amenities, separated by commas." name="AmenityName" id="AmenityName" rows="5" cols="60">Wi-Fi,Pool,Free Parking,Fenced in Yard</textarea>
          <label for="floatingTextarea">Property Amenities</label>
        </div>
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Submit -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4"><button type="submit" class="btn btn-primary">Create</button></div>
      <div class="col-4">&nbsp;</div>
    </div>
  </fieldset>
</form>

<?php
require "footer.php";
?>