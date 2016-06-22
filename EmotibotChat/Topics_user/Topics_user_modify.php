<?php

//   echo "123";
//   return;
   define("FILE_NAME", "../DB.conf");
   define("DELAY_SEC", 3);
   define("FILE_ERROR", -2);
   
   if (file_exists(FILE_NAME))
   {
      include(FILE_NAME);
   }
   else
   {
      sleep(DELAY_SEC);
      echo FILE_ERROR;
      return;
   }
   
   try{
      // TODO: 从 Session 里面拿到 login_name + user_id
      session_start();
      if (isset($_SESSION["QQ"]) == "" || isset($_SESSION["username"]) == "")
      {
         session_write_close();
         sleep(DELAY_SEC);
         header("Location:". $web_path . "userMain.php?cmd=err");
         exit();
      }
   }
   catch(exception $ex)
   {
      session_write_close();
      sleep(DELAY_SEC);
      header("Location:". $web_path . "userMain.php?cmd=err");
      exit();
   }
   
   $QQ = $_SESSION["QQ"];
   $login_name = $_SESSION["username"];
   $current_func_name = "iSearch";
   session_write_close();

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
   $chatId;                 //会话编号
   header('Content-Type:text/html;charset=utf-8');
   
   //define
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("TIME_ZONE", "Asia/Shanghai");
   define("ILLEGAL_CHAR", "'-;<>");                         //illegal char

   //return value
   define("SUCCESS", 0);
   define("DB_ERROR", -1);
   define("SYMBOL_ERROR", -3);
   define("SYMBOL_ERROR_CMD", -4);
   define("MAPPING_ERROR", -5);
   
   //timezone
   date_default_timezone_set(TIME_ZONE);
   
   //----- Connect to MySql -----
   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
   if (!$link)  //connect to server failure   
   {   
      sleep(DELAY_SEC);
      echo DB_ERROR;                
      return;
   }
   
   //----- Check command -----
   function check_command($check_str)
   {
      if(strcmp($check_str, "read") && strcmp($check_str, "write"))
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   //----- Check number -----
   function check_number($check_str)
   {
      if(!is_numeric($check_str))
      {
         return SYMBOL_ERROR; 
      }
      if($check_str < 0)
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   
   //get data from client
   $cmd;
   $DeptId;
   //query
   $link;
   
   //echo "123";
   //echo "cmd";
   //1.get information from client 
   if(($cmd = check_command($_GET["cmd"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR_CMD;
      return;
   }
   if(($topicId = check_number($_GET["TopicId"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR;
      return;
   }
   //link    
   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
   mysqli_set_charset ($link,"utf8");
   if (!$link)  //connect to server failure    
   {
      sleep(DELAY_SEC);
      echo DB_ERROR;       
      return;
   }
   
  // echo "$link";
  // return;
//   $datasyz = array();
//   $datacpmc = array();
//   class StuFunction{
//      public $functionId;
//      public $functionName;
//      public $createdTime;
//   }
   //------query -----
   //判断当前用户是否合法，若合法，生成chaId insert 到chatlog，生成一条新纪录
   $str_query_name = "select username from users where username = '$login_name'";
   $str_query_qq  = "select qq from qqnumbers where qq = '$QQ'";
   $str_query_topicId = "select name from topics where id = $topicId";

   $result_topicId = mysqli_query($link,$str_query_topicId);
   $result_name = mysqli_query($link, $str_query_name);
   $result_qq = mysqli_query($link, $str_query_qq);
 //  return;
   //  
 //echo "$topicId";
 //echo "$str_query_name";
 //echo "$str_query_qq";
 //return;
 // echo "$str_query_topicId";
 //  echo "$result_name";
 //  echo "$result_qq";
 //  echo "$connect_db";
 //  echo "$link";
   if($result_name && $result_qq && $result_topicId)
   {
      //return;
      $rowNum_name = mysqli_num_rows($result_name);
      $rowNum_qq = mysqli_num_rows($result_qq);
      $rowNum_topicId = mysqli_num_rows($result_topicId);
      $row_topicName = mysqli_fetch_assoc($result_topicId);
      //echo "$rowNum_name";
      //echo "$rowNum_qq";
      //echo "$rowNum_topicId";
      //echo <"/br">;
      //echo "$rowNum_qq";
      //echo "123";
      //echo <"/br">;
      //return;
      if($rowNum_name > 0 && $rowNum_qq >0 && $rowNum_topicId > 0)
      {
         //echo "----";
         //return;
         $topicname = $row_topicName["name"];
         $username1 = $login_name;
         $qq1 = $QQ;
         $username2 = "";
         $qq2 = "";
         $create_time = date("Y-m-d H:i:s");
         $upload_time = NULL;
         $counter = 0;
         $status = 10;
         $str_insert = "insert into chatlogs(topic,username1,qq1,username2,qq2,create_time,"
            ."upload_time,counter,status) values('$topicname','$username1','$qq1','$username2','$qq2','$create_time',"
            ."'','$counter','$status')";
         //echo "$str_insert";
         //echo "$topicName";
         //return;
         if(mysqli_query($link,$str_insert))
         {
            //echo "123";;
            //若插入成功，则获取当前插入id
            $str_query_id = "select last_insert_id()";
            $result_id = mysqli_query($link,$str_query_id);
            if($result_id)
            {
               //echo "456";
               $row_number_id = mysqli_num_rows($result_id);
               //echo "$row_number_id";
               if($row_number_id > 0)
               {
                 // echo "789";
                  $row_id = mysqli_fetch_assoc($result_id);
                  //print_r($row_id);
                  $chatId = $row_id["last_insert_id()"];
                  //echo "$chatId";
               }
            }
            //  return;
         }//Insert若不成功？
         else{}//TODO
      }//审核用户失败，怎么办？
      else{}//TODO
   }

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
      <title>竹间</title>
      <!-- BEG_ORISBOT_NOINDEX -->
      <!-- Billy 2012/2/3 -->
      <Script Language=JavaScript>
         function lockFunction(obj, n)
         {
            if (g_defaultExtremeType[n] == 1)
               obj.checked = true;
         } 

         function click_logout()  //log out
         {
            document.getElementsByName("logoutform")[0].submit();
         }

         function loaded(){}

         //***Step12 修改页面点击保存按钮出发Ajax动作
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
         <div class="wraper container-fluid">
            <div class="page-title"> 
               <h3 class="title"><?php echo $TitleStr; ?></h3> 
            </div>
            <!-- Basic Form Wizard -->
            <div class="row">
               <div class="col-md-12">
                  <div class="panel panel-default">
                     <div class="panel-body">
                        <table class="searchField" border="0" cellspacing="0" cellpadding="0">
                           <tr>
                              <th>对话编号：</th>
                              <td><Input type=text name=strQModify readonly="true" || disabled="true" size=50 value="<?php echo "$chatId";?>"></td>
                           </tr>
                        </table>
                     </div>  <!-- End panel-body -->
                  </div> <!-- End panel -->
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
      <!-- Main Content Ends -->
      <script type="text/javascript" src="../lib/jquery.easyui.min.js"></script>
      <script type="text/javascript" src="../lib/jquery.min.js"></script>
      <script type="text/javascript" src="../lib/jquery-ui.min.js"></script>
      <script type="text/javascript" src="../js/OSC_layout.js"></script>
   </body>
</html>
<!--Step15 新增修改页面    结束 -->
