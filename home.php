
<?php 
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include 'header.php'; ?>



<!-- Custom CSS Styles -->
<style>
    body {
        background-color: #f8f9fa; 
        font-family: 'Arial', sans-serif; 
        color: #333; 
    }

    .banner {
        background-image: url('images/banner.jpg');
        height: 50vh;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .banner h1 {
        position: absolute;
        bottom: 20px;
        left: 20px;
        color: white;
        font-size: 3rem; 
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); 
    }

    h2 {
        font-size: 2rem; 
        margin-bottom: 20px; 
    }

    .img-fluid {
        border-radius: 10px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
        transition: transform 0.2s;
        margin-bottom: 20px; 
    }

    .img-fluid:hover {
        transform: scale(1.05);
    }

    .container {
        margin-top: 30px;
    }
</style>

<!-- Banner Image -->
<div class="container-fluid p-0">
    <div class="banner">
        <h1>Welcome to the Game Store</h1> 
    </div>
</div>

<!-- Trending Games Section -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Trending Games</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <img src="images/game1.jpg" class="img-fluid" alt="Game 1">
        </div>
        <div class="col-md-4 mb-4"> 
            <img src="images/game2.png" class="img-fluid" alt="Game 2">
        </div>
        <div class="col-md-4 mb-4"> 
            <img src="images/game3.avif" class="img-fluid" alt="Game 3">
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>