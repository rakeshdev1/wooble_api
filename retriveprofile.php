<?php
// if(isset($_POST['fullname']) && isset($_POST['username'])){
//     // Include the necessary files
    

    if($_POST['email']){

        require_once "conn.php";
        require_once "validate.php";

         $response = array();
        // if the parameter send from the user id id then
        // we will search the item for specific id.
        $email = $_POST['email'];
           //on below line we are selecting the course detail with below id.
        $stmt = $conn->prepare("SELECT field_1,compressed_image,username,field_2,fb_link,insta_link,linkedin_link,twitter_link,whatsapp_link,field_3 FROM user_details WHERE email = ?");
        $stmt->bind_param("s",$email);
        $result = $stmt->execute();
      // on below line we are checking if our 
      // table is having data with specific id. 
      if($result == TRUE){
            // if we get the response then we are displaying it below.
            $response['error'] = false;
            $response['message'] = "Retrieval Successful!";
            // on below line we are getting our result. 
            $stmt->store_result();
            // on below line we are passing parameters which we want to get.
            $stmt->bind_result($full_name,$profile_pic,$username,$designation,$fb_link,$insta_link,$linkedin_link,$twitter_link,$whatsapp_link,$background);
            // on below line we are fetching the data. 
            $stmt->fetch();
            // after getting all data we are passing this data in our array.
            $response['full_name'] = $full_name;
            $response['profile_pic'] = $profile_pic;
            $response['username'] = $username;
            $response['designation'] = $designation;
            $response['fb_link'] = $fb_link;
            $response['insta_link'] = $insta_link;
            $response['linkedin_link'] = $linkedin_link;
            $response['twitter_link'] = $twitter_link;
            $response['whatsapp_link'] = $whatsapp_link;
            $response['background'] = $background;
        } else{
            // if the id entered by user donot exist then 
            // we are displaying the error message
            $response['error'] = true;
            $response['message'] = "Incorrect id";
        }
    } else{
         // if the user donot adds any parameter while making request
         // then we are displaying the error as insufficient parameters.
         $response['error'] = true;
         $response['message'] = "Insufficient Parameters";
    }
    // at last we are printing 
    // all the data on below line. 
    echo json_encode($response);

    
// }
?>