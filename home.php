<?php
require_once 'controller.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style_home.css">
    <script src="script.js"></script>
</head>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Product Management</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $username; ?></a></li>
                <li>
                    <form method="post" action="home.php" class="logout_btn">
                        <button type="submit" name="logout" style="background: none; border: none; padding: 0; cursor: pointer;">
                            <span class="glyphicon glyphicon-log-out"></span> Logout
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="add_product text-center">
            <h2>Add Product</h2>
            <form method="post" action="home.php" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Name" required>
                <input type="text" name="product_price" placeholder="Price" required onkeypress="return isNumberKey(event)">
                <input type="file" name="product_image" accept="image/*" onchange="previewAddProductImage(event)">
                <img id="addProductPreview" src="" alt="Preview" style="display: none; width: 150px;">
                <button type="submit" name="add_product" class="btn btn-success">Submit</button>
            </form>
        </div>
        <div class="product_list text-center">
            <h2>Product List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $key => $product) : ?>
                        <tr>
                            <td>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 150px;">
                            </td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo number_format($product['price'], 0, '.', '.') . " VNĐ"; ?></td>
                            <td>
                                <!-- Đoạn code thay thế cho link "Delete" -->
                                <button type="button" class="btn btn-danger del-btn" onclick="deleteProduct(<?php echo $key; ?>)">Delete</button>
                                <br><br>
                                <button type="button" class="btn btn-primary edit-btn" onclick="openEditForm(<?php echo $key; ?>)">Edit</button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Product</h2>
                <form id="editForm" method="post" action="home.php" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="edit_id">
                    <input type="file" id="editImage" name="edit_image" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" src="" alt="Preview" style="display: none; width: 150px;">
                    <input type="text" id="editName" name="edit_name" placeholder="Tên sản phẩm" required>
                    <input type="text" id="editPrice" name="edit_price" placeholder="Giá sản phẩm" required>
                    <button type="submit" name="edit_product">Confirm</button>
                </form>
            </div>
        </div>

    </div>




</body>
<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        // Kiểm tra độ dài của chuỗi số
        var price = document.getElementsByName('product_price')[0].value;
        if (price.length >= 10) {
            return false;
        }
        return true;
    }
    // Function to handle deletion of a product
    function deleteProduct(id) {
        if (confirm("Delete this product?")) {
            window.location.href = 'home.php?delete_id=' + id;
        }
    }
    // Function to preview selected image in Add Product section
    function previewAddProductImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var img = document.getElementById('addProductPreview');
            img.src = reader.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    // Function to preview selected image
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var img = document.getElementById('preview');
            img.src = reader.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Mở modal khi nhấn nút sửa
    function openEditForm(id) {
        // Lấy thông tin sản phẩm để sửa
        var product = <?php echo json_encode($products); ?>[id];

        // Đổ dữ liệu sản phẩm cần sửa vào modal
        document.getElementById("editId").value = id;
        document.getElementById("editName").value = product.name;
        document.getElementById("editPrice").value = product.price;

        // Hiển thị modal
        document.getElementById("editModal").style.display = "block";
    }

    // Đóng modal khi nhấn nút đóng (close)
    document.getElementsByClassName("close")[0].onclick = function() {
        document.getElementById("editModal").style.display = "none";
    }
</script>

</html>