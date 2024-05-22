<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="Home.php"><img src="resource/logo.png" alt="logo"/></a>
            </div>
            <div class="extra">
                <p><a href="Home.php">Locally<span>Cleck</span></a></p>
            </div>

            <div class="box">
            <input type="text" id="searchInput" placeholder="Search...">
            <i class="fas fa-search" id="searchIcon"></i>
            </div>

            <div class="dropdown">
             <button class="dropbtn">Categories</button>
             <div class="dropdown-content">
            <a href="categorypage.php?category_id=4">Baker</a>
            <a href="categorypage.php?category_id=2">Green Grocer</a>
            <a href="categorypage.php?category_id=3">FishMonger</a>
            <a href="categorypage.php?category_id=1">Butcher</a>
            <a href="categorypage.php?category_id=5">Delicatessen</a>
                </div>
            </div>
            <ul>
                <?php if(isset($_SESSION['user_id']) && $_SESSION['logged_in']): ?>
                    <!-- User is logged in -->
                    <li><a href="customerprofile.php"><i class="fa-solid fa-user"></i>Profile</a></li>
                    <li><a href="wishlist.php"><i class="fa-solid fa-heart"></i></a></li>
                    <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
                    <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i>Logout</a></li>
                <?php else: ?>
                    <!-- User is not logged in -->
                   <li><a href="Authenticate/customersignin.html"><i class="fa-solid fa-user"></i>Customer Signin</a></li> 
                      <li><a href="Authenticate/Tradersignin.html"><i class="fa-solid fa-home"></i>Trader Signin</a></li>  
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
     
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var searchInput = document.getElementById("searchInput");
            var searchIcon = document.getElementById("searchIcon");

            searchIcon.addEventListener("click", function() {
                var searchTerm = searchInput.value.trim();
                if (searchTerm !== "") {
                    // Perform search operation
                    searchProducts(searchTerm);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Search Term',
                        text: 'Please enter a search term.',
                    });
                }
            });

            searchInput.addEventListener("keyup", function(event) {
                if (event.key === "Enter") {
                    searchIcon.click();
                }
            });

            function searchProducts(searchTerm) {
                // Send AJAX request to search for products
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "functionalphp/search.php?product=" + encodeURIComponent(searchTerm), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = xhr.responseText;
                        if (response === "found") {
                            // Redirect to product detail page
                            window.location.href = "productDetail.php?product=" + encodeURIComponent(searchTerm);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Product Not Found',
                                text: 'No product found with the given search term.',
                            });
                        }
                    }
                };
                xhr.send();
            }
        });
    </script>