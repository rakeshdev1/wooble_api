<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', 'profile_pic/');
$response = array();
$profileEmail = $_POST['profileEmail'];
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'updateprofile':
            if (isset($_POST['profileEmail']) && ($_POST['password'])) {
                $password = md5($_POST['password']);
                try {
                    $stmt = $conn->prepare("UPDATE user_details SET field_1=?,phone=?,email=?,password=? WHERE email=? ");
                    $stmt->bind_param("sssss", $_POST['fullname'], $_POST['mobile'], $_POST['email'], $password, $_POST['profileEmail']);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Profile saved successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }

            } elseif (isset($_POST['profileEmail'])) {

                try {
                    $stmt = $conn->prepare("UPDATE user_details SET field_1=?,phone=?,email=? WHERE email=? ");
                    $stmt->bind_param("ssss", $_POST['fullname'], $_POST['mobile'], $_POST['email'], $_POST['profileEmail']);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Profile saved successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }

            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }
            break;
        case 'getprofile':
            $server_ip = gethostbyname(gethostname());
            $stmt = $conn->prepare("SELECT field_1, phone, email FROM user_details WHERE email=?");
            $stmt->bind_param("s", $profileEmail);
            $stmt->execute();
            $stmt->bind_result($fullname, $mobile, $email);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['fullname'] = $fullname;
                $temp['mobile'] = $mobile;
                $temp['email'] = $email;
                array_push($images, $temp);
            }
            $response['error'] = false;
            $response['images'] = $images;
            break;

        default:
            $response['error'] = true;
            $response['message'] = 'Invalid api call';
    }

} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}

header('Content-Type: application/json');
echo json_encode($images);
