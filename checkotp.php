<?php
if (isset($_POST['email']) && isset($_POST['otp'])) {
    require_once "conn.php";
    require_once "validate.php";
    $email = validate($_POST['email']);
    $otp = validate($_POST['otp']);

    $stmt = $conn->prepare("select id from user_info where email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id_id);

    $id = null;
    while ($stmt->fetch()) {
        $id = $id_id;
    }
    $sql = " select id,otp from otp_db where id='$id' AND otp='$otp' AND is_expired='0'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sql = " UPDATE `otp_db` SET `is_expired`='1'WHERE id='$id' AND otp='$otp'";
        $result = $conn->query($sql);
        $sql = " DELETE FROM `otp_db` WHERE id='$id'";
        $result = $conn->query($sql);

        echo "success";
    } else {
        echo "failure";
    }
}
