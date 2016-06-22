<script type="text/javascript">
   //***Step5 expand search Result table
   function expandSearchOnlineListContentFunc() {
      if ($('span.strQ, span.strA').hasClass('fixWidth')) {
         $('span.strQ, span.strA').removeClass('fixWidth');
         $('.NewsexpandSR').text('隐藏过长内容');
      } else {
         $('span.strQ, span.strA').addClass('fixWidth');
         $('.NewsexpandSR').text('显示过长内容');
      }
   }

   //***Step9 列表中的动作聊天Ajax呼叫
   function chatSearchUsers(chatLogsId,chatLogsQq,chatLogsTopic) {
      //ajax
      str = "cmd=actionOnlineList" + "&chatLogsId=" + chatLogsId ;//+ "&Status=" + Status;
      url_str = "OnlineList/OnlineList_action.php?";

      //$('#loadingWrap').show();
      $.ajax({
         beforeSend : function() {
         //alert(url_str + str);
         },
         type : 'GET',
         url : url_str + str,
         cache : false,
         success : function(res) {
         // alert(res);
            $('#loadingWrap').delay(D_LOADING).fadeOut('slow', function() {
               if (!res.match(/^-\d+/))//success
               {
                  document.getElementsByName("searchOnlineListButton")[0].click();
                  alert("预订用户成功，下面将进行qq聊天! 当前聊天主题 "+chatLogsTopic+",请在该主题下聊天； 聊天编号"+chatLogsId+"哦,存储对话时会用到,请熟记这个编号!!");
                  var href="tencent://message/?uin="+chatLogsQq+"&Site=工具啦&Menu=yes&chatLogsId="+chatLogsId;
                  window.open(href);
               }
               else//failed
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


   function clickSearchOnlineListPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_onlinelist_page active")
         return;
      nPage = document.getElementsByName("search_onlinelist_page_no")[0].value;
      document.getElementsByName("search_onlinelist_page_no")[0].value = n;
      str = "search_onlinelist_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_onlinelist_page";
      str = "search_onlinelist_page_end_no_" + nPage;
      document.getElementById(str).className = "search_onlinelist_page";
      str = "search_onlinelist_page_begin_no_" + n;
      document.getElementById(str).className = "search_onlinelist_page active";
      str = "search_onlinelist_page_end_no_" + n;
      document.getElementById(str).className = "search_onlinelist_page active";

      //clear current table
      str = "search_onlinelist_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_onlinelist_page" + n;
      document.getElementById(str).style.display = "block";
   }




   //***Eric 是否可以 删除
   function occurTimeDatePicker() {
      datepicker();
   }
</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchOnlineListContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step:2 搜索框的设计 开始 -->
   <form>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>主题 ：</th>
            <td>
            <input id="searchTopicList" type="text" maxlength="50">
            </td>
         </tr>
         <tr>
            <th colspan="4" class="submitBtns"><a class="btn_submit_new searchOnlineList">
            <input name="searchOnlineListButton" type="button" value="开始查询">
            </a></th>
         </tr>
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->

   <!-- ***Step3 表格框架 开始 -->
   <div id="sResultW" class="reportW" style="display:block;">
      <div id="searchOnlineListPages">
         <div class="toolMenu">
        <!--    <span align=right class="btn" OnClick="newSearchOnlineListContentFunc();">新增</span> -->
         </div>
         <table class="report" border="0" cellspacing="0" cellpadding="0">
            <colgroup>
               <col class="num" />
               <col class="topic" />
               <col class="username1" />
               <col class="qq1" />
               <col class="chatid" />
               <col class="createtime" />
               <col class="userAction" />
            </colgroup>
            <tr>
               <th>序号</th>
               <th>主题</th>
               <th>用户名</th>
               <th>qq号</th>
               <th>对话编号</th>
               <th>创建时间</th>
               <th>动作</th>
            </tr>
            <tr>
               <td colspan="7" class="empty">请输入上方查询条件，并点选[开始查询]</td>
            </tr>
         </table>
         <div class="toolMenu">
          <!--  <span align=right class="btn" OnClick="newSearchOnlineListContentFunc();">新增</span> -->
         </div>
      </div>
   </div>
   <!-- search pages-->
</div>
<!-- ***Step3 表格框架 结束 -->
