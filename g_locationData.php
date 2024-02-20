<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

// Query to get place_code and location_name from locationtravel
$sql = "SELECT place_code, location_name FROM locationtravel";

$result = mysqli_query($conn, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = array(
            'place_code' => $row['place_code'],
            'location_name' => $row['location_name'],
        );
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
