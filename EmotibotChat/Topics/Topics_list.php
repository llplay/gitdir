<script type="text/javascript">
   //***Step5 expand search Result table
   function expandSearchTopicsContentFunc() {
      if ($('span.strQ, span.strA').hasClass('fixWidth')) {
         $('span.strQ, span.strA').removeClass('fixWidth');
         $('.NewsexpandSR').text('隐藏过长内容');
      } else {
         $('span.strQ, span.strA').addClass('fixWidth');
         $('.NewsexpandSR').text('显示过长内容');
      }
   }

   //***Step9 列表中的动作上架/下架Ajax呼叫
   function actionSearchTopics(TopicId, Status) {
      //ajax
      str = "cmd=actionTopics" + "&TopicId=" + TopicId + "&Status=" + Status;
      url_str = "Topics/Topics_action.php?";

      //$('#loadingWrap').show();
      $.ajax({
         beforeSend : function() {
      //      alert(url_str + str);
         },
         type : 'GET',
         url : url_str + str,
         cache : false,
         success : function(res) {
            //alert(res);
            $('#loadingWrap').delay(D_LOADING).fadeOut('slow', function() {
               if (!res.match(/^-\d+$/))//success
               {
                  document.getElementsByName("searchTopicsButton")[0].click();
               } else//failed
               {
                  //echo "1.0";
                  alert("新增/修改主题失败,无法写入数据库,数据内容错误");
               }
            });
         },
         error : function(xhr) {
            alert("ajax error: " + xhr.status + " " + xhr.statusText);
         }
      });
   }

   //***Step10 列表中动作删除Ajax呼叫
   function deleteSearchTopics(RFId) {
      ret = confirm("确定要删除此部门吗?");
      if (!ret)
         return;
      //ajax
      str = "cmd=deleteTopics" + "&" + "RFId=" + RFId;
      url_str = "Rebotprofile/Topics_delete.php?";

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
                  document.getElementsByName("searchTopicsButton")[0].click();
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
   function modifySearchTopics(TopicId) {
      str = "cmd=read&TopicId=" + TopicId;
      url_str = "Topics/Topics_modify.php?";
      window.open(url_str + str);
   }

   function clickSearchTopicsPage(obj, n)//搜尋換頁
   {
      if (obj.className == "search_topics_page active")
         return;
      nPage = document.getElementsByName("search_topics_page_no")[0].value;
      document.getElementsByName("search_topics_page_no")[0].value = n;
      str = "search_topics_page_begin_no_" + nPage;
      document.getElementById(str).className = "search_topics_page";
      str = "search_topics_page_end_no_" + nPage;
      document.getElementById(str).className = "search_topics_page";
      str = "search_topics_page_begin_no_" + n;
      document.getElementById(str).className = "search_topics_page active";
      str = "search_topics_page_end_no_" + n;
      document.getElementById(str).className = "search_topics_page active";

      //clear current table
      str = "search_topics_page" + nPage;
      document.getElementById(str).style.display = "none";
      str = "search_topics_page" + n;
      document.getElementById(str).style.display = "block";
   }

   //***Step13 新增页面点击保存按钮出发Ajax动作
   function newSearchTopicsContentFunc() {
      str = "cmd=read&TopicId=0";
      url_str = "Topics/Topics_modify.php?";
      window.open(url_str + str);
   }

   //***Eric 是否可以 删除
   function occurTimeDatePicker() {
      datepicker();
   }
</script>

<!--新增修改所跳出的 block 开始-->
<div id="searchTopicsContent" class="blockUI" style="display:none;"></div>
<!--新增修改所跳出的 block 结束-->

<!--快速查詢 從這裡開始-->
<div class="searchW">
   <!-- ***Step2 搜索框的设计 开始 -->
   <form>
      <table class="searchField" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <th>主题 ：</th>
            <td>
            <input id="searchTopics" type="text" maxlength="50">
            </td>
           <th>状态 ：</th>
            <td colspan="3"><label>
               <input id="searchTopicsCheckBox1" type="checkbox" checked>
               上架</label><label>
               <input id="searchTopicsCheckBox2" type="checkbox" checked>
               下架</label></td> 
         </tr>
         <tr>
            <th colspan="4" class="submitBtns"><a class="btn_submit_new searchTopics">
            <input name="searchTopicsButton" type="button" value="开始查询">
            </a></th>
         </tr>
      </table>
   </form>
   <!-- ***Step2 搜索框的设计 结束 -->

   <!-- ***Step3 表格框架 开始 -->
   <div id="sResultW" class="reportW" style="display:block;">
      <div id="searchTopicsPages">
         <div class="toolMenu">
            <span align=right class="btn" OnClick="newSearchTopicsContentFunc();">新增</span>
         </div>
         <table class="report" border="0" cellspacing="0" cellpadding="0">
            <colgroup>
               <col class="num" />
               <col class="topics" />
               <col class="Status" />
               <col class="topicsAction" />
            </colgroup>
            <tr>
               <th>编号</th>
               <th>主题</th>
               <th>状态</th>
               <th>动作</th>
            </tr>
            <tr>
               <td colspan="4" class="empty">请输入上方查询条件，并点选[开始查询]</td>
            </tr>
         </table>
         <div class="toolMenu">
            <span align=right class="btn" OnClick="newSearchTopicsContentFunc();">新增</span>
         </div>
      </div>
   </div>
   <!-- search pages-->
</div>
<!-- ***Step3 表格框架 结束 -->
