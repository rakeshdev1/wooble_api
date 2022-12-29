<?php
if (isset($_POST['email']) && isset($_POST['password'])) {
    require_once "conn.php";
    require_once "validate.php";
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $sql = " UPDATE `user_info` SET `password`='$password' WHERE email='$email' ";
    $result = $conn->query($sql);

    if($result==true){

        echo "success";
    } else {
        echo "failure";
    }
}
?>
