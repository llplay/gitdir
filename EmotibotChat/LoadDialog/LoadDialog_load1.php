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
   
   session_start();
   if ($_SESSION["QQ"] == "" || $_SESSION["username"] == "")
   {
      session_write_close();
      sleep(DELAY_SEC);
      // header("Location:". $web_path . "main.php?cmd=err");
      $return_string = "<div id=\"sResultTitle\" class=\"sResultTitle\">Session 已经过期，请重新登录！</div>";
      echo $return_string;
      exit();
   }
   $QQ = $_SESSION["QQ"];
   $login_name = $_SESSION["username"];
   // $login_name = "Phantom";
   // $user_id = 1;
   $current_func_name = "iSearch";
   session_write_close();
   
   header('Content-Type:text/html;charset=utf-8');
   
   //define
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("TIME_ZONE", "Asia/Shanghai");
   define("ILLEGAL_CHAR", "'-;<>");                          //illegal char
   define("STR_LENGTH", 50);
   define("SEARCH_SIZE", 1000);                             //上限1000笔数
   define("PAGE_SIZE", 100);                                //设置列表显示笔数

   //return value
   define("SUCCESS", 0);
   define("DB_ERROR", -1);
   define("SYMBOL_ERROR", -3);
   define("SYMBOL_ERROR_CMD", -4);
   define("MAPPING_ERROR", -5);
   define("DIALOGCONTENT_ERROR",-6);

   //timezone
   date_default_timezone_set(TIME_ZONE);
   //----- Check command -----
   function check_command($check_str)
   {
      if(strcmp($check_str, "searchLoadDialog"))
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   //----- Check name -----
   function check_name($check_str)
   {
      //----- check str length -----
      if(strlen($check_str) > STR_LENGTH)
      {
         return SYMBOL_ERROR;
      }       
      //----- replace "<" to "&lt" -----
      if(strpbrk($check_str, "<") == true)
      {
         $check_str = str_replace("<", "&lt;", $check_str);
      }
      //----- replace ">" to "&gt" -----
      if(strpbrk($check_str, ">") == true)
      {
         $check_str = str_replace(">", "&gt;", $check_str);
      }
      return $check_str;
   }
   //----- Check time range begin -----
   function check_range_begin($check_str)
   {
      //----- check illegal char -----
      if(strpbrk($check_str, ILLEGAL_CHAR) == true)
      {
         return SYMBOL_ERROR;
      }
      //----- check empty string -----
      if(trim($check_str) == "")
      {
         return $check_str;
      }
      //----- format begin range mm/dd/yy to yyyy-mm-dd 00:00:00 -----
      date_default_timezone_set(TIME_ZONE);
      $check_str = $check_str . " 00:00:00";
      if(($check_str = strtotime($check_str)) == "")
      {
         //----- str to time failure -----
         return SYMBOL_ERROR;
      }
      $check_str = date("Y-m-d H:i:s", $check_str);
      return $check_str; 
   }
   //----- Check report range end -----
   function check_range_end($check_str)
   {
      //----- check illegal char -----
      if(strpbrk($check_str, ILLEGAL_CHAR) == true)
      {
         return SYMBOL_ERROR;
      }
      //----- check empty string -----
      if(trim($check_str) == "")
      {
         return $check_str;
      }
      //----- format end range mm/dd/yy to yyyy-mm-dd 23:59:59 -----
      date_default_timezone_set(TIME_ZONE);
      $check_str = $check_str . " 23:59:59";

      if(($check_str = strtotime($check_str)) == "")
      {
         //----- str to time failure -----
         return SYMBOL_ERROR;
      }
      $check_str = date("Y-m-d H:i:s", $check_str);
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
   //----- Check encrypt setting -----
  