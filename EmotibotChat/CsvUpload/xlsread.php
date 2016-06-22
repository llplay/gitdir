<?php
require_once '/var/www/html/EmotibotChat/PHPExcel/Classes/PHPExcel.php';   
  
//要读的文件  
$filePath = 'test.xlsx';  
  
$PHPExcel = new PHPExcel();  
    
$PHPReader = new PHPExcel_Reader_Excel2007();   
if(!($PHPReader->canRead($filePath)))  
{  
     $PHPReader = new PHPExcel_Reader_Excel5();   
     if(!$PHPReader->canRead($filePath))  
      {   
           echo 'no Excel';   
           return ;   
       }  
}  
                                      
 $PHPExcel = $PHPReader->load($filePath);   
 /**读取excel文件中的第一个工作表*/   
 $currentSheet = $PHPExcel->getSheet(0);   
 /**取得最大的列号*/   
 $allColumn = $currentSheet->getHighestColumn();  
 /**取得一共有多少行*/   
 $allRow = $currentSheet->getHighestRow();   
 /**从第二行开始输出，因为excel表中第一行为列名*/   
 for($currentRow = 1;$currentRow <= $allRow;$currentRow++)  
 {   
     /**从第A列开始输出*/   
         for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++)  
         {   
            $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/   
            echo $val."\t";  
         }  
         echo "</br>";   
}  
                                   
?>  

