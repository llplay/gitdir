<?php

   require_once("../PHPExcel/Classes/PHPExcel.php");
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
  // if ($_SESSION["QQ"] == "" || $_SESSION["username"] == "")
 //  {
 //     session_write_close();
  //    sleep(DELAY_SEC);
 //     // header("Location:". $web_path . "main.php?cmd=err");
 //     $return_string = "<div id=\"sResultTitle\" class=\"sResultTitle\">Session 已经过期，请重新登录！</div>";
 //     echo $return_string;
 //     exit();
 //  }
 //  $QQ = $_SESSION["QQ"];
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

   define("FileFormat_Error",-6);
   define("FileUpload_Error",-7);
   define("AnalysisFile_Error",-8);
   define("DatabaseOperator_Error",-9);
   //timezone
   date_default_timezone_set(TIME_ZONE);
   //----- Check command -----
   function check_command($check_str)
   {
      if(strcmp($check_str, "searchExcelUpload"))
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
   function check_encrypt($check_str)
   {
      if(!is_numeric($check_str))
      {
         return SYMBOL_ERROR; 
      }
      if($check_str != 0 && $check_str != 1)
      {
         return SYMBOL_ERROR;
      }
      return $check_str;
   }
   //get data from client
   $cmd;
   $exceluploadName;
   $exceluploadCode;
   $Status;
   $EditTime;

   //query
   $link;
   $str_query;
   $str_update;
   $result;                 //query result
   $row;                    //1 data array
   $return_string;
   //1.get information from client 
//   $excelPath=="./test.xlsx"// $_POST["excelPath"];
//   if (strlen($chatId) > 0 )
//   {
//        sleep(DELAY_SEC);
//        echo SYMBOL_ERRORg
   //        return;
//   }

   //上传文件路径
   $target_dir = "uploads/";
   $file_type = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);//csv
   //上传后新文件名称
   $target_file = $target_dir.time().hash('md5', $_FILES["fileToUpload"]["name"]).".$file_type";
 //  $file_status = new UploadFileStatus();
 //  $file_status->status = UPLOAD_SUCCESS; 

 
   //判断是否是空文件
   if ($_FILES['fileToUpload']['size'] == 0)
   {
//      $file_status->status = ERR_EMPTY_FILE;
//      array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_EMPTY_FILE));//      goto err_exit;
      return FileFormat_Error;
   }
   
   
   //文件是否存在
   if (file_exists($target_file))
   {
//       $file_status->status = ERR_FILE_EXIST;
//       array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_FILE_EXIST));
//       goto err_exit;
         return FileFormat_Error;
   }
        
   
   
      //产生新文件
   if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
   {
//       $file_status->status = ERR_MOVE_FILE;
//       array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_MOVE_FILE));
//       goto err_exit;
         return FileFormat_Error;
   }
   


   if (($ret = read_excel_and_insert_into_database($target_file)) != SUCCESS)
   {
   //    $file_status->status = $ret;
   //    if ($ret == ERR_UPDATE_DATABASE)
   //    {
   //        array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_UPDATE_DATA    BASE));
   //    }
   //    else if ($ret == ERR_INSERT_DATABASE)
   //    {
   //        array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>MSG_ERR_INSERT_DATA    BASE));
   //    }
   }

   function is_empty_row($row)
   {
       foreach ($row as $element)
       {
          if (strlen($element) > 0)
          {
              return false;
          }
       }
       return true;
    }


   function read_excel_and_insert_into_database($target_file)
   {
      try
      {
         $input_file_type = PHPExcel_IOFactory::identify($target_file);//csv
         $reader = PHPExcel_IOFactory::createReader($input_file_type);
         $excel = $reader->load($target_file);
         //print_r($excel);
      }
      catch (Exception $e)
      {
      //   $file_status->status = ERR_FILE_LOAD;
      //   array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>$e->getMessage()));
         //   return $file_status->status;
            return AlysisFile_Error;
      }

      // parse file
      $sheet_count = $excel->getSheetCount();
      $topic="";
      $topicid=-1;
      for ($cur_sheet=0; $cur_sheet < $sheet_count; $cur_sheet++)//
      {
          $sheet = $excel->getSheet($cur_sheet);
          $sheet_title = $sheet->getTitle();
         // print_r($sheet_title);
          $highest_row = $sheet->getHighestRow();//376            
          $highest_col = 2;//count($file_status->upload_user_syntax);

          for ($row=0; $row<=4; $row++)//$highest_row
          {
             $tmp = array();
            // $functions = array();
             for ($col=0; $col<$highest_col; $col++)
             {
                array_push($tmp, trim($sheet->getCellByColumnAndRow($col, $row)->getValue()));
             }
             print_r($tmp[0]);
             echo "111";
             print_r($tmp[1]);
             echo "222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222
