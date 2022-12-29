<?php
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Include the necessary files
    require_once "conn.php";
    require_once "validate.php";
    // Call validate, pass form data as parameter and store the returned value
    $name = validate($_POST['name']);
    $mobile = $_POST['mobile'];
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    date_default_timezone_set("Asia/Calcutta");
    $date = date('Y-m-d H:i:s a', time());
    // Create the SQL query string. We'll use md5() function for data security. It calculates and returns the MD5 hash of a string
    //$sql = " insert into 'user_info' values('','$name', '$mobile','$email', '". md5($password) ."' , '', '','', '', '', '', '', '', '', '', '', '', '', '') ";
    //$sql = "insert into user_info values('','$name', '$mobile','$email', '" . md5($password) . "', '',DEFAULT,'', '', '', '', '', '', '', '', '', '', '$date', '',DEFAULT)";

    $sql = "insert into user_details (email, password, phone, field_1, reg_time)
    VALUES ('$email', '" . md5($password) . "', '$mobile', '$name', '$date')";

    // Execute the query. Print "success" on a successful execution, otherwise "failure".
    $check_sql = " select * from user_details where email='$email' ";
    $result = $conn->query($check_sql);
    if ($result->num_rows > 0) {
        echo "exist";

    } else {
        if (!$conn->query($sql)) {
            echo "failure";
        } else {
            echo "success";
        }
    }

}
?>

