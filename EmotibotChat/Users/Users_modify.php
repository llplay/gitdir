<?php

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
         header("Location:". $web_path . "adminMain.php?cmd=err");
         exit();
      }
   }
   catch(exception $ex)
   {
      session_write_close();
      sleep(DELAY_SEC);
      header("Location:". $web_path . "adminMain.php?cmd=err");
      exit();
   }
  

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
   function check($check_str)
   {
      //----- check illegal char -----
      if(strpbrk($check_str, ILLEGAL_CHAR) == true)
      {
         return SYMBOL_ERROR; 
      }
      //----- check empty string -----
      if(trim($check_str) == "")
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   function check_command($check_str)
   {
      if(strcmp($check_str, "read") && strcmp($check_str, "insert") && strcmp($check_str, "write"))
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
   
   //1.get information from client 
   if(($cmd = check_command($_GET["cmd"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR_CMD;
      return;
   }
   if(($UserId = check($_GET["UserId"])) == SYMBOL_ERROR)
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
   
   $datasyz = array();
   $datacpmc = array();
   class StuFunction{
      public $functionId;
      public $functionName;
      public $createdTime;
   }


   //----- query -----
   //***Step14 如果cmd为读取通过ID获取要修改内容信息，如果cmd不为读取并且ID为零为新增动作，如果不为读取和新增则为修改动作
   if ($cmd == "read") // Load
   {
      $str_select = "Select * from users where id='$UserId'";
      if($result = mysqli_query($link, $str_select))
      {
         $row_number = mysqli_num_rows($result);
         if ($row_number > 0)
         {
            $row = mysqli_fetch_assoc($result);
            $UserName= $row["username"];
            $UserPhone = $row["phone"];
            $UserWeChatId = $row["wechatid"];
            $UserStatus = $row["status"];
            $TitleStr = "用户信息修改";
         }
         else
         {
            $UserId = 0;
            $UserName = "";
            $UserPhone = "";
            $UserWeChatId="";
            $TitleStr = "用户信息";
            $UserStatus = 0;
         }
      }
   }
   else if ($cmd == "insert") // Insert
   {
      $UserName = $_GET["UserName"];
      $UserPhone = $_GET["UserPhone"];
      $UserWeChatId = $_GET["UserWeChatId"];
      $UserId = $_GET["UserId"];
      $UserStatus = $_GET["UserStatus"];
      $str_insert = "Insert into users (username,id,phone,wechatid,status)" 
                  . " VALUES('$UserName','$UserId','$UserPhone','$UserWeChatId',$UserStatus)" ;
      
      if(mysqli_query($link, $str_insert))
      {
         echo "0";
         return;
      }
      else
      {
         echo -__LINE__ . $str_insert;
         return;
      }
   }
   else // Update
   {
      $UserName = $_GET["UserName"];
      $UserPhone = $_GET["UserPhone"];
      $UserWeChatId = $_GET["UserWeChatId"];
      $UserStatus = $_GET["UserStatus"];

      $str_update = "Update users set username='$UserName', phone='$UserPhone', 
                     wechatid='$UserWeChatId', status=$UserStatus where id='$UserId'";
      if(mysqli_query($link, $str_update))
      {
         echo "0";
         return;
      }
      else
      {
         echo -__LINE__ . $str_update;
         return;
      }
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
         function modifyUsersContent()
         {

            UserName = document.getElementsByName("UserName")[0].value.trim();
            UserId = document.getElementsByName("UserId")[0].value.trim();
            UserPhone = document.getElementsByName("UserPhone")[0].value.trim();
            UserWeChatId = document.getElementsByName("UserWeChatId")[0].value.trim();
            len = document.getElementsByName("UserStatus").length;
            UserStatus = 0;
            for (i=0;i<len;i++)
            {
               if (document.getElementsByName("UserStatus")[i].checked)
                  UserStatus += parseInt(document.getElementsByName("UserStatus")[i].value);
            }
   
            if (UserName == 0 || UserId == 0)
            {
               alert("用户名、身份证号不可为空白");
               return;
            }
<?php
   if ($UserId == "0")
      echo "cmdstr='insert';";
   else
      echo "cmdstr='write';";
?>
            str = "cmd=" + cmdstr +
               "&UserId=" + encodeURIComponent(UserId) + 
               "&UserName=" + encodeURIComponent(UserName) + 
               "&UserPhone=" + encodeURIComponent(UserPhone) + 
               "&UserWeChatId=" + encodeURIComponent(UserWeChatId) + 
               "&UserStatus=" + encodeURIComponent(UserStatus);
            url_str = "../Users/Users_modify.php?";

            $.ajax
            ({
               beforeSend: function()
               {
                  //alert(str);
               },
               type: "GET",
               url: url_str + str,
               cache: false,
               success: function(res)
               {
                  //alert("Data Saved: " + res);
                  if (res.match(/^-\d+/))  //failed
                  {
                     alert("无法写入数据库,数据内容错误");
                  }
                  else  //success
                  {
                     alert("用户新增/修改成功，页面关闭后请自行刷新");
                     window.close();
                  }
               },
               error: function(xhr)
               {
                  alert("ajax error: " + xhr.status + " " + xhr.statusText);
               }
            });
         }
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
                              <th>身份证号：</th>
                              <td><Input type=text name=UserId size=50 value="<?php echo $UserId;?>" 
                                  <?php if($UserId <> 0) echo " readonly";?>
                                  ></td>
                           </tr>
                           <tr>
                              <th>用户名：</th>
                              <td><Input type=text name=UserName size=50 value="<?php echo $UserName;?>"></td>
                           </tr>
                           <tr>
                              <th>手机号：</th>
                              <td><Input type=text name=UserPhone size=20 value="<?php echo $UserPhone;?>"></td>
                           </tr>
                           <tr>
                              <th>微信号：</th>
                              <td><Input type=text name=UserWeChatId size=20 value="<?php echo $UserWeChatId;?>"></td>
                           </tr>
                           <tr>
                              <th>权限：</th>
                              <td>
                                 <Input type=checkbox name=UserStatus value=1
                                 <?php if ($UserStatus & 1) echo " checked"; ?>
                                 >用户
                                 <Input type=checkbox name=UserStatus value=2
                                 <?php if ($UserStatus & 2) echo " checked"; ?>
                                 >机器人
                                 <Input type=checkbox name=UserStatus value=4
                                 <?php if ($UserStatus & 4) echo " checked"; ?>
                                 >管理者
                              </td>
                           </tr>
                           <tr>
                              <th colspan="4" class="submitBtns">
                                 <a class="btn_submit_new modifyUsersContent"><input name="modifyUsersButton" type="button" value="保存" OnClick="modifyUsersContent()"></a>
                              </th>
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
