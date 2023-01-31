<?php
require_once "conn.php";
require_once "validate.php";
//define('DB_HOST', 'localhost');
//define('DB_USER', 'root');
//define('DB_PASS', '');
//define('DB_NAME', 'wooble');
//$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');
//define('UPLOAD_PATH', 'project_data/');
define('UPLOAD_PATH', '../works/upload/');
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'insertprojectdata':
            $server_ip = gethostbyname(gethostname());
            if (isset($_POST['email_id'])) {
                try {
                    $email_id = $_POST['email_id'];
                    $work_title = $_POST['work_title'];
                    if ($work_title == "") {
                        $work_title = NULL;
                    }
                    $aim_of_work = $_POST['aim_of_work'];
                    if ($aim_of_work == "") {
                        $aim_of_work = NULL;
                    }
                    $work_description = $_POST['work_description'];
                    if ($work_description == "") {
                        $work_description = NULL;
                    }
                    $image_1 = $_POST['image_1'];
                    $image_2 = $_POST['image_2'];
                    $image_3 = $_POST['image_3'];
                    $image_4 = $_POST['image_4'];
                    $image_5 = $_POST['image_5'];
                    $image_6 = $_POST['image_6'];
                    $video = $_POST['video'];
                    $project_pdf = $_POST['project_pdf'];
                    $conclusion = $_POST['conclusion'];
                    if ($conclusion == "") {
                        $conclusion = NULL;
                    }

                    $max_entry_id = 0;
                    $stmt2 = $conn->prepare("SELECT MAX(entry_id) FROM project_db");
                    $stmt2->execute();
                    $stmt2->bind_result($fgh);
                    while ($stmt2->fetch()) {
                        $max_entry_id = $fgh;
                    }
                    $max_entry_id = $max_entry_id + 1;

                    date_default_timezone_set("Asia/Calcutta");
                    $added_date = date("Y-m-d");

                    if (base64_decode($image_1) != "NULL") {
                        $image_1_name = $email_id . "_project_image_1_" . $max_entry_id;
                        $decode_image_1 = base64_decode("$image_1");
                        file_put_contents(UPLOAD_PATH . $image_1_name . ".WEBP", $decode_image_1);
                        $decode_image_1_name = 'https://app.wooble.org/works/upload' . $image_1_name . ".WEBP";
                    }

                    if (base64_decode($image_2) != "NULL") {
                        $image_2_name = $email_id . "_project_image_2_" . $max_entry_id;
                        $decode_image_2 = base64_decode("$image_2");
                        file_put_contents(UPLOAD_PATH . $image_2_name . ".WEBP", $decode_image_2);
                        $decode_image_2_name = 'https://app.wooble.org/works/upload' . $image_2_name . ".WEBP";
                    }

                    if (base64_decode($image_3) != "NULL") {
                        $image_3_name = $email_id . "_project_image_3_" . $max_entry_id;
                        $decode_image_3 = base64_decode("$image_3");
                        file_put_contents(UPLOAD_PATH . $image_3_name . ".WEBP", $decode_image_3);
                        $decode_image_3_name = 'https://app.wooble.org/works/upload' . $image_3_name . ".WEBP";
                    }

                    if (base64_decode($image_4) != "NULL") {
                        $image_4_name = $email_id . "_project_image_4_" . $max_entry_id;
                        $decode_image_4 = base64_decode("$image_4");
                        file_put_contents(UPLOAD_PATH . $image_4_name . ".WEBP", $decode_image_4);
                        $decode_image_4_name = 'https://app.wooble.org/works/upload' . $image_4_name . ".WEBP";
                    }

                    if (base64_decode($image_5) != "NULL") {
                        $image_5_name = $email_id . "_project_image_5_" . $max_entry_id;
                        $decode_image_5 = base64_decode("$image_5");
                        file_put_contents(UPLOAD_PATH . $image_5_name . ".WEBP", $decode_image_5);
                        $decode_image_5_name = 'https://app.wooble.org/works/upload' . $image_5_name . ".WEBP";
                    }

                    if (base64_decode($image_6) != "NULL") {
                        $image_6_name = $email_id . "_project_image_6_" . $max_entry_id;
                        $decode_image_6 = base64_decode("$image_6");
                        file_put_contents(UPLOAD_PATH . $image_6_name . ".WEBP", $decode_image_6);
                        $decode_image_6_name = 'https://app.wooble.org/works/upload' . $image_6_name . ".WEBP";
                    }

                    if (base64_decode($video) != "NULL") {
                        $video_name = $email_id . "_project_video_1_" . $max_entry_id;
                        $decode_video = base64_decode("$video");
                        file_put_contents(UPLOAD_PATH . $video_name . ".mp4", $decode_video);
                        $decode_video_name = 'https://app.wooble.org/works/upload' . $video_name . ".mp4";
                    }


                    if (base64_decode($project_pdf) != "NULL") {
                        $project_pdf_name = $email_id . "_project_pdf_1_" . $max_entry_id;
                        $decode_project_pdf = base64_decode("$project_pdf");
                        file_put_contents(UPLOAD_PATH . $project_pdf_name . ".pdf", $decode_project_pdf);
                        $decode_project_pdf_name = 'https://app.wooble.org/works/upload' . $project_pdf_name . ".pdf";
                    }

                    $stmt = $conn->prepare("INSERT INTO `project_db`(`email_id`, `work_title`, `aim_of_work`, `work_description`, `image_1`, `image_2`, `image_3`, `image_4`, `image_5`, `image_6`, `video`, `pdf_file`, `conclusion`, `added_date`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("ssssssssssssss", $email_id, $work_title, $aim_of_work, $work_description, $decode_image_1_name, $decode_image_2_name, $decode_image_3_name, $decode_image_4_name, $decode_image_5_name, $decode_image_6_name, $decode_video_name, $decode_project_pdf_name, $conclusion, $added_date);

                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Project Inserted successfully';
                    } else {
                        throw new Exception("Could not upload file");
                    }
                    header('Content-Type: application/json');
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
        case 'updateprojectdata':
            $server_ip = gethostbyname(gethostname());
            if (isset($_POST['email_id'])) {

                try {

                    $entry_id = $_POST['entry_id'];
                    $email_id = base64_decode($_POST['email_id']);
                    
                    $work_title = base64_decode($_POST['work_title']);
                    if ($work_title == "") {
                        $work_title = NULL;
                    }
                    $aim_of_work = base64_decode($_POST['aim_of_work']);
                    if ($aim_of_work == "") {
                        $aim_of_work = NULL;
                    }
                    $work_description = base64_decode($_POST['work_description']);
                    if ($work_description == "") {
                        $work_description = NULL;
                    }
                    $image_1 = $_POST['image_1'];
                    $image_2 = $_POST['image_2'];
                    $image_3 = $_POST['image_3'];
                    $image_4 = $_POST['image_4'];
                    $image_5 = $_POST['image_5'];
                    $image_6 = $_POST['image_6'];
                    $video = $_POST['video'];
                    $project_pdf = $_POST['project_pdf'];
                    $conclusion = base64_decode($_POST['conclusion']);
                    if ($conclusion == "") {
                        $conclusion = NULL;
                    }

                    date_default_timezone_set("Asia/Calcutta");
                    $added_date = date("Y-m-d");

                    function startsWith($string, $startString)
                    {
                        $len = strlen($startString);
                        return (substr($string, 0, $len) == $startString);
                    }


                    if (startsWith(base64_decode($image_1), "http")) {
                        $decode_image_1_name=base64_decode($image_1);
                    } else if (base64_decode($image_1) == "NULL") {
                        $decode_image_1_name=null;
                    } else {
                        $image_1_name = $email_id . "_project_image_1_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_1_name."jpg");
                        $decode_image_1 = base64_decode("$image_1");
                        file_put_contents(UPLOAD_PATH . $image_1_name . ".WEBP", $decode_image_1);
                        $decode_image_1_name = 'https://app.wooble.org/works/upload' . $image_1_name . ".WEBP";
                    }


                    if (startsWith(base64_decode($image_2), "http")) {
                        $decode_image_2_name=base64_decode($image_2);
                    } else if (base64_decode($image_2) == "NULL") {
                        $decode_image_2_name=null;
                    }else{
                        $image_2_name = $email_id . "_project_image_2_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_2_name."jpg");
                        $decode_image_2 = base64_decode("$image_2");
                        file_put_contents(UPLOAD_PATH . $image_2_name . ".WEBP", $decode_image_2);
                        $decode_image_2_name = 'https://app.wooble.org/works/upload' . $image_2_name . ".WEBP";
                    }

                    if (startsWith(base64_decode($image_3), "http")) {
                        $decode_image_3_name=base64_decode($image_3);
                    } else if (base64_decode($image_3) == "NULL") {
                        $decode_image_3_name=null;
                    }else{
                        $image_3_name = $email_id . "_project_image_3_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_3_name."jpg");
                        $decode_image_3 = base64_decode("$image_3");
                        file_put_contents(UPLOAD_PATH . $image_3_name . ".WEBP", $decode_image_3);
                        $decode_image_3_name = 'https://app.wooble.org/works/upload' . $image_3_name . ".WEBP";
                    }

                    if (startsWith(base64_decode($image_4), "http")) {
                        $decode_image_4_name=base64_decode($image_4);
                    } else if (base64_decode($image_4) == "NULL") {
                        $decode_image_4_name=null;
                    }else{
                        $image_4_name = $email_id . "_project_image_4_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_4_name."jpg");
                        $decode_image_4 = base64_decode("$image_4");
                        file_put_contents(UPLOAD_PATH . $image_4_name . ".WEBP", $decode_image_4);
                        $decode_image_4_name = 'https://app.wooble.org/works/upload' . $image_4_name . ".WEBP";
                    }

                    if (startsWith(base64_decode($image_5), "http")) {
                        $decode_image_5_name=base64_decode($image_5);
                    } else if (base64_decode($image_5) == "NULL") {
                        $decode_image_5_name=null;
                    }else{
                        $image_5_name = $email_id . "_project_image_5_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_5_name."jpg");
                        $decode_image_5 = base64_decode("$image_5");
                        file_put_contents(UPLOAD_PATH . $image_5_name . ".WEBP", $decode_image_5);
                        $decode_image_5_name = 'https://app.wooble.org/works/upload' . $image_5_name . ".WEBP";
                    }

                    if (startsWith(base64_decode($image_6), "http")) {
                        $decode_image_6_name=base64_decode($image_6);
                    } else if (base64_decode($image_6) == "NULL") {
                        $decode_image_6_name=null;
                    }else{
                        $image_6_name = $email_id . "_project_image_6_" . $entry_id;
                        unlink(UPLOAD_PATH.$image_6_name."jpg");
                        $decode_image_6 = base64_decode("$image_6");
                        file_put_contents(UPLOAD_PATH . $image_6_name . ".WEBP", $decode_image_6);
                        $decode_image_6_name = 'https://app.wooble.org/works/upload' . $image_6_name . ".WEBP";
                    }


                    if (startsWith(base64_decode($video), "http")) {
                        $decode_video_name=base64_decode($video);
                    } else if (base64_decode($video) == "NULL") {
                        $decode_video_name=null;
                    }else{
                        $video_name = $email_id . "_project_video_1_" . $entry_id;
                        unlink(UPLOAD_PATH.$video_name.".mp4");
                        $decode_video = base64_decode("$video");
                        file_put_contents(UPLOAD_PATH . $video_name . ".mp4", $decode_video);
                        $decode_video_name = 'https://app.wooble.org/works/upload' . $video_name . ".mp4";
                    }
                    if (startsWith(base64_decode($project_pdf), "http")) {
                        $decode_project_pdf_name=base64_decode($project_pdf);
                    } else if (base64_decode($project_pdf) == "NULL") {
                        $decode_project_pdf_name=null;
                    }else{
                        $project_pdf_name = $email_id . "_project_pdf_1_" . $entry_id;
                        unlink(UPLOAD_PATH.$project_pdf_name.".pdf");
                        $decode_project_pdf = base64_decode("$project_pdf");
                        file_put_contents(UPLOAD_PATH . $project_pdf_name . ".pdf", $decode_project_pdf);
                        $decode_project_pdf_name = 'https://app.wooble.org/works/upload' . $project_pdf_name . ".pdf";
                    }
                    $zeero = null;
                    $stmt1 = $conn->prepare("UPDATE `project_db` SET `work_title`=?,`aim_of_work`=?,`work_description`=?,`image_1`=?,`image_2`=?,`image_3`=?,`image_4`=?,`image_5`=?,`image_6`=?,`video`=?,`pdf_file`=?,`conclusion`=?,`added_date`=? WHERE `entry_id`=? AND `email_id`=?");
                    $stmt1->bind_param("sssssssssssssss", $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $zeero, $entry_id, $email_id);
                    $stmt1->execute();

                    $stmt = $conn->prepare("UPDATE `project_db` SET `email_id`=?,`work_title`=?,`aim_of_work`=?,`work_description`=?,`image_1`=?,`image_2`=?,`image_3`=?,`image_4`=?,`image_5`=?,`image_6`=?,`video`=?,`pdf_file`=?,`conclusion`=?,`added_date`=? WHERE `entry_id`=? AND `email_id`=?");
                    $stmt->bind_param("ssssssssssssssss", $email_id, $work_title, $aim_of_work, $work_description, $decode_image_1_name, $decode_image_2_name, $decode_image_3_name, $decode_image_4_name, $decode_image_5_name, $decode_image_6_name, $decode_video_name, $decode_project_pdf_name, $conclusion, $added_date, $entry_id, $email_id);

                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Project updated successfully';
                    } else {
                        throw new Exception("Could not update file");
                    }
                    echo json_encode($response);
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not update file';
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }
            break;
        case 'getprojectdata':
            $server_ip = gethostbyname(gethostname());
            $profileEmail = validate($_POST['email_id']);
            $stmt = $conn->prepare("SELECT `entry_id`, `email_id`, `work_title`, `aim_of_work`, `work_description`, `image_1`, `image_2`, `image_3`, `image_4`, `image_5`, `image_6`, `video`, `pdf_file`, `conclusion` FROM `project_db` WHERE email_id=?");
            $stmt->bind_param("s", $profileEmail);
            $stmt->execute();
            $stmt->bind_result($entry_id, $email_id, $work_title, $aim_of_work, $work_description, $image_1, $image_2, $image_3, $image_4, $image_5, $image_6, $video, $pdf_file, $conclusion);
            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['entry_id'] = $entry_id;
                $temp['email_id'] = $email_id;
                $temp['work_title'] = $work_title;
                $temp['aim_of_work'] = $aim_of_work;
                $temp['work_description'] = $work_description;
                $temp['image_1'] = $image_1;
                $temp['image_2'] = $image_2;
                $temp['image_3'] = $image_3;
                $temp['image_4'] = $image_4;
                $temp['image_5'] = $image_5;
                $temp['image_6'] = $image_6;
                $temp['video'] = $video;
                $temp['pdf_file'] = $pdf_file;
                $temp['conclusion'] = $conclusion;
                array_push($images, $temp);
            }

            header('Content-Type: application/json');
            echo json_encode($images);
            $response['error'] = false;
            $response['images'] = $images;
            break;
        case 'deleteprojectdata':

            if (isset($_POST['entry_id']) && isset($_POST['email_id'])) {
                try {
                    $stmt1 = $conn->prepare("DELETE FROM `project_db` WHERE `entry_id`=? AND `email_id`=?");
                    $stmt1->bind_param("ss", $_POST['entry_id'], $_POST['email_id']);
                    if ($stmt1->execute()) {
                        $response['error'] = false;
                        $response['message'] = 'Project deleted successfully';
                    } else {
                        throw new Exception("Could not delete file");
                    }

                    echo json_encode($response);
                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not delete project';
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
?>