<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "../php/config.php";


$name = $email = $mnumber = "";
$name_err = $email_err = $mnumber_err = "";

$sql = "SELECT * FROM student WHERE id = ?";

if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
    $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $id=$row["id"];
                $username = $row["username"];
                $email = $row["email"];
                $name=$row["name"];
                $mnumber=$row["mobile"];
                $profileurl=$row["profileurl"];
                $address=$row["address"];
                $gender=$row["gender"];
                $linkin = $row["linkedin"];
                $perweb = $row["personalweb"];
                $descrip = $row["descrip"];
                $field =$row["field"];
                $gpa = $row["gpa"];
                $cvurl = $row["cvurl"];


            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                exit();
            }
            
        }
        else{
            echo "Oops! Something went wrong. Please try again later.";
        }


    // Close statement
        mysqli_stmt_close($stmt);

    // Close connection

    } else{
    // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }




    if($_SERVER["REQUEST_METHOD"] == "POST"){



// Processing form data when form is submitted
        if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
            $id = $_POST["id"];
            $gender=trim($_POST["gender"]);
            $address=trim($_POST["address"]);
            $linkin = trim($_POST["linkin"]);
            $perweb = trim($_POST["perweb"]);
            $descrip =trim($_POST["descrip"]);
            $field = trim($_POST["field"]);

    // Validate name
            $input_name = trim($_POST["name"]);
            if(empty($input_name)){
                $name_err = "Please enter a name.";
                echo "Detected";
            } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
                $name_err = "Please enter a valid name.";
                echo "Detected";
            } else{
                $name = $input_name;

            }








    // Validate address address
            $input_email = trim($_POST["email"]);
            if(empty($input_email)){
                $email_err = "Please enter an address.";     
            } else{
                $email = $input_email;
            }

    // Validate salary
            $input_mnumber = trim($_POST["mnumber"]);
            if(empty($input_mnumber)){
                $mnumber_err = "Please enter the salary amount.";     
            } elseif(!ctype_digit($input_mnumber)){
                $mnumber_err = "Please enter a positive integer value.";
            } else{
                $mnumber = $input_mnumber;
            }
//////////////////////////////////validate PDF ////////////////////
            $param_cvurl= $cvurl;
            $uploadOk = 1;
            $target_dir = "../cvuploads/";
            if(!isset($_FILES['cvToUpload']) || $_FILES['cvToUpload']['error'] == UPLOAD_ERR_NO_FILE) {
                $param_cvurl=$cvurl;
            }
            else{
                $extension = pathinfo($_FILES["cvToUpload"]["name"], PATHINFO_EXTENSION);
                $fname = $id;
                $target_file = $target_dir . $fname.".".$extension;

                $pdf_error="";
                $pdfFileType = strtolower(pathinfo($_FILES["cvToUpload"]["name"], PATHINFO_EXTENSION));


                if ($_FILES["cvToUpload"]["size"] > 15000000) {
                    $pdf_error.=  "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                if($pdfFileType != "pdf" && $pdfFileType != "docx" ) {
                    $pdf_error.= "Sorry, only PDF & Docx files are allowed.";
                    $uploadOk = 0;
                }

//////////////////////////////upload PDF //////////////////////////
                if ($uploadOk == 0) {
                    echo $pdf_error;
// if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["cvToUpload"]["tmp_name"], $target_file)) {
                        $param_cvurl=$target_file;
        // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                    } else {
        // $uploadOk=0;
                        echo $pdf_error;

                    }
                }
            }
///////////////////////////////////////////////////////////////////




    // Check input errors before inserting in database
            if(empty($name_err) && empty($email_err) && empty($mnumber_err) && $uploadOk==1){
        // Prepare an update statement
                $sql = "UPDATE student SET name=?, email=?, mobile=?,address=?,gender=? , descrip=?,linkedin=?,personalweb=?,field=?,cvurl=? WHERE id=?";

                if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssssssssssi", $param_name, $param_email, $param_mnumber,$param_address, $param_gender,$param_descrip,$param_linkin,$param_perweb,$param_field,$param_cvurl, $param_id);

            // Set parameters
                    $param_name = $name;
                    $param_email = $email;
                    $param_mnumber = $mnumber;
                    $param_id = $id;
                    $param_address=$address;
                    $param_gender=$gender;
                    $param_descrip=$descrip;
                    $param_linkin=$linkin;
                    $param_perweb=$perweb;
                    $param_field = $field;
                    

            // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                        header("location: managestudent.php");
                        exit();
                    } else{
                        echo "Something went wrong. Please try again later.";
                    }
                }

        // Close statement
            }
            echo "error";
        }
        mysqli_close($link);

    }

    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="images/favicon.ico" type="image/ico" />

        <title>InternsPaths | Admin Console</title>

        <!-- Bootstrap -->
        <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- iCheck -->
        <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

        <!-- bootstrap-progressbar -->
        <link href="../vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- JQVMap -->
        <link href="../vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
        <!-- bootstrap-daterangepicker -->
        <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="../build/css/custom.min.css" rel="stylesheet">
        <link href="css/viewprof.css" rel="stylesheet">
    </head>

    <body class="nav-md">
        <div class="container body" style="height: 1100px">
          <div class="main_container">
            <div class="col-md-3 left_col" style="height: 1200px">
              <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                  <a href="../index.html" class="site_title"><i class="fa fa-paw"></i> <span>InternsPaths Administrator Console</span></a>
              </div>

              <div class="clearfix"></div>

              <!-- menu profile quick info -->
              <div class="profile clearfix">
                  <div class="profile_pic">
                    <img src="<?php echo $_SESSION["profileurl"]; ?>" alt="..." class="img-circle profile_img">
                </div>
                <div class="profile_info">
                    <span>Welcome,</span>
                    <a href="viewadmin.php?id=<?php echo $_SESSION["id"]?>"><h2><?php echo ($_SESSION["name"]);?></h2></a>
                </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Administrators <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="index.php">Home</a></li>
                      <li><a href="editmyprofile.php?id=<?php echo $_SESSION["id"]?>">Edit My Profile</a></li>
                      <li><a href="addadmin.php">Add Administrators</a></li>
                      <li><a href="manageadmin.php">Manage Administrators</a></li>

                  </ul>
              </li>
              <li><a><i class="fa fa-edit"></i> Students <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <li><a href="form.html">Search For a Student</a></li>
                  <li><a href="managestudent.php">Manage Students</a></li>
                  <li><a href="addstudent.php">Add a New Student</a></li>
              </ul>
          </li>
          <li><a><i class="fa fa-desktop"></i> Companies <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="general_elements.html">Search For a Company</a></li>
              <li><a href="managecompany.php">Manage Companies</a></li>
              <li><a href="addcompany.php">Add a New Company</a></li>
          </ul>
      </li>

  </ul>
</div>


</div>
<!-- /sidebar menu -->


</div>
</div>

<!-- top navigation -->
<div class="top_nav">
  <div class="nav_menu">
      <div class="nav toggle">
        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
    </div>
    <nav class="nav navbar-nav">
      <ul class=" navbar-right">
        <li class="nav-item dropdown open" style="padding-left: 15px;">
          <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo $_SESSION["profileurl"]; ?>" alt=""><?php echo ($_SESSION["name"]);?>
        </a>
        <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item"  href="../php/logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
        </div>
    </li>


</ul>
</nav>
</div>
</div>
<!-- /top navigation -->

<!-- page content -->
<div class="right_col" role="main" style="height: 1200px">
  <!-- top tiles -->
  <div class="col-md-10 col-sm-10" style="display: inline-block;" >
    <div class="container emp-profile">
        <div class="row" >
            <div class="col-md-4">
                <div class="col-md-8">
                <div class="profile-head" style="position: relative; left: 195%">
                    <h5>
                        <?php echo $name; ?>
                    </h5>
                    <h6>
                        Student
                    </h6>

                </div>
            </div>
                <div class="profile-img">
                    <img src="<?php echo $profileurl; ?>" alt=""/>

                </div>
                <form class="col-md-12" action="imagestu.php" method="post" enctype="multipart/form-data" style="position: absolute;top: 120%; left: 15%" >
                 <div class="" >
                    <div class="profile-img">
                        <div class="file btn-primary" >
                            Select Image
                            <input  type="file" style="position: absolute;" name="fileToUpload" id="fileToUpload" >                          
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="submit" class="login100-form-btn" value="Click to Change Image" name="submit"style="position: relative;left: 14%">
            </form>
            
        </div>
        <div class="col-md-2" style="position: relative; right:-50%">
            <a href="viewstudent.php?id=<?php echo $id?>" class="btn btn-success pull-right">Back</a>
        </div>

        <div class="row">

            <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype='multipart/form-data' >
            </div>

            <div class="col-md-9 " style="position: relative;left: 25%;top: -170px">
                <div class="tab-content profile-tab" id="myTabContent">
                    <div class="tab-pane fade show active " id="home" role="tabpanel" aria-labelledby="home-tab">

                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" >User ID <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 ">
                              <input type="text" id="id" required="required" class="form-control " value="<?php echo $id ?>" readonly>
                          </div>
                      </div>
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" >User Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="username" name="username"  required="required" class="form-control " value="<?php echo $username ?>" readonly>
                      </div>
                  </div>
                  <br>
                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" >Name <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="text" id="name" name="name"  required="required" class="form-control " value="<?php echo $name ?>" >
                  </div>
              </div>
              <div class="item form-group">
                <label class="col-form-label col-md-3 col-sm-3 label-align" >Email <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 ">
                  <input type="text" id="email" name="email" required="required" class="form-control " value="<?php echo $email ?>" >
              </div>
          </div>
          <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align" >Mobile Number <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 ">
              <input type="text" id="mnumber" name="mnumber" required="required" class="form-control " value="<?php echo $mnumber ?>" >
          </div>
      </div>
      <div class="item form-group">
        <label class="col-form-label col-md-3 col-sm-3 label-align" >Address <span class="required">*</span>
        </label>
        <div class="col-md-6 col-sm-6 ">
          <input type="text" id="address" name="address" required="required" class="form-control " value="<?php echo $address ?>" >
      </div>
  </div>
  <div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align">Gender *:</label>
    <div class="col-md-6 col-sm-6 " style="position: relative;top: 8px">
      <p>
        Male:
        <input type="radio" class="flat" name="gender" id="genderM" value="Male" <?php echo($gender == "Male" ? 'checked' : '') ?> required /> Female:
        <input type="radio" class="flat" name="gender" id="genderF" value="Female" <?php echo($gender == "Female" ? 'checked' :  '') ?> />
    </p>
</div>
</div>
<div class="item form-group" >
    <label class="col-form-label col-md-3 col-sm-3 label-align" >Field Of Study <span class="required">*</span>
    </label>
    <div class="col-md-6 col-sm-6 ">
      <select name = "field" style="height: 100%">
        <option value = "Electronic & Telecommunication Engineering" <?php echo($field == "Electronic & Telecommunication Engineering" ? 'selected' : '') ?>>Electronic & Telecommunication Engineering</option>
        <option value = "Computer Science & Engineering" <?php echo($field == "Computer Science & Engineering" ? 'selected' : '') ?> >Computer Science & Engineering</option>
        <option value = "Mechanical Engineering" <?php echo($field == "Mechanical Engineering" ? 'selected' : '') ?> >Mechanical Engineering</option>
        <option value = "Civil Engineering" <?php echo($field == "Civil Engineering" ? 'selected' : '') ?>>Civil Engineering</option>
        <option value = "Electrical Engineering" <?php echo($field == "Electrical Engineering" ? 'selected' : '') ?> >Electrical Engineering</option>
        <option value = "Chemical & Process Engineering" <?php echo($field == "Chemical & Process Engineering" ? 'selected' : '') ?>>Chemical & Process Engineering</option>
        <option value = "Material Science & Engineering" <?php echo($field == "Material Science & Engineering" ? 'selected' : '') ?>>Material Science & Engineering</option>
        <option value = "Textile Engineering" <?php echo($field == "Textile Engineering" ? 'selected' : '') ?>>Textile Engineering</option>
        <option value = "Earth Resource Engineering" <?php echo($field == "Earth Resource Engineering" ? 'selected' : '') ?> >Earth Resource Engineering</option>
    </select>
</div>
</div>
<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" >Current GPA <span class="required">*</span>
    </label>
    <div class="col-md-6 col-sm-6 ">
      <input type="text" id="address" name="gpa" required="required" class="form-control " value="<?php echo $gpa ?>" >
  </div>
</div>
<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" >LinkedIn URL <span class="required">*</span>
    </label>
    <div class="col-md-6 col-sm-6 ">
      <input type="text" id="address" name="linkin" required="required" class="form-control " value="<?php echo $linkin ?>" >
  </div>
</div>
<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" >Personal Website URL <span class="required">*</span>
    </label>
    <div class="col-md-6 col-sm-6 ">
      <input type="text" id="perweb" name="perweb" required="required" class="form-control " value="<?php echo $perweb ?>" >
  </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" >Short Description <span class="required">*</span>
    </label>
    <div   class="col-md-6 col-sm-6 ">
      <textarea class="resizable_textarea form-control" name="descrip" value="<?php echo $descrip ?>"  spellcheck="false"><?php echo $descrip ?></textarea>
  </div>
</div>
<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" >Upload CV <span class="required">*</span>
    </label>
    <div class="col-md-6 col-sm-6 ">
     <input type="file" name="cvToUpload" id="cvToUpload" >
 </div>
</div>
<input type="hidden" name="id" value="<?php echo $id; ?>"/>
<input type="submit" class="btn btn-primary" value="Submit">


</div>

</div>
</div>
</div>
</div>

</form>    

</div>
</div>
</div>





<!-- /top tiles -->

<!-- /////////////contend///////// -->

<!-- /////////////contend///////// -->



</div>
</div>

<!-- jQuery -->
<script src="../vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="../vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress -->
<script src="../vendors/nprogress/nprogress.js"></script>
<!-- Chart.js -->
<script src="../vendors/Chart.js/dist/Chart.min.js"></script>
<!-- gauge.js -->
<script src="../vendors/gauge.js/dist/gauge.min.js"></script>
<!-- bootstrap-progressbar -->
<script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
<!-- iCheck -->
<script src="../vendors/iCheck/icheck.min.js"></script>
<!-- Skycons -->
<script src="../vendors/skycons/skycons.js"></script>
<!-- Flot -->
<script src="../vendors/Flot/jquery.flot.js"></script>
<script src="../vendors/Flot/jquery.flot.pie.js"></script>
<script src="../vendors/Flot/jquery.flot.time.js"></script>
<script src="../vendors/Flot/jquery.flot.stack.js"></script>
<script src="../vendors/Flot/jquery.flot.resize.js"></script>
<!-- Flot plugins -->
<script src="../vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
<script src="../vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
<script src="../vendors/flot.curvedlines/curvedLines.js"></script>
<!-- DateJS -->
<script src="../vendors/DateJS/build/date.js"></script>
<!-- JQVMap -->
<script src="../vendors/jqvmap/dist/jquery.vmap.js"></script>
<script src="../vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
<script src="../vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
<!-- bootstrap-daterangepicker -->
<script src="../vendors/moment/min/moment.min.js"></script>
<script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Custom Theme Scripts -->
<script src="../build/js/custom.min.js"></script>

</body>
</html>