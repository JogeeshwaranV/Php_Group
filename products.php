<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
include 'header.php';
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamestore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filter variables
$filter_name = '';
$filter_min_price = '';
$filter_max_price = '';
$filter_category = '';

// Update filter variables if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filter_name = isset($_POST['name']) ? $_POST['name'] : '';
    $filter_min_price = isset($_POST['min_price']) ? $_POST['min_price'] : '';
    $filter_max_price = isset($_POST['max_price']) ? $_POST['max_price'] : '';
    $filter_category = isset($_POST['category']) ? $_POST['category'] : '';
}

// Fetch categories for the filter
$categories_result = $conn->query("SELECT * FROM Genres");

// Construct SQL query with filters using prepared statements
$sql = "SELECT g.*, GROUP_CONCAT(DISTINCT gr.Name) as genre_names FROM Games g
LEFT JOIN GameGenres gg ON g.GameID = gg.GameID
LEFT JOIN Genres gr ON gg.GenreID = gr.GenreID
WHERE 1=1";

$params = [];
$types = '';

if (!empty($filter_name)) {
    $sql .= " AND g.Title LIKE ?";
    $params[] = '%' . $filter_name . '%';
    $types .= 's';
}

if (!empty($filter_min_price)) {
    $sql .= " AND g.Price >= ?";
    $params[] = $filter_min_price;
    $types .= 'd';
}

if (!empty($filter_max_price)) {
    $sql .= " AND g.Price <= ?";
    $params[] = $filter_max_price;
    $types .= 'd';
}

if (!empty($filter_category)) {
    $sql .= " AND gg.GenreID = ?";
    $params[] = $filter_category;
    $types .= 'i';
}

$sql .= " GROUP BY g.GameID";

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$products_found = $result->num_rows > 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container-product {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .nav {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .nav a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
        }

        .nav a:hover {
            text-decoration: none;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form input,
        .filter-form select {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .filter-form button {
            padding: 10px 20px;
            border: none;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            background-color: #ddd;
            padding: 10px;
        }

        .product {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: #000;
            position: relative;
            transition: transform 0.3s ease;
            text-decoration: none !important;
        }

        .product:hover {
            transform: scale(1.05);
        }

        .product a {
            text-decoration: none !important;
        }

        .product img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product:hover img {
            transform: scale(1.05);
        }

        .product-details {
            padding: 15px;
        }

        .product-title {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }

        .product-price {
            font-size: 18px;
            color: #fff;
            margin: 10px 0;
        }

        .product-description {
            font-size: 14px;
            color: #fff;
            margin: 10px 0;
        }

        .product-category {
            font-size: 16px;
            color: #fff;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: teal;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        button {
            background-color: #ffcc00!important;
            color: #000 !important;
        }
        button:hover {
            background-color: black !important;
            color: #fff !important;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            border: none;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            width: 150px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function clearFilters() {
            document.querySelector('input[name="name"]').value = '';
            document.querySelector('input[name="min_price"]').value = '';
            document.querySelector('input[name="max_price"]').value = '';
            document.querySelector('select[name="category"]').value = '';
            document.querySelector('.filter-form').submit();
        }
    </script>
</head>

<body>
    <div class="container-product">
        <h1>Our Products</h1>
        <form method="POST" class="filter-form">
            <input type="text" name="name" placeholder="Search by name" value="<?php echo htmlspecialchars($filter_name); ?>">
            <input type="number" name="min_price" placeholder="Min price" value="<?php echo htmlspecialchars($filter_min_price); ?>">
            <input type="number" name="max_price" placeholder="Max price" value="<?php echo htmlspecialchars($filter_max_price); ?>">
            <select name="category">
                <option value="">Select category</option>
                <?php while ($cat = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($cat['GenreID']); ?>" <?php if ($filter_category == $cat['GenreID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Filter</button>
            <button type="button" onclick="clearFilters()">Clear Filters</button>
        </form>
        <?php if ($products_found): ?>
            <div class="products">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <a href="product_details.php?id=<?php echo htmlspecialchars($row['GameID']); ?>">
                            <img src="images/<?php echo htmlspecialchars($row['ImageFile']); ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>">
                            <div class="product-details">
                                <p class="product-title"><?php echo htmlspecialchars($row['Title']); ?></p>
                                <p class="product-price">$<?php echo htmlspecialchars($row['Price']); ?></p>
                                <p class="product-description"><?php echo htmlspecialchars($row['Description']); ?></p>
                                <p class="product-category"><?php echo htmlspecialchars($row['genre_names']); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No products found matching your criteria.</p>
        <?php endif; ?>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>