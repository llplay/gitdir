<?php

//////////////////////////////////////////////
// #001  Phantom,Odie   2013-04-26  Add loginType parameter, 
//                                  if loginType=1 ==> Admin, will check customer table
//                                  if loginType=2 ==> User, will check userLogin table
//
// #002  Odie           2014-11-26  Add SQL escape to $login_name
   //----- Define -----
   // include_once("http.php");
   // include_once("wstrust.php");
   define("FILE_NAME", "./DB.conf"); //account file name
   define("DELAY_SEC", 3);                                       //delay reply
   define("FILE_ERROR", -3);
   //----- Read account and password from DB.conf -----
   
//   echo "$login_name";echo "---";return;
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
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("ILLEGAL_CHAR", "'-;<>");                          //illegal char
   define("TIME_ZONE", "Asia/Taipei");
   define("VCODE_LENGTH", 29);             
   //return value
   define("DB_ERROR", -1);       
   define("EMPTY_REMAIN", -2);   
   define("SYMBOL_ERROR", -3);
   define("SYMBOL_ERROR_GUID", -4);
   define("SYMBOL_ERROR_HOSTNAME", -5);
   //////////////////////
   // Input validation
   //////////////////////

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

   if(($login_name = check($_POST["login_name"])) == SYMBOL_ERROR)
   {
     sleep(DELAY_SEC);
     header("Location:adminMain.php?cmd=err");
     exit();
   }


   //echo "$login_name";echo "---";return;
   $qqnumber = $_POST["qqnumber"];
   $username = $login_name;

   //----- Read account and password from DB.conf -----
   if(file_exists(FILE_NAME))
   {
      include(FILE_NAME);
   }
   else
   {
      sleep(DELAY_SEC);
      header("Location:adminMain.php");
      exit();
   }

   //echo "$login_name";echo "---";return;
   //----- Connect to MySQL -----
   $link = mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
   mysqli_set_charset ($link,"utf8");
   if(!$link)
   {  //connect to server failure
      sleep(DELAY_SEC);
      header("Location:adminMain.php");
      exit();
   }
   //----- Query entryID by GUID, hostname, domainname -----

   $login_name = mysqli_real_escape_string($link, $login_name);   #002
   //echo "$login_name";
   //echo "$qqnumber"; 
   $str_query_user = "select username, status from users where status in (4,5,6,7) and username = '$login_name'";
   $str_query_qq = "select qq from qqnumbers where status=1 and qq = '$qqnumber'";
   $result_user = mysqli_query($link, $str_query_user);
   $result_qq = mysqli_query($link, $str_query_qq);
   if($result_user && $result_qq)
   {   //query success
      $rownum_user = mysqli_num_rows($result_user);
      $rownum_qq = mysqli_num_rows($result_qq);
      //echo "here<br/>";return;
      
      if ($rownum_qq > 0 && $rownum_user > 0)
      {
         session_start();
         $_SESSION["username"] = $login_name; //#001 Add
         $_SESSION["QQ"] = $qqnumber;
         session_write_close();
         //根据用户身份判断跳转到哪一个页面         
         header("Location:adminIndex.php");
         exit();
      }
      else 
      {
         if($link)
         {
            mysqli_close($link);
            $link = 0;   
         }
      }
   }
   else
   {
      if($link)
      {
         mysqli_close($link);
         $link = 0;   
      }
      sleep(DELAY_SEC);
      header("Location:adminMain.php");
      exit();
   }
    
   //////////////////////
   // If failed, set session=empty, redirect to main.php
   //////////////////////
   session_start();
   $_SESSION["QQ"] = ""; //#001 Add
   $_SESSION["username"] = ""; //#001 Add
   session_write_close();
   sleep(DELAY_SEC);
   header("Location:adminMain.php?cmd=err");
   exit();
?>
