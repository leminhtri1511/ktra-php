<?php
require_once 'controller.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        header('Location: home.php');
        exit();
    } else {
        echo "Sai tên đăng nhập hoặc mật khẩu!";
    }
}

if (isset($_POST['logout'])) {
    logoutUser();
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    deleteProduct($deleteId);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Trong file home.php
if (isset($_POST['edit_product'])) {
    $editId = $_POST['edit_id'];
    $editedName = $_POST['edit_name'];
    $editedPrice = $_POST['edit_price'];

    // Kiểm tra xem người dùng đã chọn hình ảnh mới hay không
    if ($_FILES['edit_image']['size'] > 0) {
        $targetDir = "uploads/"; // Thư mục lưu trữ ảnh tải lên
        $targetFile = $targetDir . basename($_FILES["edit_image"]["name"]);

        // Xử lý upload hình ảnh mới
        if (move_uploaded_file($_FILES["edit_image"]["tmp_name"], $targetFile)) {
            // Nếu upload thành công, cập nhật đường dẫn hình ảnh mới trong cơ sở dữ liệu
            $editedImage = $targetFile;
            // Cập nhật thông tin sản phẩm với hình ảnh mới
            editProductWithImage($editId, $editedName, $editedPrice, $editedImage);
        } else {
            echo "Đã xảy ra lỗi khi tải lên hình ảnh mới.";
        }
    } else {
        // Nếu không có hình ảnh mới được chọn, chỉ cập nhật tên và giá sản phẩm
        editProduct($editId, $editedName, $editedPrice);
    }

    // Sau khi cập nhật, chuyển hướng về trang chủ hoặc trang danh sách sản phẩm
    header("Location: home.php");
    exit();
}




if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Xử lý file ảnh
    $targetDir = "uploads/"; // Thư mục lưu trữ ảnh tải lên
    $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Kiểm tra nếu file là file hình ảnh thực sự
    if (isset($_POST["add_product"])) {
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }

    // Kiểm tra kích thước file
    if ($_FILES["product_image"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Cho phép tải lên nếu mọi kiểm tra đều đã qua
    if ($uploadOk == 0) {
        echo "File của bạn không được tải lên.";
    } else {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            // Nếu tệp được tải lên thành công, thêm thông tin sản phẩm vào file
            addProduct($targetFile, $productName, $productPrice);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Đã xảy ra lỗi khi tải lên file của bạn.";
        }
    }
}

$products = getProducts();
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
        <div class="add_product">
            <h2 class="add_product_text">Add Product</h2>
            <form method="post" action="home.php" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Tên sản phẩm" required>
                <input type="text" name="product_price" placeholder="Giá sản phẩm" required>
                <input type="file" name="product_image" accept="image/*" onchange="previewAddProductImage(event)">
                <img id="addProductPreview" src="" alt="Preview" style="display: none; width: 150px;">
                <button type="submit" name="add_product" class="btn btn-success">Submit</button>
            </form>
        </div>
        <div class="product_lits">
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
                                <button type="button" class="btn btn-danger" onclick="deleteProduct(<?php echo $key; ?>)">Delete</button>
                                <br><br>
                                <button type="button" class="btn btn-primary" onclick="openEditForm(<?php echo $key; ?>)">Edit</button>

                            </td>
                            <!-- <td>
                                <a href="?delete_id=<?php echo $key; ?>" class="delete_btn">Delete</a>

                                <br><br>
                                <a href="#" class="edit_btn" onclick="openEditForm(<?php echo $key; ?>)">Edit</a>
                            </td> -->

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
    // Function to handle deletion of a product
    function deleteProduct(id) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
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