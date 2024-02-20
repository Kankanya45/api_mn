<?php
header('Access-Control-Allow-Origin: *');
include "../conn.php";

// Fetch the maximum user_id from the user table
$max_user_id_query = "SELECT MAX(user_id) AS max_user_id FROM user";
$max_user_id_result = mysqli_query($conn, $max_user_id_query);

if ($max_user_id_result) {
    $row = mysqli_fetch_assoc($max_user_id_result);
    $max_user_id = $row['max_user_id'];
    $user_id = $max_user_id + 1;
} else {
    // Error handling if fetching maximum user_id fails
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error fetching maximum user_id']);
    exit(); // Exit the script
}

$firstname = isset($_REQUEST['firstname']) ? $_REQUEST['firstname'] : '';
$lastname = isset($_REQUEST['lastname']) ? $_REQUEST['lastname'] : '';
$address = isset($_REQUEST['address']) ? $_REQUEST['address'] : '';
$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '';
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

// Insert data into the user table using parameterized query
$sql = "INSERT INTO user (user_id, firstname, lastname, address, phone, email, username, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

// Bind the parameters
mysqli_stmt_bind_param($stmt, 'isssssss', $user_id, $firstname, $lastname, $address, $phone, $email, $username, $password);

// Execute the statement
mysqli_stmt_execute($stmt);

// Check for success
if (mysqli_stmt_affected_rows($stmt) > 0) {
    // Successful insertion
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
} else {
    // Error in insertion
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error adding user']);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
