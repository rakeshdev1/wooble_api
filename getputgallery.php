<?php

//Constants for database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');

//We will upload files to this folder
//So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
define('UPLOAD_PATH', 'gallery_pic/');

//connecting to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');

//An array to display the response
$response = array();

date_default_timezone_set("Asia/Calcutta");
$date = date('Y-m-d H:i:s a', time());
//if the call is an api call
if (isset($_GET['apicall'])) {

    //switching the api call
    switch ($_GET['apicall']) {

        //if it is an upload call we will upload the image
        case 'creategallery':

            $profileEmail = $_POST['profileEmail'];
            $gallery_pic = $_FILES['pic']['name'];
            //first confirming that we have the image and tags in the request parameter
            if (isset($_POST['profileEmail'])) {

                //uploading file and storing it to database as well
                try {

                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    //$stmt = $conn->prepare("INSERT INTO gallery_db (file_id, email_id, file_name, thumbnail, file_content, title, description, file_created) VALUES ('1','rakesh@gmail.com','logo.png','logo.png','logo.png','hello','hello','hii') WHERE email='rakesh@gmail.com'");
                    //$stmt->bind_param("ssssssss", $_POST['profileEmail'],$gallery_pic,$gallery_pic,$gallery_pic,$_POST['title'], $_POST['description'],$date,$_POST['profileEmail']);
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

//uploading file and storing it to database as well
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

        //in this call we will fetch all the images
        case 'getgallery':

            //getting server ip for building image url
            //getting server ip for building image url
            $server_ip = gethostbyname(gethostname());

            //query to get images from database
            $stmt = $conn->prepare("SELECT full_name, mobile, email FROM user_info WHERE email=?");
            $stmt->bind_param("s", $profileEmail);
            $stmt->execute();
            $stmt->bind_result($fullname, $mobile, $email);

            $images = array();

            //fetching all the images from database
            //and pushing it to array
            while ($stmt->fetch()) {
                $temp = array();
                $temp['fullname'] = $fullname;
                $temp['mobile'] = $mobile;
                $temp['email'] = $email;

                array_push($images, $temp);
            }

            //pushing the array in response
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

//displaying the response in json
header('Content-Type: application/json');
//echo json_encode($response);
echo json_encode($response);
