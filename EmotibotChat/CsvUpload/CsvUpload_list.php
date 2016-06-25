<script type="text/javascript">
   //***Step5 expand search Result table
   function expandSearchCsv_UploadContentFunc() {
      if ($('span.strQ, span.strA').hasClass('fixWidth')) {
         $('span.strQ, span.strA').removeClass('fixWidth');
         $('.NewsexpandSR').text('隐藏过长内容');
      } else {
         $('span.strQ, span.strA').addClass('fixWidth');
         $('.NewsexpandSR').text('显示过长内容');
      }
   }

   //***Step11 列表中动作修改Ajax长出修改页面
   function modifySearchCsv_Upload(RFId) {
      str = "cmd=read&RFId=" + RFId;
      url_str = "Csv_Upload/Csv_Upload_modify.php?";
      window.open(url_str + str);
   }

   function clickSearchCsv_UploadPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_csv_upload_page active")
         return;
      nPage = document.getElementsByName("search_csv_upload_page_no")[0].value;
      document.getElementsByName("search_csv_upload_page_no")[0].value = n;
      str = "search_csv_upload_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_csv_upload_page";
      str = "search_csv_upload_page_end_no_" + nPage;
      document.getElementById(str).className = "search_csv_upload_page";
      str = "search_csv_upload_page_begin_no_" + n;
      document.getElementById(str).className = "search_csv_upload_page active";
      str = "search_csv_upload_page_end_no_" + n;
      document.getElementById(str).className = "search_csv_upload_page active";

      //clear current table
      str = "search_csv_upload_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_csv_upload_page" + n;
      document.getElementById(str).style.display = "block";
   }

   //***Step13 新增页面点击保存按钮出发Ajax动作
   function newSearchCsv_UploadContentFunc() {
      str = "cmd=read&RFId=0";
      url_str = "Csv_Upload/Csv_Upload_modify.php?";
      window.open(url_str + str);
   }

   //***Eric 是否可以 删除
   function occurTimeDatePicker() {
      datepicker();
   }
</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchCsv_UploadContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step2 搜索框的设计 开始 -->
   <form enctype="multipart/form-data"  method=POST action=CsvUpload/CsvUpload_load.php>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>选取上传文档: </th>
            <td>
            <Input type=file size=50 name="fileToUpload" class="form-control"/>
           
          <!--  <input name="searchPath" type="button" value="选择路径"> -->
            </td>
           <th>
               <td colspan="2"><label>
                  公司名</label>
                 <input name="inputCompanyName" type="text" >
                
           </th>
           <!-- <th>状态 ：</th>
            <td colspan="3"><label>
               <input id="searchRFCheckBox1" type="checkbox" checked>
               上架</label><label>
               <input id="searchRFCheckBox2" type="checkbox" checked>
               下架</label></td> -->
         </tr>
         <tr>
           <!-- <th colspan="4" class="submitBtns"><a class="btn_submit_new searchCsv_Upload">
            <input name="searchCsv_UploadButton" type="button" value="开始上传"> -->
           <th  class="submitBtns">  
           <input type="submit" value="上传文档" name="submit"  class="btn btn-purple">
           </th>
         </tr>
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->

   <!-- ***Step3 表格框架 开始 -->
<!--   <div id="sResultW" class="reportW" style="display:block;">
      <div id="searchCsv_UploadPages">
         <div class="toolMenu">
            <span align=right class="btn" OnClick="newSearchCsv_UploadContentFunc();">新增</span>
         </div>
         <table class="report" border="0" cellspacing="0" cellpadding="0">
            <colgroup>
               <col class="num" />
               <col class="strQ" />
               <col class="strA" />
               <col class="Status" />
               <col class="csv_uploadAction" />
            </colgroup>
            <tr>
               <th>编号</th>
               <th>问题</th>
               <th>解答</th>
               <th>状态</th>
               <th>动作</th>
            </tr>
            <tr>
               <td colspan="5" class="empty">请输入上方查询条件，并点选[开始查询]</td>
            </tr>
         </table>
         <div class="toolMenu">
            <span align=right class="btn" OnClick="newSearchCsv_UploadContentFunc();">新增</span>
         </div>
      </div>
   </div> -->
   <!-- search pages-->
</div>
<!-- ***Step3 表格框架 结束 -->
