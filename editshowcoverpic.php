<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', 'home/images/');
//define('UPLOAD_PATH', 'upload/');
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'wooble');
$url = 'https://' . 'wooble.org/app/home/images/';
//$url = 'http://172.168.0.182/wooble_api/upload/';
//$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
$response = array();
$profileEmail = $_POST['profileEmail'];
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'updatecoverpic':
            if (isset($_FILES['pic']['name']) && isset($_POST['profileEmail'])) {
                try {
                    $cover_pic_name = "";
                    $stmt1 = $conn->prepare("SELECT `cover_pic`FROM `user_details` WHERE email=?");
                    $stmt1->bind_param("s", $_POST['profileEmail']);
                    $stmt1->execute();
                    $stmt1->bind_result($cover_pic_name_select);
                    while ($stmt1->fetch()) {
                        $cover_pic_name = $cover_pic_name_select;
                    }

                    if ($cover_pic_name != null) {
                        unlink(UPLOAD_PATH . $cover_pic_name);
                    }
                    $cover_pic_name = $_FILES['pic']['name'];
                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    $stmt = $conn->prepare("UPDATE `user_details` SET `cover_pic`=? WHERE `email`=? ");
                    $stmt->bind_param("ss", $cover_pic_name, $_POST['profileEmail']);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'File uploaded successfully';
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
            echo json_encode($response);

            break;
        case 'getcoverpic':
            if (isset($_POST['profileEmail'])) {
                try {
                    $stmt = $conn->prepare("SELECT `cover_pic` FROM `user_details` WHERE `email`=? ");
                    $stmt->bind_param("s", $_POST['profileEmail']);
                    $stmt->execute();
                    $stmt->bind_result($image);
                    $imagelink = $image;
                    $images = array();
                    while ($stmt->fetch()) {
                        $temp = array();
                        $temp['image'] = $url . $image;
                        array_push($images, $temp);
                    }
                    $response['error'] = false;
                    $response['images'] = $images;

                    header('Content-Type: application/json');
                    echo json_encode($images);

                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }

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
//echo json_encode($images);
