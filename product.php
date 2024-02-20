<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$id_store = mysqli_real_escape_string($conn, $_POST['id_store']);
$id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
$product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
$count = mysqli_real_escape_string($conn, $_POST['count']);
$price = mysqli_real_escape_string($conn, $_POST['price']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum id_product
        $sqlMaxId = "SELECT MAX(id_product) AS MAX_ID FROM product_data";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Insert with the manually counted id_product
        $sqlInsert = "INSERT INTO product_data (id_store, id_product, product_name, count, price)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'ssssd', $id_store, $maxId, $product_name, $count, $price);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert product data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $sqlUpdate = "UPDATE product_data
            SET id_store=?, product_name=?, count=?, price=?
            WHERE id_product=?";

        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'dssds', $id_store, $product_name, $count, $price, $id_product);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update product data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $sqlDelete = "DELETE FROM product_data WHERE id_product=?";

        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 'd', $id_product);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete product data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    default:
        $response['status'] = 400;
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
