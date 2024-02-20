<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

// Query to get id_store and name_store from product_info
$sql = "SELECT id_store, name_store FROM storename";

$result = mysqli_query($conn, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = array(
            'id_store' => (string) $row['id_store'],
            'name_store' => $row['name_store'],
        );
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
