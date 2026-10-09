<?php
// update the [penn state access id to you] -- remove the brackets
// example for me
//$source_db = new PDO("mysql:host=i4-cs-db1.up.ist.psu.edu;dbname=dmc186", 'dmc186', '5fhcg$43');
// 8.2
$source_db = new PDO("mysql:host=i4-cs-db1.up.ist.psu.edu;dbname=ajd7226", 'ajd7226', '3pPngvMK');
$target_db = new PDO("mysql:host=i4-cs-db1.up.ist.psu.edu;dbname=ajd7226", 'ajd7226', '3pPngvMK');

// Function to migrate Users
function migrateUsers($source_db, $target_db) {
    $query = "SELECT UserID, Name, Email, PhoneNumber, UserType FROM Users";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Dim_Users table
        $insert_query = "
            INSERT INTO Dim_Users (user_id, name, email, phone_number, user_type, date_joined)
            VALUES (:user_id, :name, :email, :phone_number, :user_type, NOW())
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':user_id' => $row['UserID'],
            ':name' => $row['Name'],
            ':email' => $row['Email'],
            ':phone_number' => $row['PhoneNumber'],
            ':user_type' => $row['UserType']
        ]);
    }
    echo "Users migrated successfully.<br>";
}

// Function to migrate Properties
function migrateProperties($source_db, $target_db) {
    $query = "SELECT PropertyID, HostID, PropertyName, Description, Address, City, State, ZipCode, PricePerNight, MaxGuests FROM Properties";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Dim_Properties table
        $insert_query = "
            INSERT INTO Dim_Properties (property_id, host_id, property_name, description, address, city, state, zip_code, price_per_night, max_guests)
            VALUES (:property_id, :host_id, :property_name, :description, :address, :city, :state, :zip_code, :price_per_night, :max_guests)
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':property_id' => $row['PropertyID'],
            ':host_id' => $row['HostID'],
            ':property_name' => $row['PropertyName'],
            ':description' => $row['Description'],
            ':address' => $row['Address'],
            ':city' => $row['City'],
            ':state' => $row['State'],
            ':zip_code' => $row['ZipCode'],
            ':price_per_night' => $row['PricePerNight'],
            ':max_guests' => $row['MaxGuests']
        ]);
    }
    echo "Properties migrated successfully.<br>";
}

// Function to migrate Reservations
function migrateReservations($source_db, $target_db) {
    $query = "SELECT ReservationID, PropertyID, GuestID, CheckInDate, CheckOutDate, TotalPrice FROM Reservations";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Fact_Reservations table
        $insert_query = "
            INSERT INTO Fact_Reservations (reservation_id, property_id, guest_id, check_in_date, check_out_date, total_price)
            VALUES (:reservation_id, :property_id, :guest_id, :check_in_date, :check_out_date, :total_price)
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':reservation_id' => $row['ReservationID'],
            ':property_id' => $row['PropertyID'],
            ':guest_id' => $row['GuestID'],
            ':check_in_date' => $row['CheckInDate'],
            ':check_out_date' => $row['CheckOutDate'],
            ':total_price' => $row['TotalPrice']
        ]);
    }
    echo "Reservations migrated successfully.<br>";
}

// Function to migrate Reviews
function migrateReviews($source_db, $target_db) {
    $query = "SELECT ReviewID, PropertyID, GuestID, Rating, ReviewText, ReviewDate FROM Reviews";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Dim_Reviews table
        $insert_query = "
            INSERT INTO Dim_Reviews (review_id, property_id, guest_id, rating, review_text, review_date)
            VALUES (:review_id, :property_id, :guest_id, :rating, :review_text, :review_date)
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':review_id' => $row['ReviewID'],
            ':property_id' => $row['PropertyID'],
            ':guest_id' => $row['GuestID'],
            ':rating' => $row['Rating'],
            ':review_text' => $row['ReviewText'],
            ':review_date' => $row['ReviewDate']
        ]);
    }
    echo "Reviews migrated successfully.<br>";
}

// Function to migrate Property_Amenities
function migrateAmenities($source_db, $target_db) {
    $query = "SELECT AmenityID, PropertyID, AmenityName FROM Property_Amenities";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Dim_Property_Amenities table
        $insert_query = "
            INSERT INTO Dim_Property_Amenities (amenity_id, property_id, amenity_name)
            VALUES (:amenity_id, :property_id, :amenity_name)
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':amenity_id' => $row['AmenityID'],
            ':property_id' => $row['PropertyID'],
            ':amenity_name' => $row['AmenityName']
        ]);
    }
    echo "Property amenities migrated successfully.<br>";
}

// Function to migrate Property_Images
function migrateImages($source_db, $target_db) {
    $query = "SELECT ImageID, PropertyID, ImageURL FROM Property_Images";
    $stmt = $source_db->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into target star schema Dim_Property_Images table
        $insert_query = "
            INSERT INTO Dim_Property_Images (image_id, property_id, image_url)
            VALUES (:image_id, :property_id, :image_url)
        ";
        $insert_stmt = $target_db->prepare($insert_query);
        $insert_stmt->execute([
            ':image_id' => $row['ImageID'],
            ':property_id' => $row['PropertyID'],
            ':image_url' => $row['ImageURL']
        ]);
    }
    echo "Property images migrated successfully.<br>";
}

// Call the migration functions
migrateUsers($source_db, $target_db);
migrateProperties($source_db, $target_db);
migrateReservations($source_db, $target_db);
migrateReviews($source_db, $target_db);
migrateAmenities($source_db, $target_db);
migrateImages($source_db, $target_db);

echo "Data migration completed successfully.";
?>
