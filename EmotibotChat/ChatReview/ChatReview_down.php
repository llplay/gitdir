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
  // if ($_SESSION["GUID"] == "" || $_SESSION["username"] == "")
  // {
  //    session_write_close();
  //    sleep(DELAY_SEC);
      //header("Location:". $web_path . "main.php?cmd=err");
  //    $return_string = "<div id=\"sResultTitle\" class=\"sResultTitle\">Session 已经过期，请重新登录！</div>";
  //    echo $return_string;
  //    exit();
 //  }
 //  $user_id = $_SESSION["GUID"];
 //  $login_name = $_SESSION["username"];
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
   define("DOWNLOAD", "upload");
   define("TIME_ZONE", "Asia/Shanghai");
   define("ILLEGAL_CHAR", "'-;<>");                         //illegal char

   //return value
   define("SUCCESS", 0);
   define("DB_ERROR", -1);
   define("SYMBOL_ERROR", -3);
   define("SYMBOL_ERROR_CMD", -4);
   define("MAPPING_ERROR", -5);
   define("STR_LENGTH", 50);
   
   //timezone
   date_default_timezone_set(TIME_ZONE);
   
   //----- Check command -----
   function check_command($check_str)
   {
      if(strcmp($check_str, "downC"))
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   function check_name($check_str)
   {
      //----- check str length -----
      if(mb_strlen($check_str, "utf8") > STR_LENGTH)
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
   $CategoryId;

   //query
   $link;
   
   function export_csv($filename,$filepath) { 
      Header("Content-type: application/octet-stream");
      Header("Accept-Ranges: bytes");
      Header("Accept-Length: " . filesize($filepath));
      Header("Content-Disposition: attachment; filename=" . $filename);
      // 输出文件内容
      //echo fread($file,filesize($file_dir . $file_name));
      $file = fopen($filepath,"r"); // 打开文件
      while(1) {
         $str = fread($file,1024);
         echo $str;
         if (strlen($str) < 1024)
            break;
      }
      fclose($file);
      exit(); 
      //echo $data; 
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
   
   //1.get information from client 
   if(($cmd = check_command($_GET["cmd"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR_CMD;
      return;
   }
   if(($searchChatUsername = check_name($_GET["searchChatUsername"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR;
      return;
   }
   //(($statusCheckbox = check_number($_GET["statusCheckbox"])) == SYMBOL_ERROR)
  //
  //  sleep(DELAY_SEC);
  //  echo SYMBOL_ERROR;
  //  return;
  //
   if(($searchReviewfrom = check_range_begin($_GET["searchReviewfrom"])) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR;
      return;
   }
   if(($searchReviewto = check_range_end($_GET["searchReviewto"])) == SYMBOL_ERROR)
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
    
   //----- query -----
   //***Step18 上下架动作修改SQL语句
   $str_query1 = "select * from chatlogs where status";
   $str_query2 = "select sum(counter) as counter  from chatlogs where status";
   if (is_numeric($statusCheckbox))
   {
       $str_query1 = $str_query1 . "=$statusCheckbox ";
       $str_query2 = $str_query2 . "=$statusCheckbox ";
   }
   else
   {
       $str_query1 = $str_query1 . ">=-1 AND status <=3 ";
       $str_query2 = $str_query2 . ">=-1 AND status <=3 ";
   }
   if (strlen($searchChatUsername) > 0)
   {
       $str_query1 = $str_query1 . "AND (username1 like '%$searchChatUsername%' OR username2 like '%$searchChatUsername%') ";
       $str_query2 = $str_query2 . "AND (username1 like '%$searchChatUsername%' OR username2 like '%$searchChatUsername%') ";
   }
   if (strlen($searchReviewUsername) > 0)
   {
        $str_query1 = $str_query1 . "AND (username1 like '%$searchChatUsername%' OR username2 like '%$searchChatUsername%') ";
       $str_query2 = $str_query2 . "AND (username1 like '%$searchChatUsername%' OR username2 like '%$searchChatUsername%') ";
   }
  
   if (strlen($searchReviewfrom) > 0)
   {
         $str_query1 = $str_query1 . "AND (upload_time >= '$searchReviewfrom' ) ";
         $str_query2 = $str_query2 . "AND (upload_time >= '$searchReviewfrom' ) ";
   }
   if (strlen($searchReviewto) > 0)
   {
        $str_query1 = $str_query1 . "AND (upload_time <= '$searchReviewto' ) ";
        $str_query2 = $str_query2 . "AND (upload_time <= '$searchReviewto' ) ";
   }
  
   $str_query1 = $str_query1 . "Order by id DESC ";
   
   /////////////////////
   // prepare the SQL command and query DB
   /////////////////////
   $filename =date('YmdHis') . ".csv";//设置文件名 
   $filepath = "./upload/" . $filename;
   //$myfile = fopen($filepath,"a"); 
   $myfile = fopen($filepath, "w");
 
   $strHead = "对话编号,用户,小影,对话主题,对话轮数,上传日期,状态\n";
   // $strHead = iconv('utf-8','gb2312//IGNORE',$strHead);  
   $strdata = "\xEF\xBB\xBF" . $strHead;
   fwrite($myfile, $strdata);
   $result = mysqli_query($link, $str_query1);
   $result2 = mysqli_query($link, $str_query2);
   if($result && $result2)
   {      
      $row_number = mysqli_num_rows($result);
      while($row=mysqli_fetch_assoc($result))
      {
         $ChatId = $row["id"];
         $topic = $row["topic"];
         $username1 = $row["username1"];
         $username2 = $row["username2"];
         $upload_time = $row["upload_time"];
         $counter = $row["counter"];
         $reviewer = $row["reviewer"];
         $Status = $row["status"];
         if ($Status == 0)
              $StatusStr = "未审核";
         else if ($Status == 1)
             $StatusStr = "合格";
        else if ($Status == 2)
             $StatusStr = "不错";
        else if ($Status == 3)
             $StatusStr = "非常好";
        else if ($Status == -1)
             $StatusStr = "不合格";
         else
             $StatusStr = "未定义";  
         
         // $strtype = iconv('utf-8','gb2312//IGNORE',$strtype);
                  // $strdata = $strdata . "\"" . $topicid ."\"" . ",\"" . $name . "\",\"" . $value .  "\"," . $strstatus .  "," . $createtime . ",\"" . $strtype . "\"\n";
         $strdata = "\"" . $ChatId ."\"" . ",\"" . $username1 .  "\",\"" . $username2 .  "\",\"" . $topic . "\",\"" . $counter . "\",\"" . $upload_time . "\",\"" . $StatusStr . $reviewer . "\"\n";
         fwrite($myfile, $strdata);
      }
   }
   fclose($myfile);

   
   if($link){
      mysqli_close($link);
   }
   
   // $filename = "Category" . $user_id . date('Ymd') . ".csv";//设置文件名 
   // export_csv($filename,$strdata); //导出  
   export_csv($filename,$filepath); //导出 
   return;
?>
