<?php
if (isset($_POST['email']) && isset($_POST['password'])) {
    require_once "conn.php";
    require_once "validate.php";
    $name = validate($_POST['name']);
    $mobile = $_POST['mobile'];
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    date_default_timezone_set("Asia/Calcutta");
    $date = date('Y-m-d H:i:s a', time());
    $sql = "insert into user_details (email, password, phone, field_1, reg_time)
    VALUES ('$email', '$password', '$mobile', '$name', '$date')";
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

