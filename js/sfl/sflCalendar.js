var global_goal_arr;
initializeGoals();
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
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + "/manager/floorEventSource");
      }
   }]
});
function initializeGoals()
{
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/initializeGoals",
      success : function(msg)
      {
         global_goal_arr = jQuery.parseJSON(msg);
         showGoals($("#calendar").fullCalendar("getView"));
      },
      error : function(textStatus, msg, error)
      {
         alert(error + " initializeGoals");
      }
   });
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
   if (support === true)
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/sfl/supportEventSource");
   else
      $("#calendar").fullCalendar("addEventSource", url + "index.php/sfl/supportEventSource");
   support = !support;
});
$("#toggleStoreEvents").click(function()
{
   if (events === true)
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/coEventSource");
   else
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/coEventSource");
   events = !events;
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

function deletePost(id)
{
   setTimeout(function()
   {
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
   }, 20);
   $.prompt("Are you sure you would like to delete this post?",
   {
      title : "Confirmation",
      buttons :
      {
         "Yes" : 1,
         "Cancel" : 2
      },
      submit : function(e, v, m, f)
      {
         if (v == 1)
         {
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
                  alert("deletePost errorThrown");
               }
            });
         }
         return true;
      }
   });
}

function updatePost(id)
{
   var text = $("#message" + id + " textarea").val();
   $.prompt("Please Enter your Employee Id: <input type='text' name='employeeId' id='impromptu_employeeId' />",
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
                  alert("updateNewsfeedPost " + errorThrown);
               }
            });
         }
      }
   });
}

function addNewPost()
{
   var value = $("#newPostTextArea").val();
   $.prompt("Please Enter your Employee Id: <input type='text' name='employeeId' id='impromptu_employeeId' />",
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
                  alert("addNewsfeedPost " + errorThrown);
               }
            });
         }
         return true;
      }
   });

}

function reloadNewsfeed()
{
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
   });
}

function addMissedSale()
{
   var form = "<input type='text' placeholder='Description' name='description'><br>" + "<input type='text' placeholder='Style Number' name='style' id='style_num' ><br>" + "<input type='text' placeholder='Color Code' name='color'><br>" + "<input type='text' placeholder='Size' name='size'><br>" + "<input type='text' placeholder='Price ex: 0.00' name='price'><br>";

   $.prompt(form,
   {
      title : "Missed Sale",
      buttons :
      {
         "Submit" : 1,
         "Cancel" : 0
      },
      loaded : function()
      {
         $("#style_num").focus();
      },
      submit : function(e, v, m, f)
      {
         if (v == 1)
         {
            var validate = validateMissedSaleForm(f);
            if (validate[0] === true)
               ajaxMissedSale(f);
            else
               alert(validate[1]);

            return validate[0];
         }
         else if (v === 0)
            return true;
         return false;
      }
   });
}

function ajaxMissedSale(form)
{
   var size = (form.size === "") ? "NA" : form.size;
   var date = new Date();
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/sfl/addMissedSale",
      data :
      {
         style : form.style,
         color : form.color,
         description : form.description,
         size : size,
         price : form.price,
         date: date.toDateString()
      },
      success : function(msg)
      {
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert("addMissedSale " + errorThrown);
      }
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
   var form = "<input id='impromptu_employeeId' placeholder='Employee ID' name='employeeId'><br>" + "<textarea name='story' rows=10 cols=35 placeholder='Tell us what awesome thing you did today!'></textarea>";
   $.prompt(form,
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
   });
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
   });
}

function getEmailTemplate()
{
   var date = new Date();
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
   });
}

function selectElementText(element)
{
   element.selText().addClass('highlighted');
}

jQuery.fn.selText = function()
{
   var obj = this[0];
   var selection, range;
   if ($.browser.msie)
   {
      range = obj.offsetParent.createTextRange();
      range.moveToElementText(obj);
      range.select();
   }
   else if ($.browser.mozilla || $.browser.opera)
   {
      selection = obj.ownerDocument.defaultView.getSelection();
      range = obj.ownerDocument.createRange();
      range.selectNodeContents(obj);
      selection.removeAllRanges();
      selection.addRange(range);
   }
   else if ($.browser.safari)
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
$("span.fc-button.fc-button-month.fc-state-default.fc-corner-left.fc-state-active").click(function()
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
