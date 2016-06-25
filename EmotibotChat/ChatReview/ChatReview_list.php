<script type="text/javascript">

 function downloadCFunc(){
       var searchChatUsername = document.getElementById("searchChatUsername").value;
       var searchReviewfrom = document.getElementsByName("searchReviewfrom")[0].value;
       var searchReviewto = document.getElementsByName("searchReviewto")[0].value;
       var statusCheckbox = document.getElementsByName("searchChatLogsStatus")[0].value;
    
          
       var str;                            //送出内文字串  
       
       //ajax
       str = "cmd=downC" + "&searchChatUsername=" + encodeURIComponent(searchChatUsername) + "&searchReviewfrom=" + searchReviewfrom
            + "&searchReviewto=" + searchReviewto + "&statusCheckbox=" + statusCheckbox;
       url_str = "ChatReview/ChatReview_down.php?";
       window.open(url_str + str);
    }

   //***Step5 expand search Result table
   function expandSearchChatReviewContentFunc() {
      if ($('span.ChatReviewUser, span.ChatReviewRobot, span.ChatReviewTopic').hasClass('fixWidth')) {
         //alert(111);
         $('span.ChatReviewUser, span.ChatReviewRobot, span.ChatReviewTopic').removeClass('fixWidth');
         $('.ChatReviewExpandSR').text('隐藏过长内容');
      } else {
         //alert(222);
         $('span.ChatReviewUser, span.ChatReviewRobot, span.ChatReviewTopic').addClass('fixWidth');
         $('.ChatReviewExpandSR').text('显示过长内容');
      }
   }

   //***Step9 列表中的动作上架/下架Ajax呼叫
   function actionSearchChatReview(RFId, Status) {
      //ajax
      str = "cmd=actionChatReview" + "&RFId=" + RFId + "&Status=" + Status;
      url_str = "ChatReview/ChatReview_action.php?";

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
                  document.getElementsByName("searchChatReviewButton")[0].click();
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
   function deleteSearchChatReview(ChatId) {
      ret = confirm("确定要删除此对话吗?");
      if (!ret)
         return;
      //ajax
      str = "cmd=deleteChatReview" + "&" + "ChatId=" + ChatId;
      url_str = "ChatReview/ChatReview_delete.php?";

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
                  document.getElementsByName("searchChatReviewButton")[0].click();
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
   function modifySearchChatReview(ChatId) {
      str = "cmd=read&ChatId=" + ChatId;
      url_str = "ChatReview/ChatReview_modify.php?";
      window.open(url_str + str);
   }

   function clickSearchChatReviewPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_chatreview_page active")
         return;
      nPage = document.getElementsByName("search_chatreview_page_no")[0].value;
      document.getElementsByName("search_chatreview_page_no")[0].value = n;
      str = "search_chatreview_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_rebotprofile_page";
      str = "search_chatreview_page_end_no_" + nPage;
      document.getElementById(str).className = "search_rebotprofile_page";
      str = "search_chatreview_page_begin_no_" + n;
      document.getElementById(str).className = "search_rebotprofile_page active";
      str = "search_chatreview_page_end_no_" + n;
      document.getElementById(str).className = "search_rebotprofile_page active";

      //clear current table
      str = "search_chatreview_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_chatreview_page" + n;
      document.getElementById(str).style.display = "block";
   }

   //***Step13 新增页面点击保存按钮出发Ajax动作
   function newSearchChatReviewContentFunc() {
      str = "cmd=read&RFId=0";
      url_str = "ChatReview/ChatReview_modify.php?";
      window.open(url_str + str);
   }

   //***Eric 是否可以 删除
   function occurTimeDatePicker() {
      datepicker();
   }
</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchChatReviewContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step2 搜索框的设计 开始 -->
   <form>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>用户/机器人扮演者姓名 ：</th>
            <td>
            <input id="searchChatUsername" type="text" maxlength="50">
            </td>
            <th>状态 ：</th>
            <td colspan="3"><label>
               <Select name="searchChatLogsStatus">
                  <Option value="all" selected>ALL</Option>
                  <Option value="0">未审核</Option>
                  <Option value="3">非常好</Option>
                  <Option value="2">不错</Option>
                  <Option value="1">合格</Option>
                  <Option value="-1">不合格</Option>
               </Select>
            </td>
         </tr>
         <tr>
            <th>对话上传时间 ：</th>
            <td>
               <input id="from22" type="text" name="searchReviewfrom" class="from" readonly="true"/> ~ <input id="to22" type="text" class="to" name="searchReviewto" readonly="true"/>
            </td>
         </tr>
         <tr>
            <th colspan="4" class="submitBtns"><a class="btn_submit_new searchChatReview">
            <input name="searchChatReviewButton" type="button" value="开始查询">
            </a></th>
         </tr>
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->

   <!-- ***Step3 表格框架 开始 -->
   <div id="sResultW" class="reportW" style="display:block;">
      <div id="searchChatReviewPages">
         <table class="report" border="0" cellspacing="0" cellpadding="0">
            <colgroup>
               <col class="num" />
               <col class="ChatLogsId" />
               <col class="ChatReviewUser" />
               <col class="ChatReviewRobot" />
               <col class="ChatReviewTopic" />
               <col class="ChatReviewCounter" />
               <col class="ChatReviewUploadTime" />
               <col class="ChatReviewStatus" />
               <col class="ChatReviewAction" />
            </colgroup>
            <tr>
               <th>编号</th>
               <th>对话编号</th>
               <th>用户</th>
               <th>小影</th>
               <th>对话主题</th>
               <th>对话轮数</th>
               <th>上传日期</th>
               <th>状态</th>
               <th>动作</th>
            </tr>
            <tr>
               <td colspan="9" class="empty">请输入上方查询条件，并点选[开始查询]</td>
            </tr>
         </table>
      </div>
   </div>
   <!-- search pages-->
</div>
<!-- ***Step3 表格框架 结束 -->
