<script type="text/javascript">
   //***Step5 expand search Result table
   function expandSearchLoadDialogContentFunc() {
      if ($('span.strQ, span.strA').hasClass('fixWidth')) {
         $('span.strQ, span.strA').removeClass('fixWidth');
         $('.NewsexpandSR').text('隐藏过长内容');
      } else {
         $('span.strQ, span.strA').addClass('fixWidth');
         $('.NewsexpandSR').text('显示过长内容');
      }
   }

   //***Step9 列表中的动作上架/下架Ajax呼叫
   function actionSearchLoadDialog(RFId, Status) {
      //ajax
      str = "cmd=actionLoadDialog" + "&RFId=" + RFId + "&Status=" + Status;
      url_str = "LoadDialog/LoadDialog_action.php?";

      //$('#loadingWrap').show();
      $.ajax({
         beforeSend : function() {
            // alert(url_str + str);
         },
         type : 'GET',
         url : url_str + str,
         cache : false,
         success : function(res) {
            //alert(res);
            $('#loadingWrap').delay(D_LOADING).fadeOut('slow', function() {
               if (!res.match(/^-\d+$/))//success
               {
                  document.getElementsByName("searchLoadDialogButton")[0].click();
               } else//failed
               {
                  //echo "1.0";
                  alert(MSG_SEARCH_ERROR);
               }
            });
         },
         error : function(xhr) {
            alert("ajax error: " + xhr.status + " " + xhr.statusText);
         }
      });
   }

   //***Step10 列表中动作删除Ajax呼叫
   function deleteSearchLoadDialog(RFId) {
      ret = confirm("确定要删除此部门吗?");
      if (!ret)
         return;
      //ajax
      str = "cmd=deleteLoadDialog" + "&" + "RFId=" + RFId;
      url_str = "Rebotprofile/LoadDialog_delete.php?";

      //alert(str);
      //$('#loadingWrap').show();
      $.ajax({
         beforeSend : function() {
            //alert(url_str + str);
         },
         type : 'GET',
         url : url_str + str,
         cache : false,
         success : function(res) {
            //alert(res);
            $('#loadingWrap').delay(D_LOADING).fadeOut('slow', function() {
               if (!res.match(/^-\d+$/))//success
               {
                  document.getElementsByName("searchLoadDialogButton")[0].click();
               } else//failed
               {
                  //echo "1.0";
                  alert(MSG_SEARCH_ERROR);
               }
            });
         },
         error : function(xhr) {
            alert("ajax error: " + xhr.status + " " + xhr.statusText);
         }
      });
   }

   //***Step11 列表中动作修改Ajax长出修改页面
   function modifySearchLoadDialog(RFId) {
      str = "cmd=read&RFId=" + RFId;
      url_str = "LoadDialog/LoadDialog_modify.php?";
      window.open(url_str + str);
   }

   function clickSearchLoadDialogPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_loaddialog_page active")
         return;
      nPage = document.getElementsByName("search_loaddialog_page_no")[0].value;
      document.getElementsByName("search_loaddialog_page_no")[0].value = n;
      str = "search_loaddialog_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_loaddialog_page";
      str = "search_loaddialog_page_end_no_" + nPage;
      document.getElementById(str).className = "search_loaddialog_page";
      str = "search_loaddialog_page_begin_no_" + n;
      document.getElementById(str).className = "search_loaddialog_page active";
      str = "search_loaddialog_page_end_no_" + n;
      document.getElementById(str).className = "search_loaddialog_page active";

      //clear current table
      str = "search_loaddialog_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_loaddialog_page" + n;
      document.getElementById(str).style.display = "block";
   }

   //***Step13 新增页面点击保存按钮出发Ajax动作
   function newSearchLoadDialogContentFunc() {
      str = "cmd=read&RFId=0";
      url_str = "LoadDialog/LoadDialog_modify.php?";
      window.open(url_str + str);
   }

   //***Eric 是否可以 删除
   function occurTimeDatePicker() {
      datepicker();
   }
</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchLoadDialogContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step2 搜索框的设计 开始 -->
   <form method=POST action=LoadDialog/LoadDialog_load.php>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>对话编号 ：</th>
            <td>
            <Textarea name="chatId" cols=10 rows=1></Textarea>
            </td>
      </table>
     <table>
     <th>对话格式 ：</th>
     <td colspan="3"><label>
        <Select name="dialogMethond">
           <Option value="1">QQ</Option>
           <Option value="-1">WeChat</Option>
        </Select>
     </td>
     </table>
     <table class="searchField" border="0" cellspacing="0" cellpadding="0">
        <tr>
             <th>对话内容: <th>
        </tr>
        <tr>
            <td>
              <textarea name="chatContent" style="width:600px;height:400px;"></textarea>
            </td>
      </table>
        <tr>
              <input  type="submit" value="保存">
        </tr>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->
</div>
<!-- ***Step3 表格框架 结束 -->
