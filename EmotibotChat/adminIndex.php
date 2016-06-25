<?php
   define("FILE_NAME", "./DB.conf");
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
   
   // TODO: 从 Session 里面拿到 login_name + user_id
   session_start();
   if ($_SESSION["QQ"] == "" || $_SESSION["username"] == "")
   {
      session_write_close();
      sleep(DELAY_SEC);
      header("Location:adminMain.php?cmd=main");
      exit();
   }
   $login_name = $_SESSION["username"];
   $user_qq = $_SESSION["QQ"];
   $current_func_name = "iSearch";
   session_write_close();
   
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("TIME_ZONE", "Asia/Shanghai");
   define("PAGE_SIZE", 100);

   define("AVAILABLE", 0);
   define("TRIAL", 0);
   define("DB_ERROR", -1);

   define("MSG_REPORT_1", "目前沒有任何報表，請點選&quot;<a>產生新的報表</a>&quot;");

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
 
   date_default_timezone_set(TIME_ZONE);  //set timezone

   //----- Connect to MySql -----
   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);    
   mysqli_set_charset ($link,"utf8");
   $f;
   $y = date("Y");
   $m = date("m");
   $d = date("d");
   $dirpath = "log/$y/$m/$d";
   if(!is_dir($dirpath)) mkdir($dirpath, 0777, true);
   if(!file_exists("$dirpath/DBLinkLog.txt")){
      $f = fopen("$dirpath/DBLinkLog.txt", "w"); 
   }
   else{
      $f = fopen("$dirpath/DBLinkLog.txt", "a");
   }
   
   if(mysqli_connect_errno($link)){
      //die('数据库连接失败: '. mysqli_connect_error());
      fwrite($f, date("Y/m/d H:i:s") . " " . mysqli_connect_error() . "\n");
      fclose($f);
      return;
   }
   else{
      fwrite($f, date("Y/m/d H:i:s") . " " . "数据库连接成功！" . "\n");
      fclose($f);
   }
   
   if (!$link)  //connect to server failure   
   {   
      sleep(DELAY_SEC);
      echo DB_ERROR;                
      return;
   }
?>

<!DOCTYPE html>
<html lang="zh-CN">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
      <link type="image/x-icon" href="./images/logo.ico" rel="shortcut icon">

      <link rel="stylesheet" type="text/css" href="lib/yui-cssreset-min.css">
      <link rel="stylesheet" type="text/css" href="lib/yui-cssfonts-min.css">
      <link rel="stylesheet" type="text/css" href="css/OSC_layout.css">

      <!--[if lt IE 10]>
      <script type="text/javascript" src="lib/PIE.js"></script>
      <![endif]-->
      <!-- Bootstrap core CSS -->
      <link href="newui/css/bootstrap.min.css" rel="stylesheet">
      <link href="newui/css/bootstrap-reset.css" rel="stylesheet">

      <!--Animation css-->
      <link href="newui/css/animate.css" rel="stylesheet">

      <!--Icon-fonts css-->
      <link href="newui/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
      <link href="newui/assets/ionicon/css/ionicons.min.css" rel="stylesheet" />

      <!-- Plugins css-->
      <link rel="stylesheet" type="text/css" href="newui/assets/jquery-multi-select/multi-select.css" />
      <link rel="stylesheet" type="text/css" href="newui/assets/select2/select2.css" />

      <!--Morris Chart CSS -->
      <link rel="stylesheet" href="newui/assets/morris/morris.css">

      <!-- sweet alerts -->
      <link href="newui/assets/sweet-alert/sweet-alert.min.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="newui/css/style.css" rel="stylesheet">
      <link href="newui/css/helper.css" rel="stylesheet">
      <link href="newui/css/style-responsive.css" rel="stylesheet" />
      <link type="text/css" href="lib/jQueryDatePicker/jquery-ui.custom.css" rel="stylesheet" />

      <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
      <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
      <![endif]-->
      <title>竹间</title>
      <!-- BEG_ORISBOT_NOINDEX -->
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

      </Script>
      <style>
         #enterpiceReport .mainContent {
            background-color: #fff;
            border: 0px solid #bbb;
         }
      </style>
   </head>
   <body Onload="">
      <div id="loadingWrap" class="nodlgclose loading" style="display:none;">
         <div id="loadingContent">
            <span id="loadingContentInner">
               <span id="loadingIcon"></span><span id="loadingText">读取中，请稍后...</span>
            </span>
         </div>
      </div>
      <!-- Aside Start-->
      <aside class="left-panel">
         <!-- brand -->
         <div class="logo">
            <a href="index.php" class="logo-expanded">
               <img src="images/logo.jpg" height=80 alt="logo">
               <span class="nav-label">竹间</span>
            </a>
         </div>
         <!-- / brand -->
         <!-- Navbar Start -->
            <nav class="navigation">
               <ul class="list-unstyled">
                  <li class="has-submenu active"><a href="#"><i class="ion-home"></i> <span class="nav-label">一般</span></a>
                     <ul class="list-unstyled mainTabW">                	
<?php
   echo "<li id=0 class='active'><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>QQ号管理</span></a></li>";
   echo "<li id=1><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>Topic管理</span></a></li>";
   echo "<li id=2><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>用户管理</span></a></li>";
   echo "<li id=3><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>对话审核</span></a></li>";
   echo "<li id=4><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>Excel上传</span></a></li>";
   echo "<li id=5><a href='javascript:void(0)'><span class='tabIcon QQ'></span><span class='nav-label'>批量删除</span></a></li>";
?>
                     </ul>
                  </li>
               </ul>
            </nav>
         </aside>
         <!-- Aside Ends-->
         <!--Main Content Start -->
         <section class="content">
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
                  <form name=logoutform action=logout.php></form>
                  <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                     <i class="fa fa-user"></i>
                     <span class="username"><?php echo $login_name ?> </span> <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu extended pro-menu fadeInUp animated" tabindex="5003" style="overflow: hidden; outline: none;">
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
         <div class="wraper container-fluid" id="enterpiceReport">
            <div class="mainContent">
      	
<?php
   echo "<div class='container2 searchNewsC' style='display:block;'>";
   include("QQNumbers/QQNumbers_list.php");
   echo "</div>";

   echo "<div class='container2 searchNewsC' style='display:none;'>";
   include("Topics/Topics_list.php");
   echo "</div>";

   echo "<div class='container2 searchNewsC' style='display:none;'>";
   include("Users/Users_list.php");
   echo "</div>";

   echo "<div class='container2 searchNewsC 1' style='display:none;'>";
   include("ChatReview/ChatReview_list.php");
   echo "</div>";

   echo "<div class='container2 searchNewsC 1' style='display:none;'>";
   include("CsvUpload/CsvUpload_list.php");
   echo "</div>";

   echo "<div class='container2 searchNewsC' style='display:none;'>";
   include("DelRecord/DelRecord_list.php");
   echo "</div>";
?>
            </div>
         </div>
         <!-- Page Content Ends -->
         <!-- ================== -->
         <!-- Footer Start -->
         <footer class="footer">
            2016 © 竹间.
         </footer>
         <!-- Footer Ends -->
      </section>
      <!-- Main Content Ends -->
      <!-- js placed at the end of the document so the pages load faster -->
      <script src="newui/js/jquery.js"></script>
      <script src="newui/js/bootstrap.min.js"></script>
      <script src="newui/js/modernizr.min.js"></script>
      <!-- <script src="newui/js/pace.min.js"></script> -->
      <script src="newui/js/wow.min.js"></script>
      <script src="newui/js/jquery.scrollTo.min.js"></script>
      <script src="newui/js/jquery.nicescroll.js" type="text/javascript"></script>
      <script src="newui/assets/chat/moment-2.2.1.js"></script>
      <!-- Counter-up -->
      <script src="newui/js/waypoints.min.js" type="text/javascript"></script>
      <script src="newui/js/jquery.counterup.min.js" type="text/javascript"></script>

      <!-- sparkline --> 
      <script src="newui/assets/sparkline-chart/jquery.sparkline.min.js" type="text/javascript"></script>
      <script src="newui/assets/sparkline-chart/chart-sparkline.js" type="text/javascript"></script> 

      <!-- sweet alerts -->
      <script src="newui/assets/sweet-alert/sweet-alert.min.js"></script>
      <script src="newui/assets/sweet-alert/sweet-alert.init.js"></script>

      <script src="newui/js/jquery.app.js"></script>
      <!-- Chat -->
      <script src="newui/js/jquery.chat.js"></script>

      <!-- Todo -->
      <script src="newui/js/jquery.todo.js"></script>


      <script type="text/javascript">
         /* ==============================================
            Counter Up
         =============================================== */
         jQuery(document).ready(function($) {
            $('.counter').counterUp({
               delay: 100,
               time: 1200
            });
         });
      </script>
      <!--
      <script type="text/javascript" src="openflashchart/js/swfobject.js"></script>
      <script type="text/javascript" src="openflashchart/js/json/json2.js"></script>
      -->    

      <script type="text/javascript" src="lib/jquery.min.js"></script>
      <script type="text/javascript" src="lib/jquery-ui.min.js"></script>
      <script type="text/javascript" src="js/OSC_layout.js"></script>
      <script type="text/javascript" src="js/css3pie.js"></script>
      <script type="text/javascript" src="js/WutianFunction.js"></script>
      <script type="text/javascript" src="newui/assets/jquery-multi-select/jquery.multi-select.js"></script>
      <script src="newui/assets/select2/select2.min.js" type="text/javascript"></script>

      <script>
         jQuery(document).ready(function() {
            // Select2
            jQuery(".select2").select2({
               width: '100%'
            });
         });
      </script>
   </body>
</html>
