<?php
    if($_POST['email']){
        require_once "conn.php";
        require_once "validate.php";
         $response = array();
        $email = $_POST['email'];
        $stmt = $conn->prepare("SELECT field_1,compressed_image,username,field_2,fb_link,insta_link,linkedin_link,twitter_link,whatsapp_link,field_3 FROM user_details WHERE email = ?");
        $stmt->bind_param("s",$email);
        $result = $stmt->execute();
      if($result == TRUE){
            $response['error'] = false;
            $response['message'] = "Retrieval Successful!";
            $stmt->store_result();
            $stmt->bind_result($full_name,$profile_pic,$username,$designation,$fb_link,$insta_link,$linkedin_link,$twitter_link,$whatsapp_link,$background);
            $stmt->fetch();
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
            $response['error'] = true;
            $response['message'] = "Incorrect id";
        }
    } else{
         $response['error'] = true;
         $response['message'] = "Insufficient Parameters";
    }
    echo json_encode($response);
?>