<?php
   $text = $_POST["testInput"];
   $textArr = explode("\n",$text);
   $len = count($textArr);
   for ($i=0;$i<$len;$i++) {
      echo $textArr[$i] . "<BR/>";
   }
?>
<HTML>
   <HEAD>
      <Script Language=JavaScript>
         alert("<?php echo $text;?>");
      </Script>
   </HEAD>
   <BODY>
      <H1>END</H1>
   </BODY>
</HTML>
