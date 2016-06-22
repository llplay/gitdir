<?php


   require_once("../PHPExcel/Classes/PHPExcel.php");
   define("FILE_NAME", "../DB.conf");
   define("SUCCESS", 0);
   define("DELAY_SEC", 3);
   define("FILE_ERROR", -2);
   // $login_name = "Phantom";
   session_start();
   $QQ = $_SESSION["QQ"];
   $login_name = $_SESSION["username"];
   session_write_close();

   if (file_exists(FILE_NAME))
   {
      include(FILE_NAME);
   }
   else
   {
      sleep(DELAY_SEC);
      $resultStr = FILE_ERROR . " " . __LINE__;
   }

   //query        
   $link;
   $db_host;
   $admin_account;
   $admin_password;
   $connect_db;
   $str_query;
   $str_query1;
   $result;                 //query result
   $result1;
   $row;                    //result data array
   $row1;
   $row_number;
   $refresh_str;

   header('Content-Type:text/html;charset=utf-8');

   //define
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("UPLOAD_FILE_NAME","upload.pdf");

   //timezone
   date_default_timezone_set(TIME_ZONE);

   // since phpexcel maybe execute very long time, so currently set time limit to 0
   set_time_limit(0);
   ini_set('memory_limit', '-1');

   //上传文件路径
   $target_dir = "uploads/";
   $file_type = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
   //上传后新文件名称
   $target_file = $target_dir.time().hash('md5', $_FILES["fileToUpload"]["name"]).".$file_type";

   $file_status = new UploadFileStatus();
   $file_status->status = UPLOAD_SUCCESS;


   //判断是否是空文件
   if ($_FILES['fileToUpload']['size'] == 0) {
      $file_status->status = ERR_EMPTY_FILE; 
      array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_EMPTY_FILE));
      goto err_exit;
   }

   //文件是否存在
   if (file_exists($target_file))
   {
      $file_status->status = ERR_FILE_EXIST; 
      array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_FILE_EXIST));
      goto err_exit;
   }


   //产生新文件
   if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
   {
      $file_status->status = ERR_MOVE_FILE; 
      array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_MOVE_FILE));
      goto err_exit;
   }


   //Excle 格式检查
   if (!is_valid_excel_type($target_file))
   {
      $file_status->status = ERR_FILE_TYPE; 
      array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_FILE_TYPE));
      goto err_exit;
   }

   if (($ret = read_excel_and_insert_into_database($target_file,$user_id)) != SUCCESS)
   {   
      $file_status->status = $ret;
      if ($ret == ERR_UPDATE_DATABASE)
      {
         array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_UPDATE_DATABASE));
      }
      else if ($ret == ERR_INSERT_DATABASE)
      {
         array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_INSERT_DATABASE));
      }
   }
  
//   err_exit:
  
   //判断Excel格式
//   function is_valid_excel_type($file_path)
//   {
      //only accept excel2007, and 2003 format
//      $valid_types = array('Excel2007', 'Excel5');
   
//      foreach($valid_types as $type)
//      {
//         $reader = PHPExcel_IOFactory::createReader($type);
//         print_r($reader);
//         echo "<br />";
//         print_r($reader->canRead($file_path));
//         echo "<br />";
//         if ($reader->canRead($file_path))
//         {
//            return true;
//         }
//      }
   
//      return false;
//   }

//   function read_excel_and_insert_into_database($target_file,$userid)
//   {
      // return {"status":, error:[{"line":"1", "message":"xxx error"},{"line":"", "message":""}, ...]}

//      $users = array();
//      global $file_status;
      // load file
//      try
//      {
//         $input_file_type = PHPExcel_IOFactory::identify($target_file);
//         $reader = PHPExcel_IOFactory::createReader($input_file_type);
//         $excel = $reader->load($target_file);
//      }
//      catch (Exception $e)
//      {
//         $file_status->status = ERR_FILE_LOAD; 
//         array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>$e->getMessage()));
//         return $file_status->status;
//      }
 
      // parse file
//      $sheet_count = $excel->getSheetCount();
   
//      for ($cur_sheet=0; $cur_sheet < $sheet_count; $cur_sheet++)
//      {
//         $sheet = $excel->getSheet($cur_sheet);
//         $sheet_title = $sheet->getTitle();
         //print_r($sheet_title);
//         if ($sheet_title == "上传名单说明")
//         {
//            continue;
//         }
         // if sheet name is xxxx, skip it
//         $highest_row = $sheet->getHighestRow();
//         $highest_col = count($file_status->upload_user_syntax);

//         $tmp = array();

//         for ($col=0; $col<=$highest_col; $col++)
//         {
//            array_push($tmp, trim($sheet->getCellByColumnAndRow($col, 1)->getValue()));
//         }
//         if (!is_valid_syntax_import_file($tmp))
//         {
//            $file_status->status = ERR_FILE_LOAD;
//            array_push($file_status->errors, array("sheet"=>$cur_sheet, "lines"=>0, "message"=>MSG_ERR_FILE_CONTENT_SYNTAX));
//            return $file_status->status;
//         }
      
//         for ($row=2; $row<=$highest_row; $row++)
//         {
//            $tmp = array();
//            $functions = array();
//            for ($col=0; $col<=$highest_col; $col++)
//            {
//               array_push($tmp, trim($sheet->getCellByColumnAndRow($col, $row)->getValue()));
//            }
//            if (is_empty_row($tmp))
//            {
//               continue;
//            }
         
//            $cur_user = new UploadUser($tmp);

         
            //用户名称
//            if (!is_correct_user_name_format($cur_user->UserName))
//            {
//               $file_status->status = ERR_FILE_LOAD;
//               array_push($file_status->errors, array("sheet"=>$cur_sheet, "lines"=>$row, "message"=>MSG_ERR_USER_NAME_FORMAT));
//            }
         
         
            //是否审批者
//            $canapprove = get_canapprove_from_tf($cur_user->CanApprove);
//            $cur_user->CanApprove = $canapprove;
            
            //检查员工编号是否存在
//            if (is_user_exist($cur_user))
//            {
//               array_push($file_status->errors, array("sheet"=>"$cur_sheet", "lines"=>"$row", "UserName"=>$cur_user->UserName, "EmployeeId"=>$cur_user->EmployeeId, "message"=> "此用户系统中已存在！"));
//            }
//            else {
 //              array_push($users, $cur_user);
//            }
//         }
//      }

//      if ($file_status->status == UPLOAD_SUCCESS)
//      {
//         return write_into_database($users,$userid);
//      }
//      else
//      {  
//         return $file_status->status;
//      }
//   }

//   function get_canapprove_from_tf($canapprove)
//   {
//      if ($canapprove == "")
//      {
//         return 0;
//      }
//      else if ($canapprove == "是")
//      {
//         return 1;
//      }
//      else if ($canapprove == "否")
//      {
//         return 0;
//      }
//   }

//   function get_function_id_from_database($func_name)
//   {
//      $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
//      if (!$link) 
//      {   
//         die(MSG_ERR_CONNECT_TO_DATABASE);
//      }
   
//      $str_query = "Select * from functions where FunctionName='$func_name'";
//      if ($result = mysqli_query($link, $str_query))
//      {
//         $row_number = mysqli_num_rows($result);
//         if ($row_number > 0)
//         {
//            $row = mysqli_fetch_assoc($result);
//            return $row["FunctionId"];
//         }
//      }
//      return ERR_PROB_FUNC_NOT_EXIST;
//   }

//   function write_into_database($users,$userid)
//   {
//      foreach ($users as $user)
//      {
//        $ret = insert_new_user($user,$userid);
//         if ($ret != SUCCESS)
//         {
//            return $ret;
//         }
//      }
//      return SUCCESS;
//   }

//   function is_user_exist($user)
//   {
//      $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
//      if (!$link) 
//      {   
//         die(MSG_ERR_CONNECT_TO_DATABASE);
//      }
   
//      $str_query = "Select * from users where EmployeeId='$user->EmployeeId'";
      
//      if ($result = mysqli_query($link, $str_query))
//      {
//         $row_number = mysqli_num_rows($result);
//         if ($row_number > 0)
//         {
//            return true;
//         }
//         else
//         {
//            return false;
//         }
//      }
//   }

//   function  insert_new_user($user,$userid)
//   {
//      $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
//      if (!$link) 
//      {   
//         die(MSG_ERR_CONNECT_TO_DATABASE);
//      }
   
//      $str_query = "
//             INSERT INTO users (UserName,Email,Status,CanApprove,CreatedUser,CreatedTime,EditUser,EditTime
//             ,EmployeeId) VALUES('$user->UserName', '$user->Email', 0, $user->CanApprove, $userid, now()
//             ,$userid, now(), '$user->EmployeeId')";
      
//      if(mysqli_query($link, $str_query))
//      {
//         return SUCCESS;
//      }
//      else
//      {
//         if($link){
//            mysqli_close($link);
//         }
//         sleep(DELAY_SEC);
//         return ERR_INSERT_DATABASE;
//      }
   //  }
  
  
?>
<!DOCTYPE HTML>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
      <link type="image/x-icon" href="../images/logo.ico" rel="shortcut icon">
      <link rel="stylesheet" type="text/css" href="../lib/yui-cssreset-min.css">
      <link rel="stylesheet" type="text/css" href="../lib/yui-cssfonts-min.css">
      <link rel="stylesheet" type="text/css" href="../css/OSC_layout.css">
      <link type="text/css" href="../lib/jQueryDatePicker/jquery-ui.custom.css" rel="stylesheet" />
      <script type="text/javascript" src="../lib/jquery.min.js"></script>
      <script type="text/javascript" src="../lib/jquery-ui.min.js"></script>
      <script type="text/javascript" src="../js/OSC_layout.js"></script>
      <!-- for tree view -->
      <!-- End of tree view -->
      <!--[if lt IE 10]>
      <script type="text/javascript" src="lib/PIE.js"></script>
      <![endif]-->
      <!-- Bootstrap core CSS -->
      <link href="../newui/css/bootstrap.min.css" rel="stylesheet">
      <link href="../newui/css/bootstrap-reset.css" rel="stylesheet">

      <!--Animation css-->
      <link href="../newui/css/animate.css" rel="stylesheet">

      <!--Icon-fonts css-->
      <link href="../newui/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
      <link href="../newui/assets/ionicon/css/ionicons.min.css" rel="stylesheet" />

      <!--Morris Chart CSS -->
      <link rel="stylesheet" href="../newui/assets/morris/morris.css">

      <!-- sweet alerts -->
      <link href="../newui/assets/sweet-alert/sweet-alert.min.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="../newui/css/style.css" rel="stylesheet">
      <link href="../newui/css/helper.css" rel="stylesheet">
      <link href="../newui/css/style-responsive.css" rel="stylesheet" />
      <title>竹间 - 上传名单页面</title>
      <!-- BEG_ORISBOT_NOINDEX -->
      <Script Language=JavaScript>
         function loaded() {}
      </Script>
      <!--Step15 新增修改页面    起始 -->
   </head>
   <body Onload="loaded();">
      <div id="content">
      <!-- Header -->
         <header class="top-head container-fluid">
            <button type="button" class="navbar-toggle pull-left">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <!-- Left navbar -->
            <nav class=" navbar-default hidden-xs" role="navigation">
               <ul class="nav navbar-nav">
                  <li><a href="#"><?php echo date('Y-m-d',time()); ?></a></li>
               </ul>
            </nav>
            <!-- Right navbar -->
            <ul class="list-inline navbar-right top-menu top-right-menu">  
            <!-- user login dropdown start-->
               <li class="dropdown text-center">
                  <input type="hidden" id="userid" value="<?php echo $user_id ?>" />
                  <form name=logoutform action=logout.php></form>
                  <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                     <i class="fa fa-user"></i>
                     <span class="username"><?php echo $login_name ?> </span> <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu extended pro-menu fadeInUp animated" tabindex="5003" style="overflow: hidden; outline: none; display:none;">
                     <li><a href="javascript:void(0)" OnClick="click_logout();"><i class="fa fa-sign-out"></i> 退出</a></li>
                  </ul>
               </li>
               <!-- user login dropdown end -->       
            </ul>
            <!-- End right navbar -->
         </header>
         <!-- Header Ends -->
         <!-- Page Content Start -->
         <!-- ================== -->
         <div class="wraper container-fluid">
            <div class="page-title"> 
               <h3 class="title"><?php echo $TitleStr; ?></h3> 
            </div>
            <!-- Basic Form Wizard -->
            <div class="row">
               <div class="col-md-12">

               </div> <!-- end col -->
            </div> <!-- End row -->
         </div>
         <!-- Page Content Ends -->
         <!-- ================== -->

         <!-- Footer Start -->
         <footer class="footer">
            2015 © 竹间.
         </footer>
         <!-- Footer Ends -->
      </div> 
   </body>
</html>
