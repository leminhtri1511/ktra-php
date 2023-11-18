 <?php
    session_start();

    $productsFile = 'products.txt';
    $valid_username = 'letri';
    $valid_password = '123';
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

    function loginUser($username, $password)
    {
        global $valid_username, $valid_password;
        if ($username === $valid_username && $password === $valid_password) {
            $_SESSION['username'] = $username;
            return true;
        } else {
            return false;
        }
    }

    function logoutUser()
    {
        session_unset();
        session_destroy();
    }

    function getProducts()
    {
        global $productsFile;
        $products = [];

        if (file_exists($productsFile)) {
            $lines = file($productsFile, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $product = explode("|", $line);
                $products[] = [
                    'image' => $product[0],
                    'name' => $product[1],
                    'price' => $product[2],
                    // Assuming the image path is at index 2 in the line
                ];
            }
        }

        return $products;
    }


    function addProduct($productImage, $productName, $productPrice,)
    {
        global $productsFile;
        $productData =  $productImage . "|" . $productName . "|" . $productPrice . PHP_EOL;
        file_put_contents($productsFile, $productData, FILE_APPEND);
    }

    function deleteProduct($deleteId)
    {
        global $productsFile;
        $lines = file($productsFile);

        if ($lines !== false && isset($lines[$deleteId])) {
            // Lấy thông tin về sản phẩm để lấy đường dẫn ảnh
            $productInfo = explode("|", $lines[$deleteId]);
            $imagePath = trim($productInfo[0]); // Đường dẫn ảnh đầu tiên trong dữ liệu sản phẩm

            // Xóa sản phẩm từ danh sách
            unset($lines[$deleteId]);
            file_put_contents($productsFile, implode("", $lines));

            // Xóa ảnh từ thư mục uploads
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }


    function editProduct($editId, $editedName, $editedPrice)
    {
        global $productsFile;
        $lines = file($productsFile);

        if ($lines !== false && isset($lines[$editId])) {
            $productInfo = explode("|", $lines[$editId]);

            // Chỉnh sửa tên và giá của sản phẩm
            $productInfo[1] = $editedName;
            $productInfo[2] = $editedPrice;

            // Ghi lại thông tin vào tệp
            $lines[$editId] = implode("|", $productInfo) . PHP_EOL;
            file_put_contents($productsFile, implode("", $lines));
        }
    }

    function editProductWithImage($editId, $editedName, $editedPrice, $editedImage)
    {
        global $productsFile;
        $lines = file($productsFile);

        if ($lines !== false && isset($lines[$editId])) {
            // Cập nhật thông tin sản phẩm với hình ảnh mới
            $lines[$editId] = $editedImage . " | " . $editedName . " | " . $editedPrice . PHP_EOL;

            // Ghi lại thông tin vào tệp
            file_put_contents($productsFile, implode("", $lines));
        }
    }

    ?> 