<script>
    function addToCart(productName, productPrice) {
    // Prepare data to send via AJAX
    var formData = new FormData();
    formData.append('productName', productName);
    formData.append('productPrice', productPrice);

    // Send AJAX request to addtocart.php
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'functionalphp/addtocart.php', true);

    // Set up callback function when AJAX request completes
    xhr.onload = function() {
        if (xhr.status === 200) {
            // AJAX request successful, show toast notification
            Toastify({
                text: 'Product added to cart successfully!',
                duration: 300,  // Display duration in milliseconds
                gravity: 'top',  // Position at the top
                position: 'right',  // Align to the right
                backgroundColor: 'green',  // Custom background color
                className: 'toastify-style',  // Custom CSS class
                stopOnFocus: true  // Stop timer when window is focused
            }).showToast();
        } else {
            Toastify({
                text: 'LOGIN REQUIRED!',
                duration: 300,  // Display duration in milliseconds
                gravity: 'top',  // Position at the top
                position: 'right',  // Align to the right
                backgroundColor: 'red',  // Custom background color
                className: 'toastify-style',  // Custom CSS class
                stopOnFocus: true  // Stop timer when window is focused
            }).showToast();
        }
    };

    // Send the FormData object (product details) to addtocart.php
    xhr.send(formData);
}
</script>
<script>
function addToWishlist(productName, productPrice) {
    // Prepare data to send via AJAX
    var formData = new FormData();
    formData.append('productName', productName);
    formData.append('productPrice', productPrice);

    // Send AJAX request to addtowishlist.php
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'functionalphp/addtowishlist.php', true);

    // Set up callback function when AJAX request completes
    xhr.onload = function() {
        if (xhr.status === 200) {
            // AJAX request successful, show toast notification
            Toastify({
                text: 'Added to WishList!',
                duration: 300,  // Display duration in milliseconds
                gravity: 'top',  // Position at the top
                position: 'right',  // Align to the right
                backgroundColor: 'green',  // Custom background color
                className: 'toastify-style',  // Custom CSS class
                stopOnFocus: true  // Stop timer when window is focused
            }).showToast();
        } else {
            Toastify({
                text: 'PLEASE LOGIN!',
                duration: 300,  // Display duration in milliseconds
                gravity: 'top',  // Position at the top
                position: 'right',  // Align to the right
                backgroundColor: 'red',  // Custom background color
                className: 'toastify-style',  // Custom CSS class
                stopOnFocus: true  // Stop timer when window is focused
            }).showToast();
        }
    };

    // Send the FormData object (product details) to addtowishlist.php
    xhr.send(formData);
}
</script>