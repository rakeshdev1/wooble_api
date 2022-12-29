<?php

//Constants for database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');

require_once "validate.php";
//We will upload files to this folder
//So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
define('UPLOAD_PATH', 'resume/');

//connecting to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');

//An array to display the response
$response = array();

//if the call is an api call
if (isset($_GET['apicall'])) {

    //switching the api call
    switch ($_GET['apicall']) {

            //if it is an upload call we will upload the image
        case 'insertresumedata':

            //first confirming that we have the image and tags in the request parameter
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

                    // $image_name = $_POST['name'] . ".jpg";

                    // $stmt1 = $conn->prepare("INSERT INTO `blog_image`(`file_id`,`image`) VALUES (?,?)");
                    // $stmt1->bind_param("is", $file_id, $image_name);
                    // $stmt1->execute();

                } catch (Exception $e) {
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }
            break;


            //in this call we will fetch all the images
        case 'getresumedata':

            //getting server ip for building image url
            $server_ip = gethostbyname(gethostname());

            $profileEmail = validate($_POST['email_id']);
            //query to get images from database
            $stmt = $conn->prepare("SELECT resume,title FROM resume_db WHERE email_id=?");
            $stmt->bind_param("s", $_POST['email_id']);
            $stmt->execute();
            $stmt->bind_result($resume, $title);

            $images = array();

            //fetching all the images from database
            //and pushing it to array
            while ($stmt->fetch()) {
                $temp = array();
                $temp['title'] = $title;
                $temp['resume'] = 'http://' . $server_ip . '/wooble-api/' . UPLOAD_PATH . $resume;
                
                array_push($images, $temp);
            }

            // $stmt1 = $conn->prepare("SELECT `image` FROM `blog_image` WHERE `file_id`=?");
            // $stmt1->bind_param("i", $file_id);
            // $stmt1->execute();
            // $stmt1->bind_result($image);

            // while ($stmt1->fetch()) {
            //     $temp['image'] = 'http://' . $server_ip . '/wooble-api/' . UPLOAD_PATH . $image;
            //     array_push($images, $temp);
            // }


            //pushing the array in response
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

//displaying the response in json
