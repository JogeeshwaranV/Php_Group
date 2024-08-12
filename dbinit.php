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
    Website VARCHAR(255),
    UNIQUE(Name)
);

CREATE TABLE IF NOT EXISTS Games (
    GameID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10, 2) NOT NULL,
    ReleaseDate DATE,
    Stock INT DEFAULT 0,
    ImageFile VARCHAR(255),
    DeveloperID INT,
    FOREIGN KEY (DeveloperID) REFERENCES Developers(DeveloperID) ON DELETE SET NULL,
    UNIQUE(Title, DeveloperID)
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
    Name VARCHAR(50) NOT NULL,
    UNIQUE(Name)
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
    FOREIGN KEY (GameID) REFERENCES Games(GameID) ON DELETE CASCADE,
    UNIQUE(UserID, GameID)
);

CREATE TABLE IF NOT EXISTS Country (
    CountryID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    UNIQUE(Name)
);

CREATE TABLE IF NOT EXISTS Province (
    ProvinceID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    CountryID INT,
    FOREIGN KEY (CountryID) REFERENCES Country(CountryID) ON DELETE CASCADE,
    UNIQUE(Name, CountryID)
);

CREATE TABLE IF NOT EXISTS City (
    CityID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    ProvinceID INT,
    FOREIGN KEY (ProvinceID) REFERENCES Province(ProvinceID) ON DELETE CASCADE,
    UNIQUE(Name, ProvinceID)
);

CREATE TABLE IF NOT EXISTS ShoppingCarts (
    cartID INT PRIMARY KEY AUTO_INCREMENT,
    userID INT NOT NULL,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    purchased BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (userID) REFERENCES Users(UserID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS CartItems (
    cartItemID INT PRIMARY KEY AUTO_INCREMENT,
    cartID INT NOT NULL,
    gameID INT NOT NULL,
    quantity INT NOT NULL,
    priceAtAddition DECIMAL(10, 2),
    FOREIGN KEY (cartID) REFERENCES ShoppingCarts(cartID) ON DELETE CASCADE,
    FOREIGN KEY (gameID) REFERENCES Games(GameID) ON DELETE CASCADE
);
";

// Execute the SQL to create tables
if ($conn->multi_query($sql) === TRUE) {
    echo "Database and tables created successfully.";
    // Wait for all results to be processed
    while ($conn->next_result()) {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }
} else {
    die("Error creating tables: " . $conn->error);
}

// Sample data insertion with prepared statements

// Insert Users
$users = [
    ['rahul123', 'rahul@example.com', password_hash('password123', PASSWORD_DEFAULT), 'Rahul', 'Sharma'],
    ['anita456', 'anita@example.com', password_hash('password456', PASSWORD_DEFAULT), 'Anita', 'Verma'],
    ['ajay789', 'ajay@example.com', password_hash('password789', PASSWORD_DEFAULT), 'Ajay', 'Kumar']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Users (Username, Email, Password, FirstName, LastName) VALUES (?, ?, ?, ?, ?)");
foreach ($users as $user) {
    $stmt->bind_param("sssss", $user[0], $user[1], $user[2], $user[3], $user[4]);
    $stmt->execute();
}
$stmt->close();

// Insert Developers
$developers = [
    ['Naughty Dog', 'https://www.naughtydog.com'],
    ['Sucker Punch Productions', 'https://www.suckerpunch.com'],
    ['CD Projekt Red', 'https://www.cdprojekt.com'],
    ['Santa Monica Studio', 'https://sms.playstation.com'],
    ['Insomniac Games', 'https://insomniac.games'],
    ['Rockstar Games', 'https://www.rockstargames.com'],
    ['Guerrilla Games', 'https://www.guerrilla-games.com'],
    ['Kojima Productions', 'https://www.kojimaproductions.jp'],
    ['FromSoftware', 'https://www.fromsoftware.jp'],
    ['EA Sports', 'https://www.ea.com/games/fifa'],
    ['2K Games', 'https://www.2k.com']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Developers (Name, Website) VALUES (?, ?)");
foreach ($developers as $developer) {
    $stmt->bind_param("ss", $developer[0], $developer[1]);
    $stmt->execute();
}
$stmt->close();

// Insert Games
$games = [
    ['The Last of Us Part II', 'The Last of Us Part II is an action-adventure game played from a third-person perspective.', 59.99, '2020-06-19', 100, 'lastofuspart2.jpg', 1],
    ['Ghost of Tsushima', 'Ghost of Tsushima is an open-world action-adventure game set in feudal Japan.', 49.99, '2020-07-17', 200, 'ghostoftsushima.jpg', 2],
    ['Cyberpunk 2077', 'Cyberpunk 2077 is an action RPG set in a dystopian future.', 69.99, '2020-12-10', 60, 'cyberpunk2077.jpg', 3],
    ['God of War', 'God of War is an action-adventure game that follows the journey of Kratos in the Norse mythology.', 39.99, '2018-04-20', 200, 'godofwar.jpg', 4],
    ['Spider-Man', 'Spider-Man is an action-adventure game that features the titular superhero from Marvel Comics.', 49.99, '2018-09-07', 200, 'spiderman.jpg', 5],
    ['Red Dead Redemption 2', 'Red Dead Redemption 2 is an action-adventure game set in the late 19th century America.', 59.99, '2018-10-26', 200, 'red_deadredemption2.jpg', 6],
    ['Horizon Zero Dawn', 'Horizon Zero Dawn is an action RPG set in a post-apocalyptic world where robotic creatures dominate.', 29.99, '2017-02-28', 200, 'horizonzerodawn.jpg', 7],
    ['Death Stranding', 'Death Stranding is an action game set in a post-apocalyptic world with an emphasis on exploration.', 59.99, '2019-11-08', 200, 'deathstranding.jpg', 8],
    ['Uncharted 4', 'Uncharted 4 is an action-adventure game that continues the story of Nathan Drake.', 39.99, '2016-05-10', 200, 'uncharted4.jpg', 9],
    ['Bloodborne', 'Bloodborne is an action RPG set in a dark and gothic world with a focus on fast-paced combat.', 39.99, '2015-03-24', 200, 'bloodborne.jpg', 10]
];

$stmt = $conn->prepare("INSERT IGNORE INTO Games (Title, Description, Price, ReleaseDate, Stock, ImageFile, DeveloperID) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($games as $game) {
    $stmt->bind_param("sssdisi", $game[0], $game[1], $game[2], $game[3], $game[4], $game[5], $game[6]);
    $stmt->execute();
}
$stmt->close();

// Insert Orders
$orders = [
    [1, '2024-08-01 10:00:00', 59.99, 'Pending'],
    [2, '2024-08-02 11:30:00', 49.99, 'Shipped']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Orders (UserID, OrderDate, TotalAmount, Status) VALUES (?, ?, ?, ?)");
foreach ($orders as $order) {
    $stmt->bind_param("isds", $order[0], $order[1], $order[2], $order[3]);
    $stmt->execute();
}
$stmt->close();

// Insert OrderItems
$orderItems = [
    [1, 1, 1, 59.99],
    [2, 2, 1, 49.99]
];

$stmt = $conn->prepare("INSERT IGNORE INTO OrderItems (OrderID, GameID, Quantity, PriceAtPurchase) VALUES (?, ?, ?, ?)");
foreach ($orderItems as $item) {
    $stmt->bind_param("iiid", $item[0], $item[1], $item[2], $item[3]);
    $stmt->execute();
}
$stmt->close();

// Insert Genres
$genres = [
    ['Action'],
    ['Adventure'],
    ['RPG'],
    ['Shooter'],
    ['Strategy'],
    ['Sports'],
    ['Simulation'],
    ['Puzzle'],
    ['Horror'],
    ['Racing']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Genres (Name) VALUES (?)");
foreach ($genres as $genre) {
    $stmt->bind_param("s", $genre[0]);
    $stmt->execute();
}
$stmt->close();

// Insert GameGenres
$gameGenres = [
    [1, 1],
    [1, 2],
    [2, 2],
    [2, 4],
    [3, 3],
    [4, 1],
    [4, 3],
    [5, 1],
    [5, 2],
    [6, 1],
    [6, 7]
];

$stmt = $conn->prepare("INSERT IGNORE INTO GameGenres (GameID, GenreID) VALUES (?, ?)");
foreach ($gameGenres as $gameGenre) {
    $stmt->bind_param("ii", $gameGenre[0], $gameGenre[1]);
    $stmt->execute();
}
$stmt->close();

// Insert Reviews
$reviews = [
    [1, 1, 5, 'An amazing game with a powerful story!'],
    [2, 2, 4, 'Great open-world game with beautiful visuals.'],
    [3, 3, 3, 'Interesting concept but had some bugs.']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Reviews (UserID, GameID, Rating, Comment) VALUES (?, ?, ?, ?)");
foreach ($reviews as $review) {
    $stmt->bind_param("iiis", $review[0], $review[1], $review[2], $review[3]);
    $stmt->execute();
}
$stmt->close();

// Insert Country
$countries = [
    ['United States'],
    ['Canada'],
    ['United Kingdom'],
    ['Germany'],
    ['France'],
    ['Japan'],
    ['China'],
    ['South Korea'],
    ['Australia'],
    ['Brazil']
];

$stmt = $conn->prepare("INSERT IGNORE INTO Country (Name) VALUES (?)");
foreach ($countries as $country) {
    $stmt->bind_param("s", $country[0]);
    $stmt->execute();
}
$stmt->close();

// Insert Province
$provinces = [
    ['California', 1],
    ['Ontario', 2],
    ['England', 3],
    ['Bavaria', 4],
    ['Île-de-France', 5],
    ['Tokyo', 6],
    ['Beijing', 7],
    ['Seoul', 8],
    ['New South Wales', 9],
    ['São Paulo', 10]
];

$stmt = $conn->prepare("INSERT IGNORE INTO Province (Name, CountryID) VALUES (?, ?)");
foreach ($provinces as $province) {
    $stmt->bind_param("si", $province[0], $province[1]);
    $stmt->execute();
}
$stmt->close();

// Insert City
$cities = [
    ['Los Angeles', 1],
    ['Toronto', 2],
    ['London', 3],
    ['Munich', 4],
    ['Paris', 5],
    ['Osaka', 6],
    ['Shanghai', 7],
    ['Busan', 8],
    ['Sydney', 9],
    ['Rio de Janeiro', 10]
];

$stmt = $conn->prepare("INSERT IGNORE INTO City (Name, ProvinceID) VALUES (?, ?)");
foreach ($cities as $city) {
    $stmt->bind_param("si", $city[0], $city[1]);
    $stmt->execute();
}
$stmt->close();

// Insert ShoppingCarts
$shoppingCarts = [
    [1, '2024-08-01 10:00:00', 0],
    [2, '2024-08-02 11:30:00', 1]
];

$stmt = $conn->prepare("INSERT IGNORE INTO ShoppingCarts (userID, createdDate, purchased) VALUES (?, ?, ?)");
foreach ($shoppingCarts as $cart) {
    $stmt->bind_param("isi", $cart[0], $cart[1], $cart[2]);
    $stmt->execute();
}
$stmt->close();

// Insert CartItems
$cartItems = [
    [1, 1, 1, 59.99],
    [2, 2, 2, 49.99]
];

$stmt = $conn->prepare("INSERT IGNORE INTO CartItems (cartID, gameID, quantity, priceAtAddition) VALUES (?, ?, ?, ?)");
foreach ($cartItems as $item) {
    $stmt->bind_param("iiid", $item[0], $item[1], $item[2], $item[3]);
    $stmt->execute();
}
$stmt->close();

// Close connection
$conn->close();
