<?php
//require_once "conn.php";
require_once "validate.php";
//define('UPLOAD_PATH', '../gallery/upload/');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
//$url = 'https://' . 'app.wooble.org/gallery/upload/';
$url = 'http://172.168.6.55/wooble_api/upload/';
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'insertgallerypic':
            if (isset($_FILES['pic']['name'])) {
                $return_id;
                try {
                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    $stmt1 = $conn->prepare("INSERT INTO `gallery_db`(`file_name`,`thumbnail`,`file_content`) VALUES (?,?,?)");
                    $stmt1->bind_param("sss", $_FILES['pic']['name'], $_FILES['pic']['name'], $_FILES['pic']['name']);

                    $stmt2 = $conn->prepare("SELECT file_id FROM gallery_db WHERE file_content=?");
                    $stmt2->bind_param("s", $_FILES['pic']['name']);
                    if ($stmt1->execute()) {
                        if ($stmt2->execute()) {
                            $stmt2->bind_result($return_id);

                            $images = array();
                            while ($stmt2->fetch()) {
                                $temp = array();
                                $temp['return_id'] = $return_id;
                                array_push($images, $temp);
                            }
                            $response['error'] = false;
                            $response['images'] = $images;
                            break;

                            $response['return_id'] = $return_id;
                            $response['error'] = false;
                            $response['message'] = 'File uploaded successfully';
                        }
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
            $stmt = $conn->prepare("SELECT file_id,file_name, title,description FROM gallery_db WHERE email_id='$profileEmail' ");
            $stmt->execute();
            $stmt->bind_result($id,$image, $title, $description);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['id']=$id;
                $temp['image'] = $url . $image;
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
echo json_encode($images);
?>