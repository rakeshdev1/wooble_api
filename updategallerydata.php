<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', 'gallery_pic/');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'updategallerydata':
            if (isset($_FILES['pic']['name']) || isset($_POST['title']) || isset($_POST['description'])) {
                try {
                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    $stmt = $conn->prepare("UPDATE gallery_db SET file_name=?,thumbnail=?,file_content=?,title=?,description=? WHERE file_id=?");
                    $stmt->bind_param("ssssss", $_FILES['pic']['name'],$_FILES['pic']['name'],$_FILES['pic']['name'],$_POST['title'], $_POST['description'],$_POST['file_id']);
                    if ($stmt->execute()) {
                        unlink(UPLOAD_PATH.$_POST['image_path']);
                        $response['error'] = false;
                        $response['message'] = 'Updated successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }

            }else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }
            break;
        case 'getprofile':
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