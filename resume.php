<?php
require_once "conn.php";
require_once "validate.php";
define('UPLOAD_PATH', '../home/pdf_files/');
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'wooble');
// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
// define('UPLOAD_PATH', 'upload/');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'insertresumedata':
            if (isset($_POST['title']) || $_POST['resume'] || $_POST['email_id']) {
                try {
                    $resume_name ="";
                    $user_email = null;
                    $email = validate($_POST['email_id']);
                    $stmt = $conn->prepare("SELECT email_id FROM `resume_db` WHERE `email_id`=? ");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->bind_result($email_id);
                    $images = array();
                    while ($stmt->fetch()) {
                        $user_email = $email_id;
                    }
                    if ($user_email == $_POST['email_id']) {
                        $resume = $_POST['resume'];
                        $name = $_POST['title'];
                        $stmt1 = $conn->prepare("SELECT `resume`FROM `resume_db` WHERE email_id=?");
                        $stmt1->bind_param("s",$_POST['email_id']);
                        $stmt1->execute();
                        $stmt1->bind_result($resume_name_select);
                        while($stmt1->fetch()){
                            $resume_name = $resume_name_select;
                        }
                        unlink(UPLOAD_PATH.$resume_name);
                        date_default_timezone_set("Asia/Calcutta");
                        $file_created = date("Y-m-d");
                        $decoderesume = base64_decode("$resume");
                        $return = file_put_contents(UPLOAD_PATH . $name . ".pdf", $decoderesume);
                        $resume_name = $_POST['title'] . ".pdf";
                        $stmt = $conn->prepare("UPDATE `resume_db` SET resume=?,title=?,created_date=? WHERE email_id=?");
                        $stmt->bind_param("ssss", $resume_name, $_POST['title'], $file_created,$_POST['email_id']);
                        $stmt->execute();

                        if ($return !== false) {
                            $response['error'] = false;
                            $response['message'] = 'Resume Updated successfully';
                        } else {
                            throw new Exception("Could not upload file");
                        }
                        echo json_encode($response);
                    } else {
                        $resume = $_POST['resume'];
                        $name = $_POST['title'];

                        date_default_timezone_set("Asia/Calcutta");
                        $file_created = date("Y-m-d");

                        $decoderesume = base64_decode("$resume");
                        $return = file_put_contents(UPLOAD_PATH . $name . ".pdf", $decoderesume);

                        $resume_name = $_POST['title'] . ".pdf";

                        $stmt = $conn->prepare("INSERT INTO `resume_db`(`email_id`,`resume`,`title`,`created_date`) VALUES (?,?,?,?)");
                        $stmt->bind_param("ssss", $_POST['email_id'], $resume_name, $_POST['title'], $file_created);
                        $stmt->execute();

                        if ($return !== false) {
                            $response['error'] = false;
                            $response['message'] = 'Resume Inserted successfully';
                        } else {
                            throw new Exception("Could not upload file");
                        }
                        echo json_encode($response);
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
        case 'getresumedata':
            $server_ip = gethostbyname(gethostname());
            $profileEmail = validate($_POST['email_id']);
            $stmt = $conn->prepare("SELECT content FROM resume WHERE email=?");
            $stmt->bind_param("s", $_POST['email_id']);
            $stmt->execute();
            $stmt->bind_result($resume);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['title'] = $_POST['email_id'];
                $temp['resume'] = 'https://app.wooble.org/home/pdf_files/' . $resume;
                array_push($images, $temp);
            }
            $response['error'] = false;
            $response['images'] = $images;
            header('Content-Type: application/json');
            echo json_encode($images);
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