<?php
header('Access-Control-Allow-Origin: *');
include "conn.php";

// Check if the required fields are set in the request
if (
    isset($_REQUEST['firstname']) && isset($_REQUEST['lastname']) && isset($_REQUEST['address']) &&
    isset($_REQUEST['phone']) && isset($_REQUEST['email']) && isset($_REQUEST['username']) && isset($_REQUEST['password'])
) {
    // Receive values from the HTTP Request
    $firstname = $_REQUEST['firstname'];
    $lastname = $_REQUEST['lastname'];
    $address = $_REQUEST['address'];
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    // Calculate the latest ID
    $sql = "SELECT MAX(user_id) AS MAX_ID FROM user ";
    $objQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $no = 1; // Default value
    while ($row1 = mysqli_fetch_array($objQuery)) {
        if ($row1["MAX_ID"] != "") {
            $no = $row1["MAX_ID"] + 1;
        }
    }

    // Generate a new user_id
    $newno = "" . (string) $no;
    $newno = str_pad($newno, 5, '0', STR_PAD_LEFT);
    $newuserid = $newno;

    // Prepare SQL command and execute
    $sql = "INSERT INTO user(user_id, firstname, lastname, address, phone, email, username, password)
            VALUES ('$newuserid', '$firstname', '$lastname', '$address', '$phone', '$email', '$username', '$password')";

    $result = mysqli_query($conn, $sql);

    // Check if the query was successful or not
    if ($result) {
        http_response_code(200); // OK
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error: " . mysqli_error($conn);
    }

    // Close the connection
    mysqli_close($conn);

} else {
    // If the required fields are not set in the request
    http_response_code(400); // Bad Request
    echo "Error: Incomplete request data.";
}
