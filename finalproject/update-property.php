<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "db_conn.php";
$debug = FALSE;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'delete') {
        try{
            // begin the transaction part 5.2.1
            mysqli_begin_transaction($mysqlconn);

            $property_id = $_POST['PropertyID'];
            // delete the image references in the database
            $clear_imgs = "DELETE FROM `Property_Images` WHERE `PropertyID` = {$property_id}";
            $mysqlconn->query($clear_imgs);
            if ($debug) {
                echo "{$clear_imgs}<br>";
            }

            if (mysqli_affected_rows($mysqlconn) == -1) {
                throw new Exception("Could not clear the images prior to delete.");
            }

            // delete the amenities
            $clear_amenities = "DELETE FROM `Property_Amenities` WHERE `PropertyID` = {$property_id}";
            $mysqlconn->query($clear_amenities);
            if ($debug) {
                echo "{$clear_amenities}<br>";
            }
        
            if (mysqli_affected_rows($mysqlconn) == -1) {
                throw new Exception("Could not clear the amenities prior to delete.");
            }

            // delete the property
            $clear_property = "DELETE FROM `Properties` WHERE `PropertyID` = {$property_id}";
            $mysqlconn->query($clear_property);
            if ($debug) {
                echo "{$clear_property}<br>";
            }
        
            if (mysqli_affected_rows($mysqlconn) == -1) {
                throw new Exception("Could not clear the property to delete.");
            }
            // commit part 5.2.1
            mysqli_commit($mysqlconn);
          }
          

          catch (Exception $e) {
            // An exception has been thrown
            // Rollback the transaction part 5.2.2
            mysqli_rollback($mysqlconn);

            echo "Transaction failed: " . $e->getMessage();
            exit();
          }
          header("Location:index.php?action=success");
    } // end delete
    
    elseif ($_POST['action'] == 'update') {
        try {
            // Start transaction
            mysqli_begin_transaction($mysqlconn);
            $property_id = $_POST['PropertyID'];
            $hostID      = $_POST['HostID'];
            $pname       = $_POST['PropertyName'];
            $description = $_POST['Description'];
            $street      = $_POST['Address'];
            $city        = $_POST['City'];
            $state       = $_POST['State'];
            $zip         = $_POST['ZipCode'];
            $ppn         = $_POST['PricePerNight'];
            $max         = $_POST['MaxGuests'];
            // Update statement 5.1.1
            $sql_update_properties = "UPDATE Properties SET HostID='$hostID', PropertyName='$pname', Description='$description', Address='$street', City='$city', State='$state', ZipCode='$zip', PricePerNight=$ppn, MaxGuests=$max WHERE PropertyID=$property_id";
             
            if ($debug) {
              echo "{$sql_update_properties} <br>";
            }
        
            $mysqlconn->query($sql_update_properties);
            if (mysqli_affected_rows($mysqlconn) == -1) {
              throw new Exception("Could not add property.");
            }
            
            // currently - not allowing images to be added/updated/deleted
            // 5.1.2
            $clear_amenities = "DELETE FROM Property_Amenities WHERE PropertyID = $property_id";
            $mysqlconn->query($clear_amenities);
            if ($debug) {
                echo "{$clear_amenities}<br>";
            }
        
            if (mysqli_affected_rows($mysqlconn) == -1) {
                throw new Exception("Could not clear the amenities prior to update.");
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
            exit();
          }
          // redirecting back to this page after a successful post, allows user to refresh without
          // being asked if they want the form posted again.
          $redirect_url = htmlspecialchars($_SERVER['PHP_SELF']) . "?property_id={$property_id}";
          header("Location:{$redirect_url}");
    } // end update
  
}

$property_id = $_GET['property_id'];
// 5.3.1
$property_sql = "SELECT * FROM Properties WHERE PropertyID = $property_id";
// 5.3.2
$amenities_sql = "SELECT AmenityName FROM Property_Amenities WHERE PropertyID = $property_id";   
$property_result = $mysqlconn->query($property_sql);
$property_assoc = mysqli_fetch_assoc($property_result);
$host_id = $property_assoc['HostID'] ?? null;

$property_name = $property_assoc['PropertyName'] ?? null;
$description = $property_assoc['Description'] ?? null;
$address = $property_assoc['Address'] ?? null;
$city = $property_assoc['City'] ?? null;
$state = $property_assoc['State'] ?? null;
$zipcode = $property_assoc['ZipCode'] ?? null;
$ppn = $property_assoc['PricePerNight'] ?? null;
$max_guests = $property_assoc['MaxGuests'] ?? null;

$amenity_result = $mysqlconn->query($amenities_sql);
// get the amenities for this property and create a comma delimited string of them
while ($row = $amenity_result->fetch_assoc()) {
    //echo "{$row['AmenityName']}";
    $amenities[] = $row['AmenityName'];
}
$amenities_list = implode(",", $amenities);
    


$get_hosts = "SELECT UserID, Name FROM `Users` WHERE UserType = 'Host'";
$hosts_result = $mysqlconn->query($get_hosts);
require "header.php";
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="PropertyID" value="<?php echo "{$property_id}"; ?>" >
<fieldset>
    <legend class="text-center">Update Property</legend>

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
            $selected = "";
            if ($host_id == $row['UserID']) {
                $selected = "selected";
            }
              echo "<option value=\"{$row['UserID']}\" {$selected}>{$row['Name']}</option>\n";
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
        <input value="<?php echo "{$property_name}"; ?>" type="text" id="PropertyName" name="PropertyName" class="form-control" placeholder="Property Name">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>
    
<!-- Description -->    
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <div class="form-floating">
          <textarea class="form-control" placeholder="Leave a description of the property here." name="Description" id="Description" rows="5" cols="60"><?php echo "{$description}"; ?></textarea>
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
        <input value="<?php echo "{$address}"; ?>" type="text" class="form-control" id="Address" name="Address" placeholder="Street Address">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- City -->    
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="City" class="form-label">City</label>
        <input value="<?php echo "{$city}"; ?>" type="text" class="form-control" id="City" name="City" placeholder="City">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- State -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="State" class="form-label">State</label>
        <input value="<?php echo "{$state}"; ?>" type="text" class="form-control" id="State" name="State" placeholder="State">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Zip Code -->  
    <div class="row">&nbsp;</div>  
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <label for="ZipCode" class="form-label">Zip Code</label>
        <input value = "<?php echo "{$zipcode}"; ?>" type="text" class="form-control" id="ZipCode" name="ZipCode" placeholder="16802">
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
          <input value="<?php echo "{$ppn}"; ?>" type="text" class="form-control" id="PricePerNight" name="PricePerNight" placeholder="$250" aria-label="Amount (to the nearest dollar)">
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
        <input value="<?php echo "{$max_guests}"; ?>" type="text" class="form-control" id="MaxGuests" name="MaxGuests" placeholder="3">
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Currently not allowing updating of Property Images -->
    
<!-- Amenities -->    
  <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4">
        <div class="form-floating">
          <textarea class="form-control" placeholder="List amenities, separated by commas." name="AmenityName" id="AmenityName" rows="5" cols="60"><?php echo "{$amenities_list}"; ?></textarea>
          <label for="floatingTextarea">Property Amenities</label>
        </div>
      </div>
      <div class="col-4">&nbsp;</div>
    </div>

<!-- Submit -->
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-4">&nbsp;</div>
      <div class="col-4"><button type="submit" name="action" value="update" class="btn btn-primary">Update</button></div>
      <div class="col-4"><button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button></div>
    </div>
  </fieldset>
</form>

<?php
require "footer.php";
?>