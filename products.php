<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

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
$categories_result = $conn->query("SELECT * FROM categories");

// Construct SQL query with filters
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

if (!empty($filter_name)) {
    $sql .= " AND p.name LIKE '%" . $conn->real_escape_string($filter_name) . "%'";
}

if (!empty($filter_min_price)) {
    $sql .= " AND p.price >= " . $conn->real_escape_string($filter_min_price);
}

if (!empty($filter_max_price)) {
    $sql .= " AND p.price <= " . $conn->real_escape_string($filter_max_price);
}

if (!empty($filter_category)) {
    $sql .= " AND p.category_id = " . $conn->real_escape_string($filter_category);
}

$result = $conn->query($sql);
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

            .container {
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
                /* Remove underline */
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
                /* Background color for the card */
                position: relative;
                transition: transform 0.3s ease;
                text-decoration: none !important;
            }

            .product:hover {
                transform: scale(1.05);
            }

            .product a {
                text-decoration: none !important;
                /* Remove underline from any links */
            }

            .product img {
                width: 100%;
                height: 350px;
                /* Fixed height for all images */
                object-fit: cover;
                /* Ensures images cover the space without distortion */
                transition: transform 0.3s ease;
            }

            .product:hover img {
                transform: scale(1.05);
            }

            .product-details {
                padding: 15px;
                text-decoration: none;
            }

            .product-title {
                font-size: 20px;
                font-weight: bold;
                color: #fff;
                margin: 0;
                line-height: 1.2;
                text-decoration: none;
            }

            .product-price {
                font-size: 18px;
                color: #ffff;
                margin: 10px 0;
                text-decoration: none;
            }

            .product-description {
                font-size: 14px;
                color: #ffff;
                margin: 10px 0;
                /* Added spacing above and below */
                text-decoration: none;
            }

            .product-category {
                font-size: 16px;
                color: #fff;
                /* Style the category with the same color as the price */
                margin-bottom: 10px;
                /* Add spacing below the category */
                font-weight: bold;
                /* Make the text bold */
                text-transform: uppercase;
                /* Transform text to uppercase */
                letter-spacing: 1px;
                /* Add space between letters */
                background-color: teal;
                /* Light blue background color */
                padding: 5px 10px;
                /* Add padding to the text */
                border-radius: 5px;
                /* Rounded corners */
                display: inline-block;
            }

            button {
                background-color: black;
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
                // Clear all filter inputs
                document.querySelector('input[name="name"]').value = '';
                document.querySelector('input[name="min_price"]').value = '';
                document.querySelector('input[name="max_price"]').value = '';
                document.querySelector('select[name="category"]').value = '';

                // Submit the form to refresh the product list without filters
                document.querySelector('.filter-form').submit();
            }
        </script>
    </head>
    <body>
    <div class="container">
        <h1>Our Products</h1>
        <form method="POST" class="filter-form">
            <input type="text" name="name" placeholder="Search by name" value="<?php echo htmlspecialchars($filter_name); ?>">
            <input type="number" name="min_price" placeholder="Min price" value="<?php echo htmlspecialchars($filter_min_price); ?>">
            <input type="number" name="max_price" placeholder="Max price" value="<?php echo htmlspecialchars($filter_max_price); ?>">
            <select name="category">
                <option value="">Select category</option>
                <?php while ($cat = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php if ($filter_category == $cat['id'])
                           echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
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
                        <a href="product_details.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="product-details">
                                <p class="product-title"><?php echo htmlspecialchars($row['name']); ?></p>
                                <p class="product-price">$<?php echo htmlspecialchars($row['price']); ?></p>
                                <p class="product-description"><?php echo htmlspecialchars($row['short_description']); ?></p>
                                <p class="product-category"><?php echo htmlspecialchars($row['category_name']); ?></p>
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
</body>

</html>

<?php $conn->close(); ?>