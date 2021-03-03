<?php
    include('core/header.php');

    if (isset($_POST['submit']) && $_POST['submit'] != '') {
        //default user: test@test.nl
        //default password: test123
        $email = $con->real_escape_string($_POST['email']);

        $liqry = $con->prepare("SELECT admin_user_id,email FROM admin_user WHERE email = ? LIMIT 1;");
        if($liqry === false) {
           echo mysqli_error($con);
        } else{
            $liqry->bind_param('s',$email);
            $liqry->bind_result($adminId,$email);
            if($liqry->execute()){
                $liqry->store_result();
                $liqry->fetch();
                if($liqry->num_rows == '1'){

                    $token = sha1(mt_rand(1, 90000) . 'WEBSHOP2021-1wdv');

                    $url = BASEURL_CMS.'verify_password.php?token='.$token;
                    
                    $query1 = $con->prepare("UPDATE admin_user SET password_token = ? WHERE admin_user_id = ? LIMIT 1;");
                    if ($query1 === false) {
                        echo mysqli_error($con);
                    }
                    
                    $query1->bind_param('si',$token,$adminId);
                    if ($query1->execute() === false) {
                        echo mysqli_error($con);
                    } 
                    $query1->close();
                    

                    echo "Hierbij de nieuwe url:<br>";
                    echo "<a href='{$url}'>".$url."</a>";
                    exit();
                } else {
                    echo "ERROR tijdens inloggen";
                }
            }
            $liqry->close();
        }
    }
?>
<form action="forgot_password.php" method="post">
    <input type="email" name="email" id="" placeholder="Email">
    <input type="submit" name="submit" value="Request password">
    <a href="index.php">Login</a>
</form>
<?php
    include('core/footer.php');
?>