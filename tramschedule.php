<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$place_code = mysqli_real_escape_string($conn, $_POST['place_code']);
$time = mysqli_real_escape_string($conn, $_POST['time']); // Assuming $_POST['time'] is a string in 'HH:MM:SS' format

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum numbers
        $sqlMaxNo = "SELECT MAX(numbers) AS MAX_NO FROM tramschedule";
        $resultMaxNo = mysqli_query($conn, $sqlMaxNo) or die(mysqli_error($conn));
        $maxNo = 1; // Default value
        while ($rowMaxNo = mysqli_fetch_array($resultMaxNo)) {
            if ($rowMaxNo["MAX_NO"] != "") {
                $maxNo = $rowMaxNo["MAX_NO"] + 1;
            }
        }

        $sqlInsert = "INSERT INTO tramschedule (numbers, place_code, time) VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'dss', $maxNo, $place_code, $time);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 201; // Created
            $response['message'] = "Bus route data inserted successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to insert bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $numbers = mysqli_real_escape_string($conn, $_POST['numbers']);

        $sqlUpdate = "UPDATE tramschedule SET place_code=?, time=? WHERE numbers=?";

        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'dss', $place_code, $time, $numbers);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200; // OK
            $response['message'] = "Bus route data updated successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to update bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $numbers = mysqli_real_escape_string($conn, $_POST['numbers']);

        $sqlDelete = "DELETE FROM tramschedule WHERE numbers=?";

        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 'd', $numbers);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200; // OK
            $response['message'] = "Bus route data deleted successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to delete bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    default:
        $response['status'] = 400; // Bad Request
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
