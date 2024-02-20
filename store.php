<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$id_store = mysqli_real_escape_string($conn, $_POST['id_store']);
$name_store = mysqli_real_escape_string($conn, $_POST['name_store']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum id_store
        $sqlMaxId = "SELECT MAX(id_store) AS MAX_ID FROM storename";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Insert new data with the next available id_store
        $sqlInsert = "INSERT INTO storename (id_store, name_store) VALUES (?, ?)";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmtInsert, 'ss', $maxId, $name_store);

        if (mysqli_stmt_execute($stmtInsert)) {
            $response['status'] = 200;
            $response['message'] = "Store data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtInsert);
        break;

    case '2': // update
        // Update data in storename
        $sqlUpdate = "UPDATE storename SET name_store=? WHERE id_store=?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'ss', $name_store, $id_store);

        if (mysqli_stmt_execute($stmtUpdate)) {
            $response['status'] = 200;
            $response['message'] = "Store data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtUpdate);
        break;

    case '3': // delete
        // Delete data from storename
        $sqlDelete = "DELETE FROM storename WHERE id_store=?";
        $stmtDelete = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmtDelete, 's', $id_store);

        if (mysqli_stmt_execute($stmtDelete)) {
            $response['status'] = 200;
            $response['message'] = "Store data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtDelete);
        break;

    default:
        $response['status'] = 400;
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
