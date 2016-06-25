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
   $lastInsertId;
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
   
    $companyname=trim($_POST["inputCompanyName"]);
    if(strlen($companyname)<=0)
    {
        print_r("公司名称不为空");
        return ;
    }


   $link = @mysqli_connect(DB_HOST, ADMIN_ACCOUNT, ADMIN_PASSWORD, CONNECT_DB);
   mysqli_set_charset ($link,"utf8");
   if (!$link)  //connect to server failure    
   {
        sleep(DELAY_SEC);
       echo DB_ERROR;
       return;
   }

   $upload_time = date("Y-m-d H:i:s");
   $str_insert_query = "insert into insertdetails(company,status,upload_time) values ('$companyname',1,'$upload_time')";
   $result_insert_query = mysqli_query($link, $str_insert_query);
   $str_lastid_query = "select last_insert_id() as id";
   $result_lastid_query = mysqli_query($link, $str_lastid_query);
   if($result_insert_query && $result_lastid_query){

      $row_lastid = mysqli_fetch_assoc($result_lastid_query);
      $lastInsertId = $row_lastid["id"];
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
   }
   else{
      echo "--$str_insert_query--";
      echo "<br/>";
      echo "--$str_lastid_query--";
      echo "<br/>";
      echo "insert record error";
      return;
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
      global $companyname;
      $PHPReader = new PHPExcel_Reader_Excel2007();
      if(!($PHPReader->canRead($target_file)))
      {
           $PHPReader = new PHPExcel_Reader_Excel5();
           if(!$PHPReader->canRead($target_file))
           {
                 echo 'no Excel';
                 return ;
           }
      }
      $PHPExcel = $PHPReader->load($target_file);
      /**读取excel文件中的第一个工作表*/
      $currentSheet = $PHPExcel->getSheet(0);
      /**取得最大的列号*/
      $allColumn = $currentSheet->getHighestColumn();
      /**取得一共有多少行*/   
      $allRow = $currentSheet->getHighestRow();   
      /**从第二行开始输出，因为excel表中第一行为列名*/   
      $topic="";
      $chatid=-1;
      $currentcount=0;
      for($currentRow = 1;$currentRow <= $allRow;$currentRow++)
      {
         $tmp = array();
            /**从第A列开始输出*/   
           for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++)
           {    
            $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将
            字符转为十进制数*/   
            // echo $val."\t"; 
            array_push($tmp, trim($val));
           }  
         // echo "</br>";   
      // }   

      //try
     // {
        // $input_file_type = PHPExcel_IOFactory::identify($target_file);//csv
        // $reader = PHPExcel_IOFactory::createReader($input_file_type);
        // $excel = $reader->load($target_file);
         //print_r($excel);
     // }
     // catch (Exception $e)
     // {
      //   $file_status->status = ERR_FILE_LOAD;
      //   array_push($file_status->errors, array("sheet"=>0, "lines"=>0, "message"=>$e->getMessage()));
         //   return $file_status->status;
      //      return AlysisFile_Error;
     // }

      // parse file
    //  $sheet_count = $excel->getSheetCount();
    //  $topic="";
    //  $chatid=-1;
    //  $currentcount=0;
    //  for ($cur_sheet=0; $cur_sheet < $sheet_count; $cur_sheet++)//
    //  {
     //     $sheet = $excel->getSheet($cur_sheet);
     //     $sheet_title = $sheet->getTitle();
         // print_r($sheet_title);
    //      $highest_row = $sheet->getHighestRow();//376            
     //    $highest_col = $sheet->getHighestColumn();
       //   echo "rows",$highest_row;
       //   echo "cols",$highest_col;
       //   for ($row=0; $row<=$highest_row; $row++)//$highest_row
       //   {
       //      $tmp = array();
       //      for ($col=A; $col<=$highest_col; $col++)
        //     {
          //      array_push($tmp, trim($sheet->getCellByColumnAndRow($col, $row)->getValue()));
          //   }
       //  print_r("temp0",$tmp[0]);
       //print_r("temp1",$tmp[1]);

             $file = fopen($target_file,"r");
            // setlocale(LC_ALL,array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
             $data =fgetcsv($file);
            // $data = eval('return '.iconv('gbk','utf-8',var_export(fgetcsv($file),true)).';');         
            // print_r($data);
            // foreach ($data as $arr)
            // {
                // if ($arr[0]!="")
                // {
           //     $data1 = eval('return '.iconv('gbk','utf-8',var_export($arr[0],true)).';');
           //     echo $data1;
           //     echo iconv('utf-8','gb2312', $arr[0])."\t"; 
               //         $cc=iconv('gb2312','utf-8',$arr[0]);   
                 //     echo $cc;
                      //  echo eval('return '.iconv('gbk','utf-8',var_export($arr[0],true)).';')."<br>";
                      // echo eval('return '.iconv('gbk','utf-8',var_export($arr[1],true)).';')."<br>";
                 //  } 
            // }
             $tmp[0]=trim($tmp[0]);
             $tmp[1]=trim($tmp[1]);
             if(strlen($tmp[1])==0)
             {
                if(strlen($tmp[0])==0)
                {
                 //  echo "aaaaaaaaaaaaaaaaaaa";
                   //一个主题的对话结束
                  // print_r("chatid: "+ $chatid);
                  // print_r("currentcount: "+ $currentcount);
             
                   if($chatid!=-1 && $currentcount>0)
                   {
                      //更改数据库
                    //  echo "update database";
                      if(update_chatlogs_database($chatid,$currentcount)>0)
                      {
                         //更改成功
                      }
                      //    echo "update database end";
                      echo $topic,":成功了",$currentcount,"条数据","</br>";
                   }
                   
                   $topic="";
                   $chatid=-1;
                   $currentcount=0;

                }
                else
                {
                   //   echo "不空空";
                   //
                  if($chatid==-1 && $topic=="" && currentcount==0) 
                  {
                    $topic=$tmp[0];
                    $chatid=insert_chatlogs_database($topic);
                  }
                  else
                  {
                     if(insert_chatdetails_database($chatid,$companyname."用户",$tmp[0]))
                     {
                          //  echo "insert 用户 success";
                         $currentcount++;
                     }
                  }   
                
              //  echo $chatid;
                }
             }
             else if(strlen($tmp[0])==0)
             {
                if($chatid==-1 && $topic=="" && currentcount==0)
                {
                      $topic=$tmp[1];
                      $chatid=insert_chatlogs_database($topic);
                }
                else
                {
                   if(insert_chatdetails_database($chatid,$companyname."机器人",$tmp[1]))
                   {
                      $currentcount++;
                   }
                }
             }
             else if($tmp[0]=="用户" && $tmp[1]=="机器人扮演者")
             {
              //  echo "用户机器人";
                continue;
             }
             else
             {
                if($chatid>0)
                {
                 //  echo "detail insert...";
                   if(insert_chatdetails_database($chatid,$companyname."用户名",$tmp[0]))
                   {
                    //  echo "insert 用户 success";
                       $currentcount++;
                   }
                   if(insert_chatdetails_database($chatid,$companyname."机器人",$tmp[1]))
                   {
                    //  echo "insert 机器人扮演者 success";
                      $currentcount++;
                   }
                  // echo "detail insert end";
                }
             }
          }
          if($chatid!=-1 && $currentcount>0)
          {
                 //更改数据库
                // echo "update database";
                 if(update_chatlogs_database($chatid,$currentcount)>0)
                 {
                      //更改成功
                 }
                 echo $topic,":成功了",$currentcount,"条数据","</br>";
               //  echo "update database end";
           }
       }
   


   function insert_chatlogs_database($topicname)
   {
      global $companyname;
      global $lastInsertId;
      $topicname = $topicname;
      $username1 = $companyname."用户名";
      $qq1 = "";
      $username2 = $companyname."机器人";
      $qq2 = "";
      $create_time = date("Y-m-d H:i:s");
     
      $counter = 0;
      $status = 11;
    
      $str_insert = "insert into chatlogs(topic,username1,qq1,username2,qq2,create_time,counter,status,insertindex) values('$topicname','$username1','$qq1','$username2','$qq2','$create_time',$counter,$status,$lastInsertId)";
      //echo "$str_insert";
      //echo "$topicname";
      //return;
      global $link;
      $result_update =  mysqli_query($link, $str_insert);
     //$result_update;

      if($result_update)
      {
         //echo "123";
          //若插入成功，则获取当前插入id
           $str_query_id = "select last_insert_id()";
           $result_id = mysqli_query($link,$str_query_id);
           if($result_id)
           {
           //     echo "456";
                $row_number_id = mysqli_num_rows($result_id);
                //echo "$row_number_id";
                if($row_number_id > 0)
                {
             //       echo "789";
                    $row_id = mysqli_fetch_assoc($result_id);
                    //print_r($row_id);
                    $chatId = $row_id["last_insert_id()"];
                    //echo "$chatId";
                    return $chatId;
                }
           }    
      }
      else
      {
       if($link)
       {
            //  mysqli_close($link);
       }
       return DatabaseOperator_Error;
      }
   }


   function update_chatlogs_database($chatid,$counter)
   {
      $upload_time = date("Y-m-d H:i:s");
      $str_update = "Update chatlogs set status=0, counter=$counter,upload_time='$upload_time' where id=$chatid AND status=11";
    //  echo $str_update;
      global $link;
      $result_update =  mysqli_query($link, $str_update);
      if($result_update)
      {
          $rownum = mysqli_num_rows($result_query);
          if ($rownum > 0)
             return $rownum ;
          else
          {
             if($link)
             {
              //   mysqli_close($link);
             }
             return DatabaseOperator_Error;
          }
      }
   }

   function insert_chatdetails_database($chatid,$currentUserName,$strLine)
   {
      $str_insert = "insert into chatdetails(chatid,username,content,status) values ('$chatid','$currentUserName','$strLine',1)";
    //  echo $str_inset;
      global $link;
      $result_insert = mysqli_query($link,$str_insert);
      if($result_insert)
      {
         return 1;
      }
      else
      {
         if($link)
         {
           //  mysqli_close($link);
         }
         return DatabaseOperator_Error;
      }
   }

  ?>
