var global_goal_arr;
initializeGoals();
bootbox.animate(false);
$("#calendar").fullCalendar(
{
   header :
   {
      left : 'prev,next',
      center : 'title',
      right : 'month,basicWeek,agendaDay'
   },
   height : 600,
   aspectRatio : 1,
   selectable : false,
   slotMinutes : 15,
   minTime : 8,
   maxTime : 21,
   editable : false,
   sorting : "standard",
   defaultView : 'agendaDay',
   timeFormat : 'h:mm{ - h:mm}',
   dayClick : function(date, allDay, jsEvent, view)
   {
      $("#calendar").fullCalendar("gotoDate", date);
      $("#calendar").fullCalendar("changeView", 'agendaDay');
      showGoals($("#calendar").fullCalendar("getView"));
   },
   eventClick : function(event, allDay, jsEvent, view)
   {
      $("#calendar").fullCalendar("gotoDate", event.start);
      $("#calendar").fullCalendar("changeView", "agendaDay");
      showGoals($("#calendar").fullCalendar("getView"));
   },
   viewDisplay : function(view)
   {
      if (view.name == 'month')
      {
         h = NaN;
      }
      else
      {
         h = 3000;
      }
      $("span.fc-header-title h2").hide();

      $('#calendar').fullCalendar('option', 'contentHeight', h);
      //var result = showGoals(view);
   },
   eventSources : [
   {
      url : url + "index.php/sfl/floorEventSource",
      beforeSend: function()
      {
         global_ajax_requests++;
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + "/manager/floorEventSource");
      },
      complete: function()
      {
         global_ajax_requests--;
         hideLoading();
      }
   }]
});
function initializeGoals()
{
   
   sendRequest("GET", url + "index.php/manager/initializeGoals", {}, function(msg) {
      global_goal_arr = jQuery.parseJSON(msg);
      return showGoals($("#calendar").fullCalendar("getView"));
   }, true);
   /*
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/initializeGoals",
      beforeSend: function(jqXHR, settings) {
         showLoading();
      },
      success : function(msg)
      {
         global_goal_arr = jQuery.parseJSON(msg);
         showGoals($("#calendar").fullCalendar("getView"));
      },
      error : function(textStatus, msg, error)
      {
         alert(error + " initializeGoals");
      },
      complete: function()
      {
         hideLoading();
      }
   });*/
}

function showGoals(view)
{
   if (typeof global_goal_arr == 'undefined')
   {
      return false;
   }
   var d = $("#calendar").fullCalendar("getDate").getMonth();
   var d_cmp = new Date().getMonth();
   if (d < d_cmp - 2 || d > d_cmp + 2)
   {
      $("span.fc-header-title h2").fadeIn();
      return false;
   }
   var date_arr = getStartAndEndDates(view.name, $("#calendar").fullCalendar("getDate"));
   var sum = 0;
   var goal = 0;
   if (view.name == 'basicWeek')
   {
      var currentDate = date_arr[0];
      $("th.fc-day-header").each(function()
      {
         var currentHeader = $(this);
         if (currentHeader.is(":visible"))
         {
            var key_date = makeGoalKey(currentDate);
            currentDate.setDate(currentDate.getDate() + 1);
            var text = currentHeader.html();
            var textSplit = text.split("<br>");
            if (!(typeof global_goal_arr[key_date] == "undefined"))
            {
               goal = global_goal_arr[key_date];
               sum += Number(goal.split("$")[1].replace(/,/g, ""));
            }
            $(currentHeader).html(textSplit[0] + "<br><small>(" + goal + ")</small>");
         }
      });
   }
   else if (date_arr[0] == date_arr[1])
   {
      goal = global_goal_arr[makeGoalKey(date_arr[0])];
      sum += Number(goal.split("$")[1].replace(/,/g, ""));
   }
   else
   {
      var current_date = date_arr[0];
      for (var i = date_arr[0].getDate(); i < date_arr[1].getDate(); i++)
      {
         goal = global_goal_arr[makeGoalKey(current_date)];
         sum += Number(goal.split("$")[1].replace(/,/g, ""));
         current_date.setDate(current_date.getDate() + 1);
      }
   }
   var text = $("span.fc-header-title h2").text();
   var textSplit = text.split("(");
   $("span.fc-header-title h2").text(textSplit[0] + " ($" + commaSeparateNumber(sum) + ")");
   $("span.fc-header-title h2").fadeIn();

   $("#calendar").fullCalendar("refetchEvents");
   return true;
}

function getStartAndEndDates(view, selectedDate)
{
   var ret, start, end;
   if (view == 'basicWeek')
   {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
      end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
      ret = new Array(start, end);
   }
   else if (view == 'month')
   {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
      end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
      ret = new Array(start, end);
   }
   else if (view == 'agendaDay')
   {
      ret = new Array(selectedDate, selectedDate);
   }
   return ret;
}


$("#toggleSupportStaff").click(function()
{
   if (support == true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/sfl/supportEventSource");
      support = false;
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/sfl/supportEventSource");
      support = true;
   }
});
$("#toggleStoreEvents").click(function()
{
   if (events == true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/coEventSource");
      events = false
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/coEventSource");
      events = true;
   }
});
$("#printSchedule").click(function()
{
   window.location = url + "index.php/sfl/printable?events=" + events + "&support=" + support;
});
$(document).ready(function()
{
   if (support === true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/sfl/supportEventSource");
   if (events === true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/coEventSource");
});

$(window).load(function()
{
   if(String(window.location).indexOf("printable") == -1)
      $(".leftNav").css("height", $(window).height() - 40);
   else
      $(".leftNav").hide();
});

function deletePost(id)
{

   bootbox.confirm("Are you sure you would like to delete this post?", function(result) 
   {
      if (result === true)
      {
         sendRequest("GET", url + "index.php/news/deletePost",
         { 
            messageId: id 
         },
         function(msg) 
         {
            $("#message" + id).hide();
         },
         true);
         /*
         $.ajax(
         {
            type : "GET",
            url : url + "index.php/news/deletePost",
            data :
            {
               messageId : id
            },
            success : function(msg)
            {
               $("#message" + id).hide();
            },
            error : function(msg, textStatus, errorThrown)
            {
               error_handler(msg, textStatus, errorThrown, "SFL Calendar deletePost");
            }
         });*/
      }
   });
}

function updatePost(id)
{
   var text = $("#message" + id + " textarea").val();
   bootbox.prompt("Employee ID", function(results)
   {
      if (results === null)
         return true;
      sendRequest("GET", url + "index.php/news/updateNewsfeedPost",
      {
         employeeId : results,
         messageId : id,
         message : text
      },
      function(msg)
      {
         if (msg == "false")
         {
            alert("Please enter a correct Employee ID");
            return updatePost(id);
         }
         successMessage("Updated post successfully");
         return true;
      }, 
      true);
      /*
      $.ajax(
      {
         type : "GET",
         url : url + "index.php/news/updateNewsfeedPost",
         data :
         {
            employeeId : f.employeeId,
            messageId : id,
            message : text
         },
         success : function(msg)
         {
            if (msg == "false")
               alert("Whoops, an error occured");
            else
               alert("You have successfully updated this post");
         },
         error : function(msg, textStatus, errorThrown)
         {
            error_handler(msg, textStatus, errorThrown, "SFL Calendar updateNewsfeedPost");
         }
      }); */
   });
}

function addNewPost()
{
   var value = $("#newPostTextArea").val();
   bootbox.prompt("Employee ID", function(results)
   {
      if (results === null)
         return true;

      sendRequest("GET", url + "index.php/news/addNewsfeedPost",
      {
         employeeId : results,
         message : value
      },
      function(msg)
      {
         if(msg == "false")
         {
            alert("Please enter a valid Employee ID");
            return addNewPost();
         }
         successMessage("Successfully added new post");
         reloadNewsfeed();
      },
      true);
      return true;   

      /*
      $.ajax(
      {
         type : "GET",
         url : url + "index.php/news/addNewsfeedPost",
         data :
         {
            employeeId : f.employeeId,
            message : value
         },
         success : function(msg)
         {
            if (msg == "false")
               alert("I'm not sure who you are... Please try again, and make sure you entered your employeeId in correctly.");
            else
               reloadNewsfeed();
         },
         error : function(msg, textStatus, errorThrown)
         {
            error_handler(msg, textStatus, errorThrown, "SFL Calendar addNewsfeedPost");
         }
      });*/
        
   });

}

function reloadNewsfeed()
{
   sendRequest("GET", url + "index.php/news/reloadNewsfeed", {}, 
   function(msg)
   {
      $("#newsfeed").replaceWith(msg);
   }, true);
   /*
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/news/reloadNewsfeed",
      success : function(msg)
      {
         $("#newsfeed").replaceWith(msg);
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert("reloadNewsfeed " + errorThrown);
      }
   });*/
}

function addMissedSale()
{
   var form = "<form class='form-horizontal' id='missedSaleForm' style='width: 500px;'>" +
      "<div class='form-group'>" +
         "<label for='miss_description' class='col-2 control-label'>Description</label>" +
         "<div class='col-10'>" +
            "<input type='text' class='form-control' placeholder='Enter description here'    name='description' id='miss_description'>" +
         "</div>" +
      "</div>" + 
      "<div class='form-group'>" +
         "<label for='miss_style'>Style Number" +
             "<input type='text' class='form-control' placeholder='Enter style number here'    name='style' id='miss_style'>" +
         "</label>" +
      "</div>" +
      "<div class='form-group'>" +
         "<label for='miss_color'>Color Code" +
            "<input type='text' class='form-control' placeholder='Enter color code here'      name='color' id='miss_color'>" +
         "</label>" +
      "</div>" +
      "<div class='form-group'>" +
         "<label for='miss_size' >Size" +
            "<input type='text' class='form-control' placeholder='Enter size here'            name='size'  id='miss_size'>" +
         "</label>" +
      "</div>" +
      "<div class='form-group'>" +
         "<label for='miss_price'>Price" + 
            "<input type='text' class='form-control' placeholder='Enter price here, ex: 0.00' name='price' id='miss_price'>" +
         "</label>" + 
      "</div>" + 
"</form>";

  bootbox.confirm(form, function(results)
   {
      if (results === true)
      {
         var form = $("#missedSaleForm");
         var data = {
            description : form.description,
            style       : form.style,
            color       : form.color,
            size        : form.size,
            price       : form.price
         };
         sendRequest("POST", url + "index.php/sfl/addMissedSale", data, 
         function(msg) {
            // HANDLE MISSED SALE CONFIRMATION HERE;
         }, true);    
      }
      return true;
   });
}

function validateMissedSaleForm(form)
{
   var price, ret, error, reg;
   price = form.price;
   reg = new RegExp("[^0123456789.]");
   ret = true;
   error = null;
   if (form.description === "")
   {
      ret = false;
      error = "The description field is required.";
   }
   if (reg.test(price) === true)
   {
      ret = false;
      error = "The price was entered incorrectly. Please enter the price in the format 100.00, 5.00, etc.";
   }
   if (price > 1000)
   {
      ret = false;
      error = "The price field may not exceed 1000 dollars.";
   }
   return [ret, error];
}

function addStory()
{
   var form = "<form class='form-inline'>" + 
         "<fieldset>" + 
            "<div class='form-group text-left'>" + 
               "<label for='story_employeeId'>Employee ID </label>" +
               "<input id='story_employeeId' class='placeholder='Enter Employee ID Here...' name='employeeId'>" +
            "</div>" +
            "<div class='form-group text-left'>" +
               "<textarea name='story' rows=10 cols=35 placeholder='Tell us what awesome thing you did today!'></textarea>" + 
            "</div>" + 
         "</fieldset>" +
      "</form>";
   bootbox.confirm(form, function(result)
   {
      if (result === true)
      {
         ajaxAddStory($("#story_employeeId").val(), $("#story").val());
      }
   })
   /*$.prompt(form,
   {
      title : "Employee ID",
      buttons :
      {
         "Submit" : 1,
         "Cancel" : 0
      },
      loaded : function()
      {
         $("#impromptu_employeeId").focus();
      },
      submit : function(e, v, m, f)
      {
         if (v == 1)
         {
            ajaxAddStory(f);
         }
         return true;
      }
   });*/
}

function ajaxAddStory(form)
{
   var employeeId = form.employeeId;
   var story = form.story;
   var reg = new RegExp("[^0123456789]");
   var date = new Date();

   if (reg.test(employeeId) === true)
   {
      alert("Please enter a valid employeeId");
      return false;
   }
   if (story === '')
   {
      alert("You forgot to tell us why you're so awesome!");
      return false;
   }
   sendRequest("POST", url + "index.php/sfl/addStory", 
   {
      date: date.toDateString(),
      employeeId : employeeId,
      story : story
   },
   function(msg)
   {
      if (msg == "false")
         alert("I'm not sure who you are... Please try again, and make sure you entered your employeeId in correctly.");
   }, true);
   /*
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/sfl/addStory",
      data :
      {
         date: date.toDateString(),
         employeeId : employeeId,
         story : story
      },
      success : function(msg)
      {
         if (msg == "false")
            alert("I'm not sure who you are... Please try again, and make sure you entered your employeeId in correctly.");
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert("ajaxAddStory " + errorThrown);
      }
   }); */
}

function getEmailTemplate()
{
   var date = new Date();
   sendRequest("GET", url + "index.php/sfl/getEmailTemplate", 
   {
      date: date.toDateString()
   },
   function(msg)
   {
      message = "<h3>Nightly Email for " + new Date().toDateString() + "</h3><br>";
      bootbox.alert(message + msg);
      selectElementText($("emailTemplate"));
   }, true);
   /*
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/sfl/getEmailTemplate",
      data: {
         date: date.toDateString()
      },
      success : function(msg)
      {
         $.prompt(msg,
         {
            classes : "wide",
            title : "Nightly Email for " + new Date().toDateString(),
            buttons :
            {
               "Ok" : 1
            },
            loaded : function()
            {
               selectElementText($("#emailTemplate"));
            },
            submit : function(e, v, m, f)
            {
               return true;
            }
         });
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert("getEmailTemplate" + errorThrown);
      }
   }); */
}

function selectElementText(element)
{
   element.selText().addClass('highlighted');
}

jQuery.fn.selText = function()
{
   var obj = this[0];
   var selection, range;
   if (agent.browser == "Internet Explorer")
   {
      range = obj.offsetParent.createTextRange();
      range.moveToElementText(obj);
      range.select();
   }
   else if (agent.browser == "Firefox" || agent.browser == "Opera")
   {
      selection = obj.ownerDocument.defaultView.getSelection();
      range = obj.ownerDocument.createRange();
      range.selectNodeContents(obj);
      selection.removeAllRanges();
      selection.addRange(range);
   }
   else if (agent.browser == "Safari")
   {
      selection = obj.ownerDocument.defaultView.getSelection();
      selection.setBaseAndExtent(obj, 0, obj, 1);
   }
   return this;
};

function makeGoalKey(date)
{
   var key_date = date.getFullYear() + "-";
   key_date += (Number(date.getMonth() + 1) < 10) ? "0" : "";
   key_date += (Number(date.getMonth() + 1)) + "-";
   key_date += (date.getDate() < 10) ? "0" : "";
   key_date += date.getDate();
   return key_date;
}

function commaSeparateNumber(val)
{
   while (/(\d+)(\d{3})/.test(val.toString()))
   {
      val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
   }
   return val;
}
$("span.fc-button.fc-button-prev.fc-state-default.fc-corner-left").click(function()
{
   showGoals($("#calendar").fullCalendar("getView"));
});
$("span.fc-button.fc-button-next.fc-state-default.fc-corner-right").click(function()
{
   showGoals($("#calendar").fullCalendar("getView"));
});
$("span.fc-button.fc-button-month.fc-state-default.fc-corner-left").click(function()
{
   showGoals($("#calendar").fullCalendar("getView"));
});
$("span.fc-button.fc-button-basicWeek.fc-state-default").click(function()
{
   showGoals($("#calendar").fullCalendar("getView"));
});
$("span.fc-button.fc-button-agendaDay.fc-state-default.fc-corner-right").click(function()
{
   showGoals($("#calendar").fullCalendar("getView"));
});
