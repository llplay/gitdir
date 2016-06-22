<?php
   //----- Define -----
   define("FILE_NAME", "./DB.conf"); //account file name
   define("DELAY_SEC", 3);                                       //delay reply
   define("FILE_ERROR", -3);
   //----- Read account and password from DB.conf -----
   if(file_exists(FILE_NAME))
   {
      include(FILE_NAME);
   }
   else
   {
      sleep(DELAY_SEC);
      echo FILE_ERROR;

      return;
   }
   session_start();

   //////////////////////
   // Set session=empty, redirect to main.php
   //////////////////////
   $_SESSION["QQ"] = "";
   $cmd="";
   session_write_close();
   if(is_array($_GET)&&count($_GET)>0){   //判断是否有Get参数
      if(isset($_GET["cmd"])){
         $cmd = $_GET["cmd"];
         /* 20120522 Billy begin */
         if (strcmp($cmd , "err") != 0)
            $cmd = "";
         /* 20120522 Billy end */
      }
   }
?>

<!DOCTYPE HTML>
<html lang="zh-CN">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title></title>
      <link href="css/style.css" rel="stylesheet" type="text/css" media="all">
      <link type="image/x-icon" href="images/wutian.ico" rel="shortcut icon">
      <!--[if IE 6]> 
      <link href="css/style_ie6.css" rel="stylesheet" type="text/css" media="all">
      <![endif]-->
      <script type="text/JavaScript" src="js/demoOnly.js"></script>
      <script type="text/JavaScript" src="js/jquery-1.4.4.js"></script>
	  
      <!-- Bootstrap core CSS -->
      <link href="newui/css/bootstrap.min.css" rel="stylesheet">
      <link href="newui/css/bootstrap-reset.css" rel="stylesheet">

      <!--Animation css-->
      <link href="newui/css/animate.css" rel="stylesheet">

      <!--Icon-fonts css-->
      <link href="newui/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
      <link href="newui/assets/ionicon/css/ionicons.min.css" rel="stylesheet" />

      <!--Morris Chart CSS -->
      <link rel="newui/stylesheet" href="assets/morris/morris.css">


      <!-- Custom styles for this template -->
      <link href="newui/css/style.css" rel="stylesheet">
      <link href="newui/css/helper.css" rel="stylesheet">
      <link href="newui/css/style-responsive.css" rel="stylesheet" />

      <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
      <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
      <![endif]-->

   </head>
   <body>
      <div class="wrapper-page animated fadeInDown">
         <div class="panel panel-color panel-primary">
            <div class="panel-heading"> 
               <h3 class="text-center m-t-10"> 竹间对话系统 </strong> </h3>
            </div>
            <form name=myForm method=POST  class="form-horizontal m-t-40">                             
            <div class="form-group ">
               <div class="col-xs-12">
                  <A href=userMain.php>用户</A>
               </div>
            </div>
            <div class="form-group ">
               <div class="col-xs-12">
                  <A href=robotMain.php>机器人</A>
               </div>
            </div>
            <div class="form-group ">
               <div class="col-xs-12">
                  <A href=adminMain.php>管理者</A>
               </div>
            </div>
            </form>
         </div>
      </div>
   </body>
</html>
