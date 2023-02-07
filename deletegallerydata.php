<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', '../gallery/upload/');
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'wooble');
// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
$url = 'https://' . 'app.wooble.org/home/images/';
//$url = 'http://172.168.0.182/wooble_api/upload/';
//define('UPLOAD_PATH', 'upload/');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'deletegallerydata':
            if (isset($_POST['file_id']) || isset($_POST['image_path'])) {
                try {
                    $cover_pic_name = "";
                    $stmt1 = $conn->prepare("SELECT `file_name`FROM `gallery_db` WHERE file_id=?");
                    $stmt1->bind_param("s", $_POST['file_id']);
                    $stmt1->execute();
                    $stmt1->bind_result($cover_pic_name_select);
                    while ($stmt1->fetch()) {
                        $cover_pic_name = $cover_pic_name_select;
                    }

                    if ($cover_pic_name != null) {
                        unlink(UPLOAD_PATH . $cover_pic_name);
                    }


                    $stmt1 = $conn->prepare("DELETE FROM `gallery_db` WHERE file_id=?");
                    $stmt1->bind_param("s", $_POST['file_id']);
                    if ($stmt1->execute()) {
                            $response['error'] = false;
                            $response['message'] = 'File deleted successfully';
                            break;
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
        case 'getgallerydata':
            $server_ip = gethostbyname(gethostname());
            $profileEmail = $_POST['profileEmail'];
            $stmt = $conn->prepare("SELECT file_content, title,description FROM gallery_db WHERE email_id='$profileEmail' ");
            $stmt->execute();
            $stmt->bind_result($image, $title, $description);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['image'] = 'http://' . $server_ip . '/wooble-api/' . UPLOAD_PATH . $image;
                $temp['title'] = $title;
                $temp['description'] = $description;
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