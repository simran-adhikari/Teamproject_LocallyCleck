<?php
session_start();


// Establish database connection
$connection = oci_connect("diwon", "hackvayo", "//localhost/xe");
if (!$connection) {
    $error_message = oci_error();
    echo "Failed to connect to Oracle: " . $error_message['message'];
    exit();
}

// SQL query to fetch shop_name and profileimage from Shop table
$sql = "SELECT shop_name, profileimage FROM Shop";

// Prepare the SQL statement
$stmt = oci_parse($connection, $sql);

// Execute the SQL statement
oci_execute($stmt);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="home.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
      integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap"
      rel="stylesheet"
    />
    <title>Document</title>
  </head>
  <body>
    <nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="#"><img src="resource/logo.png" alt="logo"/></a>
            </div>
            <div class="extra">
                <p><a href="#">Locally<span>Cleck</span></a></p>
            </div>
            <div class="box">
                <input type="text" name=""><i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Categories</button>
                <div class="dropdown-content">
                    <a href="#">Baker</a>
                    <a href="#">Green Grocer</a>
                    <a href="#">FishMonger</a>
                    <a href="#">Butcher</a>
                    <a href="#">Delicatessen</a>
                </div>
            </div>
            <ul>
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <!-- User is logged in -->
                    <li><a href="#"><i class="fa-solid fa-user"></i>Profile</a></li>
                    <li><a href="#"><i class="fa-solid fa-heart"></i>Wishlist</a></li>
                    <li><a href="#"><i class="fa-solid fa-cart-shopping"></i>Cart</a></li>
                    <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i>Logout</a></li>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <li><a href="customersignin.html"><i class="fa-solid fa-user"></i>Customer Signin</a></li>
                    <li><a href="Tradersignin.html"><i class="fa-solid fa-home"></i>Trader Signin</a></li>
                    <li><a href="#"><i class="fa-solid fa-cart-shopping"></i></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
     
<div class="section1">
    <?php
         echo '<div class="trader-box">';

         // Loop through each row of the result set
         while ($row = oci_fetch_assoc($stmt)) {
             $shopName = $row['SHOP_NAME'];
             $imageData = $row['PROFILEIMAGE']->load();
 
              // Encode the BLOB data as base64
             $encodedImageData = base64_encode($imageData);
 
             // Determine the image type based on the first few bytes of the image data
             $header = substr($imageData, 0, 4);
             $imageType = 'image/jpeg'; // default to JPEG
 
             if (strpos($header, 'FFD8') === 0) {
                 $imageType = 'image/jpeg'; // JPEG
             } elseif (strpos($header, '89504E47') === 0) {
                 $imageType = 'image/png'; // PNG
             }
 
             // Display each trader with the image
             echo '<div class="trader">';
             echo '<a href="trader_details.php?shop_name=' . urlencode($shopName) . '"><img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $shopName . '"></a>';
             echo '<span class="trader-name">' . $shopName . '</span>';
             echo '</div>';
         }
 
         echo '</div>';

?>

      <div class="banner">

        <div class="banner-container">
       
            <div class="banner">
                <img src="resource/Home/banner1.jpeg" class="banner-slide">
                <img src="resource/Home/banner2.jpeg" class="banner-slide">
                <img src="resource/Home/banner4.jpeg" class="banner-slide">
                <img src="resource/Home/banner3.jpeg" class="banner-slide">
               

            </div>
        </div>
      </div>
      </div>
        
        <div class="image-section">
            <div class="image-box">
                <img src="resource/Home/image1.jpg" alt="Image 1">
            </div>
            <div class="image-box">
                <img src="resource/Home/image2.jpg" alt="Image 2">
            </div>
            <div class="image-box">
                <img src="resource/Home/image3.jpg" alt="Image 3">
            </div>
            <div class="image-box">
                <img src="resource/Home/image4.jpg" alt="Image 4">
            </div>
            <div class="image-box">
                <img src="resource/Home/image5.jpg" alt="Image 5">
            </div> 
            <div class="image-box">
                <img src="resource/Home/image6.jpg" alt="Image 5">
            </div>
             <div class="image-box">
                <img src="resource/Home/image7.jpg" alt="Image 5">
            </div>
            <div class="image-box">
                <img src="resource/Home/image8.jpg" alt="Image 5">
            </div>
            <div class="image-box">
                <img src="resource/Home/image8.jpg" alt="Image 5">
            </div>
        </div>

       
<br><br>
</div>
<div class="containerr">
    <header>
        <h1>BEST SELLERS</h1>
        <div class="shopping">
        </div>
    </header>

    <div class="list">
        <div class="card">
            <ul class="listCard">
            </ul>
            <div class="checkOut">
                <div class="closeShopping">Close</div>
            </div>
        </div>
    </div>
</div>

<div class="custom-container">
   <div class="sort">
     <header1>
        <h1>CHECK THESE OUT </h1>
        <div class="custom-shopping">
        </div>
     
    </header1>
    <div class="sorting-options">
        <label for="sort">Sort by:</label>
        <select id="sort">
            <option value="name">Name</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="id">ID</option>
        </select>
    </div>
   </div>
    <div class="custom-list">
        <div class="custom-card">
            <ul class="custom-listCard">
            </ul>
            <div class="custom-checkOut">
                <div class="custom-closeShopping">Close</div>
            </div>
        </div>
    </div>
  </div>
 </div>
 

<footer class="section-p1">
    <div class="col">
        <img class="logof" src="resource/logo.png" alt="image" height="60px" width="60px">
        <h4>Contact</h4>
        <p><strong>Address:</strong> 4600 Nepal, Kathmandu, Thapathali, TT TBC</p>
        <p><strong>Phone:</strong> +977 9812455645</p>
        <p><strong>Hours:</strong> 10:00 - 18:00, Sun - Fri</p><br>
        <div class="follow">
            <h4>Follow us</h4>
            <div class="icon" >
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-twitter"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-pinterest-p"></i>
                <i class="fa-brands fa-youtube"></i>
            </div>
        </div>
    </div>

    <div class="col">
        <h4>About</h4>
        <a href="#">About us</a>
        <a href="#">Delivery Information</a>
        <a href="#">Privay Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="#">Contact Us</a>
    </div>

    <div class="col">
        <h4>My Account</h4>
        <a href="#">Sign In</a>
        <a href="#">View Cart</a>
        <a href="#">My Whislist</a>
        <a href="#">Track My Order</a>
        <a href="#">Help</a>
    </div>

    <div class="col install">
        <h4>Install App</h4>
        <p>From App Store or Google Play</p>
        <div class="row">
            <img src="syau.jpg" alt="image">
            <img src="play.jpg" alt="image">
            <p>Secure Payment Gateways</p>
            <img src="resource/Home/paypal.png" alt="image"  style="width: 100px; height: 30px; padding: 9px 35px 9px 35px ; background : #ffffff;">
        </div>
            
    </div>

    <div class="copyright">
        <p>&copy; 2024, DEspans - EOS IT Project Making my own Website </p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        const slides = document.getElementsByClassName("banner-slide");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 2500); 
    }
});
// First set of variables and arrays
let openShopping = document.querySelector('.shopping');
let closeShopping = document.querySelector('.closeShopping');
let list = document.querySelector('.list');
let listCard = document.querySelector('.listCard');
let body = document.querySelector('body');
let total = document.querySelector('.total');
let quantity = document.querySelector('.quantity');

openShopping.addEventListener('click', () => {
    body.classList.add('active');
});

closeShopping.addEventListener('click', () => {
    body.classList.remove('active');
});

let products1 = [
    {
        id: 1,
        name: 'PRODUCT NAME 1',
        image: '1.PNG',
        price: 120000
    },
    {
        id: 2,
        name: 'PRODUCT NAME 2',
        image: '2.PNG',
        price: 120000
    },
    {
        id: 3,
        name: 'PRODUCT NAME 3',
        image: '3.PNG',
        price: 220000
    },
    {
        id: 4,
        name: 'PRODUCT NAME 4',
        image: '4.PNG',
        price: 123000
    },
    {
        id: 5,
        name: 'PRODUCT NAME 5',
        image: '5.PNG',
        price: 320000
    },
    {
        id: 6,
        name: 'PRODUCT NAME 6',
        image: '6.PNG',
        price: 120000
    }
];

let listCards1 = [];

function initApp1() {
    // Fetch product data from PHP script
    fetch('getProducts.php')
        .then(response => response.json())
        .then(products => {
            products.forEach((product, index) => {
                let newDiv = document.createElement('div');
                newDiv.classList.add('item');
                newDiv.innerHTML = `
                    <img src="resource/Home/image/${index + 1}.PNG">
                    
                    <div class="title">${product.name}</div>
                    <div class="price">${product.price.toLocaleString()}</div>
                    <button onclick="addToCard1(${index})">Add To Card</button>`;

                list.appendChild(newDiv);
            });
        })
        .catch(error => {
            console.error('Error fetching product data:', error);
        });
}

// let listCards1 = [];

// function initApp1() {
//     products1.forEach((value, key) => {
//         let newDiv = document.createElement('div');
//         newDiv.classList.add('item');
//         newDiv.innerHTML = `
//             <img src="resource/Home/image/${value.image}">
//             <div class="title">${value.name}</div>
//             <div class="price">${value.price.toLocaleString()}</div>
//             <button onclick="addToCard1(${key})">Add To Card</button>`;
//         list.appendChild(newDiv);
//     });
// }

initApp1();

function addToCard1(key) {
    if (listCards1[key] == null) {
        listCards1[key] = JSON.parse(JSON.stringify(products1[key]));
        listCards1[key].quantity = 1;
    }
    reloadCard1();
}

function reloadCard1() {
    listCard.innerHTML = '';
    let count = 0;
    let totalPrice = 0;
    listCards1.forEach((value, key) => {
        totalPrice = totalPrice + value.price;
        count = count + value.quantity;
        if (value != null) {
            let newDiv = document.createElement('li');
            newDiv.innerHTML = `
                <div><img src="resource/Home/image/${value.image}"/></div>
                <div>${value.name}</div>
                <div>${value.price.toLocaleString()}</div>
                <div>
                    <button onclick="changeQuantity1(${key}, ${value.quantity - 1})">-</button>
                    <div class="count">${value.quantity}</div>
                    <button onclick="changeQuantity1(${key}, ${value.quantity + 1})">+</button>
                </div>`;
            listCard.appendChild(newDiv);
        }
    });
    total.innerText = totalPrice.toLocaleString();
    quantity.innerText = count;
}

function changeQuantity1(key, quantity) {
    if (quantity == 0) {
        delete listCards1[key];
    } else {
        listCards1[key].quantity = quantity;
        listCards1[key].price = quantity * products1[key].price;
    }
    reloadCard1();
}

// Second set of variables and arrays
let openShopping2 = document.querySelector('.custom-shopping');
let closeShopping2 = document.querySelector('.custom-closeShopping');
let list2 = document.querySelector('.custom-list');
let listCard2 = document.querySelector('.custom-listCard');
let body2 = document.querySelector('body');
let total2 = document.querySelector('.total');
let quantity2 = document.querySelector('.quantity');

openShopping2.addEventListener('click', () => {
    body2.classList.add('active');
});

closeShopping2.addEventListener('click', () => {
    body2.classList.remove('active');
});

let products2 = [
{
        id: 7,
        name: 'PRODUCT NAME 4',
        image: '7.png',
        price: 123000
    },
    {
        id: 8,
        name: 'PRODUCT NAME 5',
        image: '8.png',
        price: 320000
    },
    {
        id:9,
        name: 'PRODUCT NAME 6',
        image: '9.PNG',
        price: 120000
    },
    {
        id: 10,
        name: 'PRODUCT NAME 4',
        image: '10.png',
        price: 123000
    },
    {
        id: 11,
        name: 'PRODUCT NAME 5',
        image: '11.png',
        price: 320000
    },
    {
        id:12,
        name: 'PRODUCT NAME 6',
        image: '12.png',
        price: 120000
    }
];

let listCards2 = [];

function initApp2() {
    products2.forEach((value, key) => {
        let newDiv = document.createElement('div');
        newDiv.classList.add('custom-item');
        newDiv.innerHTML = `
            <img src="resource/Home/image/${value.image}">
            <div class="custom-title">${value.name}</div>
            <div class="custom-price">${value.price.toLocaleString()}</div>
            <button onclick="addToCard2(${key})">Add To Card</button>`;
        list2.appendChild(newDiv);
    });
}

initApp2();

function addToCard2(key) {
    if (listCards2[key] == null) {
        listCards2[key] = JSON.parse(JSON.stringify(products2[key]));
        listCards2[key].quantity = 1;
    }
    reloadCard2();
}

function reloadCard2() {
    listCard2.innerHTML = '';
    let count = 0;
    let totalPrice = 0;
    listCards2.forEach((value, key) => {
        totalPrice = totalPrice + value.price;
        count = count + value.quantity;
        if (value != null) {
            let newDiv = document.createElement('li');
            newDiv.innerHTML = `
                <div><img src="resource/Home/image/${value.image}"/></div>
                <div>${value.name}</div>
                <div>${value.price.toLocaleString()}</div>
                <div>
                    <button onclick="changeQuantity2(${key}, ${value.quantity - 1})">-</button>
                    <div class="count">${value.quantity}</div>
                    <button onclick="changeQuantity2(${key}, ${value.quantity + 1})">+</button>
                </div>`;
            listCard2.appendChild(newDiv);
        }
    });
    total2.innerText = totalPrice.toLocaleString();
    quantity2.innerText = count;
}

function changeQuantity2(key, quantity) {
    if (quantity == 0) {
        delete listCards2[key];
    } else {
        listCards2[key].quantity = quantity;
        listCards2[key].price = quantity * products2[key].price;
    }
    reloadCard2();
}


</script>
  </body>
</html>
