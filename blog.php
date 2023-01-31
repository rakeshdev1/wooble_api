<?php

//require_once "conn.php";

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
require_once "validate.php";
//define('UPLOAD_PATH', '../img/blog_assets/');
$server_ip = gethostbyname(gethostname());
define('UPLOAD_PATH', 'blog_assets/');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'startblog':
            if (isset($_POST['email_id'])) {
                try {
                    date_default_timezone_set("Asia/Calcutta");
                    $file_created = date("Y-m-d H:i:s a");
                    $blog_id = strval(time());
                    $email = validate($_POST['email_id']);
                    $stmt = $conn->prepare("INSERT INTO `blogs`(`blog_id`,`email_id`,`time_created`) VALUES (?,?,?)");
                    $stmt->bind_param("sss",$blog_id,$email,$file_created);
                    $result=$stmt->execute();
                    $stmt2 = $conn->prepare("SELECT blog_id FROM `blogs` WHERE `email_id`=?");
                    $stmt2->bind_param("s", $email);
                    $result = $stmt2->execute();
                    $stmt2->bind_result($file_id);
                    $images = array();
                    while ($stmt2->fetch()) {
                        $new_file_id=strval($file_id);
                    }
                    if ($result == true) {
                        $response['error'] = false;
                        $response['message'] = $new_file_id;
                    } else {
                        throw new Exception("Could not create blog");
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
        
        case 'uploadblogimage':
            if (isset($_POST['email_id']) && isset($_POST['blog_id']) && isset($_POST['image'] )) {
                try {
                    $blog_id = $_POST['blog_id'];
                    $email = validate($_POST['email_id']);
                    $blog_id = $_POST['blog_id'];
                    $image = $_POST['image'];
                    if (base64_decode($image) != "NULL") {
                        $image_name = rand().'-'.$blog_id;
                        $decode_image = base64_decode("$image");
                        $result = file_put_contents(UPLOAD_PATH . $image_name . ".WEBP", $decode_image);
                    }
                    if ($result == true) {
                        $response['error'] = false;
                        $response['message'] = $image_name.".WEBP";
                    } else {
                        throw new Exception("Could not create blog");
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

        case 'uploadblogvideo':
            if (isset($_POST['email_id']) && isset($_POST['blog_id']) && isset($_POST['video'] )) {
                try {
                    $blog_id = $_POST['blog_id'];
                    $email = validate($_POST['email_id']);
                    $blog_id = $_POST['blog_id'];
                    $video = validate($_POST['video']);
                    if (base64_decode($video) != "NULL") {
                        $video_name = rand().'-'.$blog_id;
                        $decode_video = base64_decode("$video");
                        $result = file_put_contents(UPLOAD_PATH . $video_name . ".MP4", $decode_video);
                    }
                    if ($result == true) {
                        $response['error'] = false;
                        $response['message'] = $video_name.".MP4";
                    } else {
                        throw new Exception("Could not create blog");
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


        case 'uploadblogaudio':
            if (isset($_POST['email_id']) && isset($_POST['blog_id']) && isset($_POST['audio'] )) {
                try {
                    $blog_id = $_POST['blog_id'];
                    $email = validate($_POST['email_id']);
                    $blog_id = $_POST['blog_id'];
                    $audio = validate($_POST['audio']);
                    if (base64_decode($audio) != "NULL") {
                        $audio_name = rand().'-'.$blog_id;
                        $decode_audio = base64_decode("$audio");
                        $result = file_put_contents(UPLOAD_PATH . $audio_name . ".MP3", $decode_audio);
                    }
                    if ($result == true) {
                        $response['error'] = false;
                        $response['message'] = $audio_name.".MP3";
                    } else {
                        throw new Exception("Could not create blog");
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


        case 'publishblog':
            if (isset($_POST['title']) || isset($_POST['content'])) {
                try {
                    date_default_timezone_set("Asia/Calcutta");
                    $file_created = date("Y-m-d H:i:s a");
                    $blog_status = 1;
                    $stmt = $conn->prepare("UPDATE `blogs` SET `title`=?,`last_updated`=?,`content`=?,`blog_status`=? WHERE `blog_id`=? AND `email_id`=?");
                    $stmt->bind_param("ssssss", $_POST['title'], $file_created, $_POST['content'], $blog_status,$_POST['blog_id'],$_POST['email_id']);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Blog Published';
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

        case 'publishdraft':
            if (isset($_POST['title']) || isset($_POST['content'])) {
                try {
                    date_default_timezone_set("Asia/Calcutta");
                    $file_created = date("Y-m-d H:i:s a");
                    $stmt = $conn->prepare("UPDATE `blogs` SET `title`=?,`last_updated`=?,`content`=? WHERE `blog_id`=? AND `email_id`=?");
                    $stmt->bind_param("sssss", $_POST['title'], $file_created, $_POST['content'],$_POST['blog_id'],$_POST['email_id']);
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Blog Saved as Draft';
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


        case 'deleterow':
            if (isset($_POST['email_id']) && isset($_POST['blog_id'])) {
                try {
                    $blog_id = $_POST['blog_id'];
                    $email = validate($_POST['email_id']);
                    $blog_id = $_POST['blog_id'];
                    $stmt = $conn->prepare("DELETE FROM `blogs` WHERE email_id=? AND blog_id=?");
                    $stmt->bind_param("ss",$email,$blog_id);
                    $result=$stmt->execute();
                
                    if ($result == true) {
                        $response['error'] = false;
                        $response['message'] = "Blog Deleted Successfully";
                    } else {
                        throw new Exception("Could not create blog");
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

        case 'insertblogdata':
            if (isset($_POST['title']) || isset($_POST['content'])) {
                try {
                    date_default_timezone_set("Asia/Calcutta");
                    $file_created = date("Y-m-d");
                    $stmt = $conn->prepare("INSERT INTO `blog_db`(`email_id`,`title`,`content`,`created_date`) VALUES (?,?,?,?)");
                    $stmt->bind_param("ssss", $_POST['email_id'], $_POST['title'], $_POST['content'], $file_created);
                    $stmt->execute();

                    $email = validate($_POST['email_id']);
                    $title = validate($_POST['title']);
                    $content = validate($_POST['content']);

                    $stmt2 = $conn->prepare("SELECT file_id FROM `blog_db` WHERE `email_id`=? AND `title`=? AND `content`=?");
                    $stmt2->bind_param("sss", $email, $title, $content);
                    $stmt2->execute();
                    $stmt2->bind_result($file_id);
                    $images = array();

                    while ($stmt2->fetch()) {
                        $temp = array();
                        $temp['file_id'] = $file_id;
                        array_push($images, $temp);
                    }

                    $fake_name = 1;
                    $image = $_POST['pic'];
                    if (is_array($image)) {
                        foreach ($image as $key => $value) {
                            $decodeimage = base64_decode("$value");
                            $image_name = $email . "_" . $file_id . "_" . $fake_name . ".jpg";
                            $return = file_put_contents(UPLOAD_PATH . $image_name, $decodeimage);
                            $stmt1 = $conn->prepare("INSERT INTO `blog_image`(`file_id`,`image`) VALUES (?,?)");
                            $stmt1->bind_param("is", $file_id, $image_name);
                            $stmt1->execute();
                            $fake_name = $fake_name + 1;
                        }
                    }

                    if ($return !== false) {
                        $response['error'] = false;
                        $response['message'] = 'Blog Published';
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

        case 'getblogdata':
            $stmt = $conn->prepare("SELECT blog_id, title, content, last_updated FROM blogs WHERE email_id=?");
            $stmt->bind_param("s", $_POST['email_id']);
            $stmt->execute();
            $stmt->bind_result($blog_id, $title, $content, $last_updated);

            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['blog_id'] = $blog_id;
                //$temp['full_name'] = $full_name;
                $temp['title'] = $title;
                $temp['content'] = $content;
                $temp['last_updated'] = $last_updated;
                array_push($images, $temp);
            }

            $response['error'] = false;
            $response['images'] = $images;
            header('Content-Type: application/json');
            echo json_encode($images);
            break;

        case 'getblogimages':

            $server_ip = gethostbyname(gethostname());


            $stmt1 = $conn->prepare("SELECT `image` FROM `blog_image` WHERE `file_id`=?");
            $stmt1->bind_param("s", $_POST['file_id']);
            $stmt1->execute();
            $stmt1->bind_result($pictures);

            $images = array();


            while ($stmt1->fetch()) {
                $temp = array();
                $temp['image'] = 'http://' . $server_ip . '/wooble-api/' . UPLOAD_PATH . $pictures;
                array_push($images, $temp);
            }

            $response['error'] = false;
            $response['images'] = $images;
            header('Content-Type: application/json');
            echo json_encode($images);
            break;

        case 'deleteblogdata':
            if (isset($_POST['file_id'])) {

                try {
                    $stmt1 = $conn->prepare("DELETE FROM `blog_db` WHERE `file_id`=?");
                    $stmt1->bind_param("s", $_POST['file_id']);
                    $stmt2 = $conn->prepare("DELETE FROM `blog_image` WHERE `file_id`=?");
                    $stmt2->bind_param("s", $_POST['file_id']);

                    if ($stmt1->execute() && $stmt2->execute()) {

                        $response['error'] = false;
                        $response['message'] = 'Blog deleted successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not delete file';
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }
            header('Content-Type: application/json');
            echo json_encode($response);

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
?>