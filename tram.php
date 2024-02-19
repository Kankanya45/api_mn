<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$id_car = mysqli_real_escape_string($conn, $_POST['id_car']);
$numbers_car = mysqli_real_escape_string($conn, $_POST['numbers_car']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum id_car from the database
        $sqlMaxId = "SELECT MAX(id_car) AS MAX_ID FROM tram_data";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value

        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Increment the maxId for the new record
        $id_car = $maxId;

        $sql = "INSERT INTO tram_data (id_car, numbers_car)
                VALUES (?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $id_car, $numbers_car);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert bus data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $sql = "UPDATE tram_data
                SET numbers_car=?
                WHERE id_car=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $numbers_car, $id_car);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update bus data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $sql = "DELETE FROM tram_data WHERE id_car=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $id_car);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete bus data: " . mysqli_error($conn);
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
