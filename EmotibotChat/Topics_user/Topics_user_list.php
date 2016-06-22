<script type="text/javascript">
   //***Step5 expand search Result table
   function expandSearchTopics_userContentFunc() {
      if ($('span.strQ, span.strA').hasClass('fixWidth')) {
         $('span.strQ, span.strA').removeClass('fixWidth');
         $('.NewsexpandSR').text('隐藏过长内容');
      } else {
         $('span.strQ, span.strA').addClass('fixWidth');
         $('.NewsexpandSR').text('显示过长内容');
      }
   }

   //***Step11 列表中动作修改Ajax长出修改页面
   function modifySearchTopics_user(TopicId,TopicName) {
      str = "cmd=read&TopicId=" + TopicId +"&TopicName=" + TopicName;
      //alert(TopicName);
      url_str = "Topics_user/Topics_user_modify.php?";
      window.open(url_str + str);
   }

   function clickSearchTopics_userPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_topics_user_page active")
         return;
      nPage = document.getElementsByName("search_topics_user_page_no")[0].value;
      document.getElementsByName("search_topics_user_page_no")[0].value = n;
      str = "search_topics_user_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_topics_user_page";
      str = "search_topics_user_page_end_no_" + nPage;
      document.getElementById(str).className = "search_topics_user_page";
      str = "search_topics_user_page_begin_no_" + n;
      document.getElementById(str).className = "search_topics_user_page active";
      str = "search_topics_user_page_end_no_" + n;
      document.getElementById(str).className = "search_topics_user_page active";

      //clear current table
      str = "search_topics_user_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_topics_user_page" + n;
      document.getElementById(str).style.display = "block";
   }

</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchTopics_userContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step2 搜索框的设计 开始 -->
   <form>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>主题 ：</th>
            <td>
            <input id="searchTopics_name" type="text" maxlength="50">
            </td>
         </tr>
         <tr>
            <th colspan="4" class="submitBtns"><a class="btn_submit_new searchTopics_user">
            <input name="searchTopics_userButton" type="button" value="开始查询">
            </a></th>
         </tr>
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->

   <!-- ***Step3 表格框架 开始 -->
   <div id="sResultW" class="reportW" style="display:block;">
      <div id="searchTopics_userPages">
         <table align="center" class="report" border="0" cellspacing="0" cellpadding="0">
            <colgroup>
               <col class="num" />
               <col class="topics" />
               <col class="topics_userAction" />
            </colgroup>
            <tr>
               <th>编号</th>
               <th>主题</th>
               <th>选择</th>
            </tr>
            <tr>
               <td colspan="3" class="empty">请输入上方查询条件，并点选[开始查询]</td>
            </tr>
         </table>
      </div>
   </div>
   <!-- search pages-->
</div>
<!-- ***Step3 表格框架 结束 -->
