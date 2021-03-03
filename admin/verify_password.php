<?php
    include('core/header.php');

    if (isset($_POST['submit']) && $_POST['submit'] != '') {
        //default user: test@test.nl
        //default password: test123
        $token = $con->real_escape_string($_GET['token']);
        $password_1 = $con->real_escape_string($_POST['password_1']);
        $password_2 = $con->real_escape_string($_POST['password_2']);
        
        $liqry = $con->prepare("SELECT admin_user_id,email FROM admin_user WHERE password_token = ? LIMIT 1;");
        if($liqry === false) {
           echo mysqli_error($con);
        } else{
            $liqry->bind_param('s',$token);
            $liqry->bind_result($adminId,$email);
            if($liqry->execute()){
                $liqry->store_result();
                $liqry->fetch();
                if($liqry->num_rows == '1' && $password_1 == $password_2){

                    $password = password_hash($password_1, PASSWORD_DEFAULT);
                    
                    $query1 = $con->prepare("UPDATE admin_user SET password = ?, password_token = '', password_changed = NOW() WHERE admin_user_id = ? LIMIT 1;");
                    if ($query1 === false) {
                        echo mysqli_error($con);
                    }
                    
                    $query1->bind_param('si',$password,$adminId);
                    if ($query1->execute() === false) {
                        echo mysqli_error($con);
                    } 
                    $query1->close();
                    
                    echo "Gelukt, u wordt doorgestuurd... <meta http-equiv=\"refresh\" content=\"2; URL=index.php\">";
                    exit();
                } else {
                    echo "ERROR tijdens verzenden. Komen de wachtwoorden overeen?";
                }
            }
            $liqry->close();
        }
    }
?>
<form action="verify_password.php?token=<?= $_GET['token'];?>" method="post">
    <input type="password" name="password_1" id="" placeholder="Password">
    <input type="password" name="password_2" id="" placeholder="Repeat Password">
    <input type="submit" name="submit" value="Request password">
    <a href="index.php">Login</a>
</form>
<?php
    include('core/footer.php');
?>