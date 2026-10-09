-- Users Table (Dimension Table)
CREATE TABLE Dim_Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    user_type ENUM('Host', 'Guest') NOT NULL,  -- Host or Guest
    date_joined DATE NOT NULL
);

-- Properties Table (Dimension Table)
CREATE TABLE Dim_Properties (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    host_id INT NOT NULL,
    property_name VARCHAR(255) NOT NULL,
    description TEXT,
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(10),
    price_per_night DECIMAL(10, 2) NOT NULL,
    max_guests INT NOT NULL,
    FOREIGN KEY (host_id) REFERENCES Dim_Users(user_id)  -- Links to host in Users table
);

-- Reservations Table (Fact Table)
CREATE TABLE Fact_Reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    guest_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES Dim_Properties(property_id),
    FOREIGN KEY (guest_id) REFERENCES Dim_Users(user_id)  -- Links to guest in Users table
);

-- Reviews Table (Dimension Table)
CREATE TABLE Dim_Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    guest_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),  -- Ratings from 1 to 5 stars
    review_text TEXT,
    review_date DATE NOT NULL,
    FOREIGN KEY (property_id) REFERENCES Dim_Properties(property_id),
    FOREIGN KEY (guest_id) REFERENCES Dim_Users(user_id)
);

-- Property_Amenities Table (Dimension Table)
CREATE TABLE Dim_Property_Amenities (
    amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    amenity_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES Dim_Properties(property_id)
);

-- Property_Images Table (Dimension Table)
CREATE TABLE Dim_Property_Images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES Dim_Properties(property_id)
);
