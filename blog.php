<?php

//Constants for database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');

require_once "validate.php";
//We will upload files to this folder
//So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
define('UPLOAD_PATH', 'blog_pic/');

//connecting to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');

//An array to display the response
$response = array();

//if the call is an api call
if (isset($_GET['apicall'])) {

    //switching the api call
    switch ($_GET['apicall']) {

        //if it is an upload call we will upload the image
        case 'insertblogdata':

            //first confirming that we have the image and tags in the request parameter
            if (isset($_POST['title']) || isset($_POST['content'])) {

                //uploading file and storing it to database as well
                try {

                    //$name = $_POST['name'];

                    date_default_timezone_set("Asia/Calcutta");
                    $file_created = date("Y-m-d");

                    //$decodeimage = base64_decode("$image");
                    //$return = file_put_contents(UPLOAD_PATH . $name . ".jpg", $decodeimage);

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

                    //fetching all the images from database
                    //and pushing it to array
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

        //in this call we will fetch all the images
        case 'getblogdata':

            //getting server ip for building image url
            $server_ip = gethostbyname(gethostname());

            $profileEmail = validate($_POST['email_id']);
            //query to get images from database
            $stmt = $conn->prepare("SELECT blog_db.file_id, user_info.full_name, blog_db.title, blog_db.content, blog_db.created_date
            FROM user_info
            INNER JOIN blog_db ON user_info.email=? AND blog_db.email_id=?");
            $stmt->bind_param("ss", $_POST['email_id'], $_POST['email_id']);
            $stmt->execute();
            $stmt->bind_result($file_id, $full_name, $title, $content, $created_date);

            $images = array();
            while ($stmt->fetch()) {
                $temp = array();
                $temp['file_id'] = $file_id;
                $temp['full_name'] = $full_name;
                $temp['title'] = $title;
                $temp['content'] = $content;
                $temp['created_date'] = $created_date;
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

        case 'getblogimages':

            //getting server ip for building image url
            $server_ip = gethostbyname(gethostname());

            //$profileEmail = validate($_POST['email_id']);
            //query to get images from database

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

            //pushing the array in response
            $response['error'] = false;
            $response['images'] = $images;
            header('Content-Type: application/json');
            echo json_encode($images);
            break;

        case 'deleteblogdata':
            //first confirming that we have the image and tags in the request parameter
            if (isset($_POST['file_id'])) {

                //uploading file and storing it to database as well
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

//displaying the response in json
