<?php
// Initialize the session
session_start();
// Define variables and initialize with empty values


// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["usertype"]=="Student"){
        header("location: ../student");
        exit;
    }
    if($_SESSION["usertype"]=="Company"){
        header("location: ../company");
        exit;
    }
    if($_SESSION["usertype"]=="Admin"){
        $id=$_SESSION["id"];
        $sql = "SELECT name, mobile, profileurl FROM admindata WHERE id='$id'";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($result);
        $_SESSION["name"]=$row["name"];
        $_SESSION["mnumber"] =$row["mobile"]; 
        $_SESSION["profileurl"]=$row["profileurl"]; 
        header("location: ../admin");
        exit;
        exit;
    }
    
}

// Include config file
require_once "config.php";
$username = $password = $usertype= "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, usertype,email FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $usertype , $email);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username; 
                            $_SESSION["usertype"] = $usertype;
                            $_SESSION["email"] = $email;

                            
                            // Redirect user to welcome page
                            if($_SESSION["usertype"]=="Student"){
                                $sql = "SELECT name, mobile, profileurl FROM student WHERE username='$username'";
                                $result = mysqli_query($link, $sql);
                                $row = mysqli_fetch_assoc($result);
                                $_SESSION["name"]=$row["name"];
                                $_SESSION["mnumber"] =$row["mobile"]; 
                                $_SESSION["profileurl"]=$row["profileurl"]; 
                                $_SESSION["id"]=$id;
                                mysqli_close($link);
                                header("location: ../student");
                                exit;
                            }
                            if($_SESSION["usertype"]=="Company"){
                                $sql = "SELECT name, mobile, profileurl FROM company WHERE username='$username'";
                                $result = mysqli_query($link, $sql);
                                $row = mysqli_fetch_assoc($result);
                                $_SESSION["name"]=$row["name"];
                                $_SESSION["mnumber"] =$row["mobile"]; 
                                $_SESSION["profileurl"]=$row["profileurl"]; 
                                $_SESSION["id"]=$id;
                                mysqli_close($link); 
                                header("location: ../company");
                                exit;
                            }
                            if($_SESSION["usertype"]=="Admin"){
                                $sql = "SELECT name, mobile, profileurl FROM admindata WHERE username='$username'";
                                $result = mysqli_query($link, $sql);
                                $row = mysqli_fetch_assoc($result);
                                $_SESSION["name"]=$row["name"];
                                $_SESSION["mnumber"] =$row["mobile"]; 
                                $_SESSION["profileurl"]=$row["profileurl"]; 
                                $_SESSION["id"]=$id;
                                mysqli_close($link);     
                                header("location: ../admin");
                                exit;}
                            } 
                            else{
                            // Display an error message if password is not valid
                                $password_err = "The password you entered was not valid.";
                            }
                        }
                    } else{
                    // Display an error message if username doesn't exist
                        $username_err = "No account found with that username.";
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            
        // Close statement
            mysqli_stmt_close($stmt);
        }
        
    // Close connection
        mysqli_close($link);
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>InternsPaths</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif; }
            .wrapper{ width: 350px; padding: 20px; }
        </style>
        <link rel="icon" type="image/png" href="../images/icons/favicon.ico"/>
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../fonts/iconic/css/material-design-iconic-font.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
        <!--===============================================================================================-->  
        <link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
        <!--===============================================================================================-->  
        <link rel="stylesheet" type="text/css" href="../vendor/daterangepicker/daterangepicker.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../css/util.css">
        <link rel="stylesheet" type="text/css" href="../css/main.css">
    </head>

    <body>
     <div class="limiter">
      <div class="container-login100" style="background-image: url('../images/bg-01.jpg')" >
       <!-- <div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30"> -->
        <div class="wrapper" style="background-color: white;border-radius: 25px;">
            <span class="login100-form-title ">
              Welcome to InternsPaths
          </span>

          <p>Please fill in your credentials to login.</p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" style="border-radius: 25px">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" style="border-radius: 25px">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="login100-form-btn" class="btn btn-primary"  value="Login">
            </div>
            <p style="text-align: center;"><span class="txt1">
                Don’t have an account?
            </span> <a href="register.php" class="txt2">Sign up now</a>.</p>
             <p style="text-align: center;"><span class="txt1">
                Fogot Your Password?
            </span> <a href="resetpassword.php" class="txt2">Reset Password</a>.</p>
        </form>
        <!--     </div> -->    
    </div>
</div>
</div>
</body>
</html>

