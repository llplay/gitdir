<?php
   $mysqlconnect = "/var/www/html/Emotibot/DB.conf";
   define("FILE_NAME", $mysqlconnect);
   define("DELAY_SEC", 3);
   define("FILE_ERROR", -2);
            
   if (file_exists(FILE_NAME)) {
      include(FILE_NAME);
   }
   else {
      sleep(DELAY_SEC);
      echo FILE_ERROR;
      return;
   }
                                                   
   header('Content-Type:text/json;charset=utf-8');
                                                         
   //define
   define("DB_HOST", $db_host);
   define("ADMIN_ACCOUNT", $admin_account);
   define("ADMIN_PASSWORD", $admin_password);
   define("CONNECT_DB", $connect_db);
   define("ILLEGAL_CHAR", "'-;<>");                       //illegal char
   define("STR_LENGTH", 50);
   define("SEARCH_SIZE", 10);                             //上限10笔数

   define("SUCCESS", 0);
   define("DB_ERROR", -2);
   define("SYMBOL_ERROR", -1); 
   
   define("TIME_ZONE", "Asia/Shanghai");
   date_default_timezone_set(TIME_ZONE);
   
   //----- Check command -----
   function check_command($check_str, $str) {
      if(strcmp($check_str, $str))
         return SYMBOL_ERROR;
      return $check_str;
   }
   
   //----- 错误信息回复 -----
   function return_message($ret,$msg,$delay) {
      if ($delay > 0)
         sleep($delay);
      echo json_encode(array("return" => $ret, "message" => $msg), JSON_UNESCAPED_UNICODE);
   }
   
   header('Content-Type:application/json;charset=utf-8');
   
   $link;
   $str_query;
   $result;                 //query result
   $row;                   
   $return_string;
   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB); 
   mysqli_set_charset ($link,"utf8");   
   if (!$link) {  //connect to server failure    
      return_message(SYMBOL_ERROR,"数据库异常".__LINE__,DELAY_SEC);
      return;
   }
?>
