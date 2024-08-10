<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamestore";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// SQL to create tables
$sql = "
CREATE TABLE IF NOT EXISTS Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    RegistrationDate DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Developers (
    DeveloperID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Website VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS Games (
    GameID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10, 2) NOT NULL,
    ReleaseDate DATE,
    Stock INT DEFAULT 0,
    DeveloperID INT,
    FOREIGN KEY (DeveloperID) REFERENCES Developers(DeveloperID) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Orders (
    OrderID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    OrderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    Status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS OrderItems (
    OrderItemID INT PRIMARY KEY AUTO_INCREMENT,
    OrderID INT,
    GameID INT,
    Quantity INT NOT NULL,
    PriceAtPurchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (GameID) REFERENCES Games(GameID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Genres (
    GenreID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS GameGenres (
    GameID INT,
    GenreID INT,
    PRIMARY KEY (GameID, GenreID),
    FOREIGN KEY (GameID) REFERENCES Games(GameID) ON DELETE CASCADE,
    FOREIGN KEY (GenreID) REFERENCES Genres(GenreID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Reviews (
    ReviewID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    GameID INT,
    Rating INT,
    Comment TEXT,
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (GameID) REFERENCES Games(GameID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Country (
    CountryID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS Province (
    ProvinceID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    CountryID INT,
    FOREIGN KEY (CountryID) REFERENCES Country(CountryID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS City (
    CityID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    ProvinceID INT,
    FOREIGN KEY (ProvinceID) REFERENCES Province(ProvinceID) ON DELETE CASCADE
);
";

// Execute the SQL to create tables
if ($conn->multi_query($sql) === TRUE) {
    echo "Database and tables created successfully.<br>";
} else {
    echo "Error creating tables: " . $conn->error;
}

// Sample data insertion
$sql = "
INSERT INTO Users (Username, Email, Password, FirstName, LastName) VALUES 
('rahul123', 'rahul@example.com', 'password123', 'Rahul', 'Sharma'),
('anita456', 'anita@example.com', 'password456', 'Anita', 'Verma'),
('ajay789', 'ajay@example.com', 'password789', 'Ajay', 'Kumar');

INSERT INTO Developers (Name, Website) VALUES 
('GameDev Studios', 'http://gamedevstudios.com'),
('Pixel Perfect', 'http://pixelperfect.com');

INSERT INTO Games (Title, Description, Price, ReleaseDate, Stock, DeveloperID) VALUES 
('Epic Adventure', 'An epic journey through mystical lands.', 29.99, '2023-01-15', 100, 1),
('Puzzle Master', 'Challenge your mind with this engaging puzzle game.', 19.99, '2023-03-10', 50, 2);

INSERT INTO Orders (UserID, TotalAmount, Status) VALUES 
(1, 49.98, 'Pending'),
(2, 19.99, 'Shipped');

INSERT INTO OrderItems (OrderID, GameID, Quantity, PriceAtPurchase) VALUES 
(1, 1, 1, 29.99),
(1, 2, 1, 19.99),
(2, 2, 1, 19.99);

INSERT INTO Genres (Name) VALUES 
('Action'),
('Adventure'),
('Puzzle');

INSERT INTO GameGenres (GameID, GenreID) VALUES 
(1, 1),
(1, 2),
(2, 3);

INSERT INTO Reviews (UserID, GameID, Rating, Comment) VALUES 
(1, 1, 5, 'Amazing game! Highly recommend.'),
(2, 2, 4, 'Very challenging and fun.');

INSERT INTO Country (Name) VALUES 
('India');

INSERT INTO Province (Name, CountryID) VALUES 
('Maharashtra', 1),
('Karnataka', 1);

INSERT INTO City (Name, ProvinceID) VALUES 
('Mumbai', 1),
('Bangalore', 2);
";

// Execute the sample data insertion
if ($conn->multi_query($sql) === TRUE) {
    echo "Sample data inserted successfully.";
} else {
    echo "Error inserting sample data: " . $conn->error;
}

// Close connection
$conn->close();
?>