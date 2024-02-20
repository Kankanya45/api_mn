<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
$latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
$longitude = mysqli_real_escape_string($conn, $_POST['longitude']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum place_code
        $sqlMaxId = "SELECT MAX(place_code) AS MAX_ID FROM locationtravel";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Insert with the manually counted place_code
        $sqlInsert = "INSERT INTO locationtravel (place_code, location_name, latitude, longitude)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'dssd', $maxId, $location_name, $latitude, $longitude);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert location data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $place_code = mysqli_real_escape_string($conn, $_POST['place_code']);

        $sqlUpdate = "UPDATE locationtravel
                SET location_name=?, latitude=?, longitude=?
                WHERE place_code=?";

        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'sdds', $location_name, $latitude, $longitude, $place_code);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update location data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $place_code = mysqli_real_escape_string($conn, $_POST['place_code']);

        $sqlDelete = "DELETE FROM locationtravel WHERE place_code=?";

        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 's', $place_code);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete location data: " . mysqli_error($conn);
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
