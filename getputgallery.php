<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', '../gallery/upload/');
$response = array();
date_default_timezone_set("Asia/Calcutta");
$date = date('Y-m-d H:i:s a', time());
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'creategallery':
            $profileEmail = $_POST['profileEmail'];
            $gallery_pic = $_FILES['pic']['name'];
            if (isset($_POST['profileEmail'])) {
                try {
                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    $stmt = $conn->prepare("INSERT INTO `gallery_db`( `email_id`, `file_name`, `thumbnail`, `file_content`, `title`, `description`, `file_created`) VALUES (?,?,?,?,?,?,?)");
                    $stmt->bind_param("sssssss", $_POST['profileEmail'], $gallery_pic, $gallery_pic, $gallery_pic, $_POST['title'], $_POST['description'], $date);
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
                    $stmt = $conn->prepare("UPDATE user_info SET full_name=?,mobile=?,email=? WHERE email=? ");
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
        case 'getgallery':
            $server_ip = gethostbyname(gethostname());
            $stmt = $conn->prepare("SELECT full_name, mobile, email FROM user_info WHERE email=?");
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
echo json_encode($response);
?>