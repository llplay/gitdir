<HTML>
   <HEAD>
   </HEAD>
   <BODY>
<?php
//modify:lingduo 6.6 qq对话格式没有测试Windows平台，发现了其他格式，需要对IsDialog进行补充，补充情况QQ号 Y/M/D H:M:
//
//
//
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
   $Status;
   $EditTime;

   //query
   $link;
   $str_query;
   $str_update;
   $result;                 //query result
   $row;                    //1 data array
   $return_string;
   $qq1;
   $qq2 = "$QQ";
   $username1;
   $username2 = "$login_name";
   $chatContent;
   $dialogCount = 0;
   $currentUserName = "";
   //1.get information from client
   $chatId = $_POST["chatId"];
   if (strlen($chatId) > 0 && check_number($chatId) == SYMBOL_ERROR)
   {
      sleep(DELAY_SEC);
      echo SYMBOL_ERROR;
      return;
   }
   $chatContent = $_POST["chatContent"];
   $chatMethod = $_POST["dialogMethod"];
 
   echo "---$chatId---";
   echo "</br>";
   echo "---$chatContent---";
  // $contentArr = explode("\n", $chatContent);
  // echo "---$contentArr[0]---";
   echo "</br>";
   echo "---$chatMethod---";
   return;
   //link    
   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
   mysqli_set_charset ($link,"utf8");
   if (!$link)  //connect to server failure    
   {
      sleep(DELAY_SEC);
      echo DB_ERROR;       
      return;
   }   
  // echo "---$chatId---";
  // echo "---$chatContent---";
   //读取qq1 和 username1

   $str_query_qq1 = "select qq1,username1 from chatlogs where id = '$chatId'";
   $result_qq1 = mysqli_query($link, $str_query_qq1);
  // echo "---$str_query_qq1---";

   if($result_qq1)
   {
      
      $rowNum_qq1 = mysqli_num_rows($result_qq1);
     // echo "$rowNum_qq1";
      if($rowNum_qq1 > 0)
      {
         $row_qq1 = mysqli_fetch_assoc($result_qq1);
         $qq1 = $row_qq1["qq1"];
         $username1 = $row_qq1["username1"];
      }
      else
      {
         echo "不存在该会话，请检查对话编号是否错误" . __LINE__;
         return;
      }
   }
   else
   {
      echo "不存在该会话，请检查对话编号是否错误 " . __LINE__;
      return;//anyting else?
   }
   //IsDialog 实现
   //需要应对两种格式 “QQ号空格空格H:M:S” 和“QQ号空格Y/M/D空格H:M:S"
   //新增应对格式“QQ+空格+TH:M:S”
   function IsDialog($strLine)
   {
      global $currentUserName;
      global $qq1;
      global $qq2;
      global $username1;
      global $username2;
      $splitArr = explode("  ",$strLine);
      if(count($splitArr) == 2)
      {
        if($splitArr[0] == $qq1 || $splitArr[0] == $qq2)
        {
           $pattern = "/^((0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/";
           if(preg_match($pattern, $splitArr[1]))
           {
              if($splitArr[0] == $qq1) 
                 $currentUserName = $username1;
              else 
                 $currentUserName = $username2;
              return false;
           }
           else 
              return true;
        }
      }
      $splitArr = explode(" ",$strLine);
      if(count($splitArr) == 2)
      {
        if($splitArr[0] == $qq1 || $splitArr[0] == $qq2)
        {
           $pattern = "/^((0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/";
           if(preg_match($pattern, $splitArr[1]))
           {
              if($splitArr[0] == $qq1) 
                 $currentUserName = $username1;
              else 
                 $currentUserName = $username2;
              return false;
           }
           else 
              return true;
        }
      }
      $splitArr = explode(" ",$strLine);
      if(count($splitArr) == 3)
      {
        //echo "--$splitArr[0]--$qq2--";
        //echo "</br>";

        if($splitArr[0] == $qq1 || $splitArr[0] == $qq2)
        {
           $pattern1 = "/^\d{4}\/([1-9]|1[012])\/([1-9]|[12][0-9]|3[01])$/";
           $pattern2 = "/^((0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/";
           
           //echo "--$splitArr[1]--$splitArr[2]--";
           //echo "</br>";
           if(preg_match($pattern1, $splitArr[1]) && preg_match($pattern2, $splitArr[2]))
           {
              
           //echo "--$splitArr[1]--";
           //echo "</br>";
              if($splitArr[0] == $qq1) 
                 $currentUserName = $username1;
              else 
                 $currentUserName = $username2;
              return false;
           }
           else 
              return true;
        }
      }
      return true;
   }


   //对chatlogs表进行更新,首先判断当前对话chatlogs下status是否是11或者0 ，若不是，说明当前对话不可上传
   $status;//
   $str_query_status = "select status from chatlogs where id = '$chatId'";
   $result_status = mysqli_query($link,$str_query_status);
   if($result_status)
   {
      $rowNumber = mysqli_num_rows($result_status);
      if($rowNumber > 0)
      {
         $row_status = mysqli_fetch_assoc($result_status);
         $status = $row_status["status"];

         if($status == 10)
         {
            echo "--单号"."$chatId"."还未有人接单，请检查是否填写错误--";
            return;
         }
         if($status != 0 && $status != 11)
         {
            echo "--单号"."$chatId"."错误或已经审核，不可以再次上传--";
            return;
         }
      }
      else
      {
         echo "--数据库操作错误--";
         return;
      }
   }
   else
   {
        echo "--数据库操作错误--";
       return;
   }

   $contentArr = explode("\n", $chatContent);
   foreach($contentArr as $strLine)
   {

      $pattern = "/^((0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/";
      $strLine = trim($strLine);
      if($strLine == "" || preg_match($pattern, $strLine))
         continue;
      else if(IsDialog($strLine))
      {
         if($currentUserName == "")
            continue;
         else
         {
            $strLine = str_replace("'","\'",$strLine);
            $str_insert = "insert into chatdetails(chatid,username,content,status) values"
               ."('$chatId','$currentUserName','$strLine',1)";
            $result_insert = mysqli_query($link,$str_insert);
            if($result_insert)
               $dialogCount++;
            else
            {
               echo "数据库错误，保存失败 " . __LINE__;
               return;
            }
         }
      }
   }
   //更新chatlogs 
   $upload_time = date("Y-m-d H:i:s");
   $str_update = "update chatlogs set upload_time = '$upload_time',status = 0, counter = counter + '$dialogCount' where id = '$chatId'";
   $result_update = mysqli_query($link,$str_update);
   if($result_update)
   {}//TODO
   else
   {echo "更新失败 " . __LINE__;return;}//TODO
   echo "保存成功，对话编号：$chatId,对话计数：$dialogCount";
   //关闭数据库
   if($link)
      mysqli_close($link);
?>
   </BODY>
</HTML>
