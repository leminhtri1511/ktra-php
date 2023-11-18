 <?php
    session_start();

    $productsFile = 'products.txt';
    $valid_username = 'letri';
    $valid_password = '123';

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

        if ($lines !== false) {
            unset($lines[$deleteId]);
            file_put_contents($productsFile, implode("", $lines));
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