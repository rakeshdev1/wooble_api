<?php

//Constants for database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wooble');

require_once "validate.php";
//We will upload files to this folder
//So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
define('UPLOAD_PATH', 'profile_pic/');

//connecting to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Unable to connect');

//An array to display the response
$response = array();

//if the call is an api call
if (isset($_GET['apicall'])) {

    //switching the api call
    switch ($_GET['apicall']) {

        //if it is an upload call we will upload the image
        case 'updatefullprofile':

            //first confirming that we have the image and tags in the request parameter
            if (isset($_FILES['pic']['name']) || isset($_POST['fullname']) || isset($_POST['username'])) {

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

                //uploading file and storing it to database as well
                try {
                    move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
                    //$stmt = $conn->prepare("INSERT INTO user_info (image, tags) VALUES (?,?)");
                    $stmt = $conn->prepare("UPDATE user_details SET field_1=?,compressed_image=?,username=?,field_2=?,fb_link=?,insta_link=?,linkedin_link=?,twitter_link=?,whatsapp_link=?,field_3=? WHERE email=? ");
                    // $stmt->bind_param("ss", $_FILES['pic']['name'], $_POST['tags']);
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

        //in this call we will fetch all the images
        case 'getfullprofile':

            //getting server ip for building image url
            $server_ip = gethostbyname(gethostname());

            $profileEmail = validate($_POST['profileEmail']);
            //query to get images from database
            $stmt = $conn->prepare("SELECT field_1, compressed_image, username, field_2, fb_link, insta_link, linkedin_link, twitter_link, whatsapp_link, field_3 FROM user_details WHERE email=?");
            $stmt->bind_param("s", $profileEmail);
            $stmt->execute();
            $stmt->bind_result($fullname, $profile_pic, $username, $designation, $fb_link, $insta_link, $linkedin_link, $twitter_link, $whatsapp_link, $background);

            $images = array();

            //fetching all the images from database
            //and pushing it to array
            while ($stmt->fetch()) {
                $temp = array();
                $temp['fullname'] = $fullname;
                $temp['profile_pic'] = 'http://' . $server_ip . '/wooble-api/' . UPLOAD_PATH . $profile_pic;
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


