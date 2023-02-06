<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', '../home/images/');
//define('UPLOAD_PATH', 'upload/');
//define('DB_HOST', 'localhost');
//define('DB_USER', 'root');
//define('DB_PASS', '');
//define('DB_NAME', 'wooble');
$url = 'https://' . 'app.wooble.org/home/images/';
//$url = 'http://172.168.0.182/wooble_api/upload/';
//$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'updatefullprofile':
            if (isset($_FILES['pic']['name']) || isset($_POST['username'])) {
                $profileEmail = validate($_POST['profileEmail']);
                $fullname = validate($_POST['fullname']);
                $profile_pic = $_FILES['pic']['name'];
                $username = validate($_POST['username']);
                $designation = validate($_POST['designation']);
                $fb_link = validate($_POST['fb_link']);
                $insta_link = validate($_POST['insta_link']);
                $linkedin_link = validate($_POST['linkedin_link']);
                $twitter_link = validate($_POST['twitter_link']);
                $whatsapp_link = validate($_POST['whatsapp_link']);
                $background = validate($_POST['background']);

                $cover_pic_name = "";

                try {

                    $stmt1 = $conn->prepare("SELECT `compressed_image`FROM `user_details` WHERE email=?");
                    $stmt1->bind_param("s", $_POST['profileEmail']);
                    $stmt1->execute();
                    $stmt1->bind_result($cover_pic_name_select);
                    while ($stmt1->fetch()) {
                        $cover_pic_name = $cover_pic_name_select;
                    }

                    if ($cover_pic_name != null) {
                        unlink(UPLOAD_PATH . $cover_pic_name);
                    }

                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    $stmt = $conn->prepare("UPDATE user_details SET field_1=?,compressed_image=?,username=?,field_2=?,fb_link=?,insta_link=?,linkedin_link=?,twitter_link=?,whatsapp_link=?,field_3=? WHERE email=? ");
                    $stmt->bind_param("sssssssssss", $fullname, $profile_pic, $username, $designation, $fb_link, $insta_link, $linkedin_link, $twitter_link, $whatsapp_link, $background, $profileEmail);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Portfolio Updated successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                    echo json_encode($response);
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }

            break;
        case 'getfullprofile':
            $server_ip = gethostbyname(gethostname());
            $profileEmail = validate($_POST['profileEmail']);
            $stmt = $conn->prepare("SELECT field_1, compressed_image, username, field_2, fb_link, insta_link, linkedin_link, twitter_link, whatsapp_link, field_3 FROM user_details WHERE email=?");
            $stmt->bind_param("s", $profileEmail);
            $stmt->execute();
            $stmt->bind_result($fullname, $compressed_image, $username, $designation, $fb_link, $insta_link, $linkedin_link, $twitter_link, $whatsapp_link, $background);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['fullname'] = $fullname;
                $temp['profile_pic'] = $url . $compressed_image;
                $temp['username'] = $username;
                $temp['designation'] = $designation;
                $temp['fb_link'] = $fb_link;
                $temp['insta_link'] = $insta_link;
                $temp['linkedin_link'] = $linkedin_link;
                $temp['twitter_link'] = $twitter_link;
                $temp['whatsapp_link'] = $whatsapp_link;
                $temp['background'] = $background;

                array_push($images, $temp);
                header('Content-Type: application/json');
                echo json_encode($images);
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
