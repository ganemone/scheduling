var global_ajax_requests = 0;
function slideUpMenu () {
   if($("button.navbar-toggle").is(":visible")) {
      $("button.navbar-toggle").trigger("click");
   }
}
function getBootBoxWidth () {
   var doc_width = $(document).width();
   var width_ret = (doc_width >= 800) ? doc_width/2 : (doc_width >= 500) ? 375 : doc_width;
   var position_left = doc_width/2 - width_ret/2;
   return "position: absolute; left: " + position_left + "px; top: 10%; width: " + width_ret + "px;";0

}
function sendRequest(method, url, data, callback, showProgress)
{
   $.ajax({
      type: method,
      url: url,
      data: data,
      beforeSend: function(jqXHR, settings) {
         global_ajax_requests++;
         if(showProgress)
            showLoading();
      },
      success: function(msg) {
         $(".fc-event").tooltip("hide");
         if(msg == "error") {
            error_handler("Error Occurred in Validation.", "Unsure of error thrown", url);
         }
         else {
            return callback(msg);
         }
      },
      error: function(jqXHR, textStatus, errorThrown) {
         error_handler(textStatus, errorThrown, url);
      },
      complete: function(jqXHR, textStatus) {
         global_ajax_requests--;
         hideLoading();
      }
   })
}
function showLoading() {
   if(!$(".overlay").is(":visible"))
      $(".overlay").fadeIn();
   if(!$(".overlay-container").is(":visible"))
      $(".overlay-container").show();   
}
function hideLoading() {
   if(global_ajax_requests == 0) {
      $(".overlay").hide();
      $(".overlay-container").fadeOut();
   }
}
//--------------------------
// Error Handling Functions
//--------------------------
function error_handler(textStatus, errorThrown, origin)
{
   $(".top-right").notify({
      type: "danger",
      message: { text: "Oops, something went wrong... Please refresh the page and try again." },
   }).show();

   $.ajax({
      type: "POST",
      url: url + "index.php/user/error_handler",
      data: {
         message: textStatus + " " + errorThrown + " " + origin
      },
   });
}

function successMessage(msg)
{
   $('.top-right').notify({
      type: "success",
      message: { text: msg },
      fadeOut: { enabled: true, delay: 3000 }
   }).show();
}

function errorMessage(msg)
{
   $('.top-right').notify({
      type: "danger",
      message: { text: msg },
      fadeOut: { enabled: true, delay: 3000 }
   }).show();
}
function showLeftNav (admin) 
{
   var left = ($(".leftNavOuter").position().left == 0) ? -$(".leftNavOuter").outerWidth() : 0;
   var calendar_width = $(document).width();
   var calendar_subtract = (left == 0) ? 340 : 90;
   $("#calendar").css("width", calendar_width - calendar_subtract + "px");
   $("#calendar").fullCalendar("render");
   $(".leftNavOuter").animate({ left: left });

   if(left == 0) {
      $("#menu-toggle-outer").hide();
      $("#menu-toggle-inner").show();   
   }
   else {
      $("#menu-toggle-outer").show();
      $("#menu-toggle-inner").hide();
   }
}
function showLeftMenuItem(show_element_id, nav_element)
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != show_element_id)
         $(this).hide();
   });
   $("#" + show_element_id).toggle();
   $(".nav-pills").children("li").each(function()
   {
      $(this).removeClass("active");
   });
   $(nav_element).addClass("active");

   if (show_element_id == "newsfeed")
      $('.leftNav').css("overflow-y", "scroll");
   else
      $('.leftNav').css("overflow", "visible");
}
/*
name     : "templateForm",
id       : "templateForm",
style    : "width: 400px;",
elements : new Array() 
*/

/**** Elements ****     
id          : "template_employee_" + employees[j].employeeId,
name        : "template_employee[]",
label       : employees[j].title.split("(")[0] + ": ",
value       : employees[j].employeeId,
type        : type
*/
function buildForm(form_obj)
{
   var form = "";
   if(typeof form_obj["title"] != "undefined")
   {
      form += "<h4>" + form_obj["title"] + "</h4>";
      form += "<hr>";
   }
   form += "<form class='form-horizontal' id='" + form_obj.id + "' name='" + form_obj.name + "'>"; //style='" + form_obj.style + ";'>";
   for (var i = 0; i < form_obj.elements.length; i++) 
   {
      if(form_obj.elements[i].type == "header")
      {
         form += "<p class='text-primary'>" + form_obj.elements[i].value + "</p>";
         form += "<hr>";
      }
      else if (form_obj.elements[i].type == "textarea")
      {
         form += "<div class='form-group'>";
         form += "   <div class='col-12'>";
         form += "      <textarea style='width:100%; height: 200px;' id='" + form_obj.elements[i].id + "' name = '" + form_obj.elements[i].name + "' placeholder='" + form_obj.elements[i].placeholder + "'>"
         if(typeof form_obj.elements[i].value != "undefined") form+= form_obj.elements[i].value;
         form += "</textarea>";
         form += "   </div>";
         form += "</div>"; 
      }
      else if(form_obj.elements[i].type == "group")
      {
         form += "<div class='form-group'>";
         for (var j = 0; j < form_obj.elements[i]["data"].length; j++) 
         {
            if(form_obj.elements[i]["data"][j].type == "select")
            {
               form += buildSelectForm(form_obj.elements[i]["data"][j]);
            }
            else 
            {
               form += "<label for='" + form_obj.elements[i]["data"][j].id + "' class='" + form_obj.elements[i]["data"][j]["label_class"] + "'>" + form_obj.elements[i]["data"][j].label;
               form += "<input class='" + form_obj.elements[i]["data"][j]["input_class"] + "' type='" + form_obj.elements[i]["data"][j].type + "' name='" + form_obj.elements[i]["data"][j].name + "' id='" + form_obj.elements[i]["data"][j].id + "'";
               if(typeof form_obj.elements[i]["data"][j].value != "undefined") 
               {
                  form+= "value='" + form_obj.elements[i]["data"][j].value + "'";
               }
               if(typeof form_obj.elements[i]["data"][j].attr != "undefined")
               {
                  form+= form_obj.elements[i]["data"][j].attr;
               } 
               form += "></label>";
            }
         }
         form += "</div>";
      }
      else if(form_obj.elements[i].type == "select")
      {
         form += "<div class='form-group'>";
         form += buildSelectForm(form_obj.elements[i]);
         form += "</div>";
      }
      else if(form_obj.elements[i].type == "hidden")
      {
         form += "<input type='hidden' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "' value='" + form_obj.elements[i].value + "'>";
      }
      else 
      {
         form += "<div class='form-group'>";
         form += "<label for='" + form_obj.elements[i].id + "' class='";
         form += (typeof form_obj.elements[i]["label_class"] != "undefined") ? form_obj.elements[i]["label_class"] : "col-3 control-label";
         form += "'>" + form_obj.elements[i].label + "</label>";
         form += "<div class='";
         form += (typeof form_obj.elements[i]["input_class"] != "undefined") ? form_obj.elements[i]["input_class"] : "col-9";
         form += "'>";
         form += "   <input type='" + form_obj.elements[i].type + "'";
         if(form_obj.elements[i].type != "radio") {
            form += "class='form-control'";
         }
         form += "placeholder='" + form_obj.elements[i].placeholder + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'";
         if(typeof form_obj.elements[i].value != "undefined") {
            form += "value='" + form_obj.elements[i].value + "'";
         }
         form += ">";
         form += "</div>";
         form += "</div>";
      }
   };
   form += "</form>";
   return form;
}
function buildSelectForm (form_obj) 
{
   var select_obj = form_obj.data;
   var form = "";
   form += "   <label for='" + form_obj.id + "' class='" + form_obj["label_class"] + "'>" + form_obj.label + "</label>";
   form += "   <div class='" + form_obj["input_class"] + "'>";
   form += "   <select class='form-control' name='" + form_obj.name + "' id='" + form_obj.id + "'>";
   var value;
   for(value in select_obj)
   {
      if(select_obj.hasOwnProperty(value))
      {
         form += "<option value='" + value + "'";
         if(select_obj[value]["selected"])
         {
            form += "selected='selected'";
         }
         form += ">" + select_obj[value]["name"] + "</option>";
      }
   }
   form += "   </select>";
   form += "   </div>";
   return form;
}
function validateStartEndTimes (start, end) 
{
   var start_split = start.split(":");
   var end_split = end.split(":");
   if((Number(start_split[0]) + Number(start_split[1])/60) >= (Number(end_split[0]) + Number(end_split[1])/60))
      return false;
   return true;
}
function validateStartEndDates (start, end) 
{
   var start_split = start.split("-"),
      end_split = end.split("-"),
      start_date = new Date(),
      end_date = new Date();

   if(start == "" || end == "" || start_split.length != 3 || end_split.length != 3) {
      return false;
   }
   start_date.setFullYear(start_split[0]);
   start_date.setMonth(start_split[1]);
   start_date.setDate(start_split[2]);

   end_date.setFullYear(end_split[0]);
   end_date.setMonth(end_split[1]);
   end_date.setDate(end_split[2]);

   return (start_date >= end_date) ? false : true;
}

function buildPostDataObj(form_selector) {
   var data = {};
   $(form_selector + " input:checked, " + form_selector + " select, " + form_selector + " input[type=hidden], " + form_selector + " input[type=text]").each(function() {
      if ($(this).val() != "NA") {
         if ($(this).prop("name").indexOf("[]") > 0) {
            if ( typeof data[$(this).prop("name")] == "undefined") {
               data[$(this).prop("name")] = [$(this).val()];
            }
            else {
               data[$(this).prop("name")].push($(this).val());
            }
         }
         else {
            data[$(this).prop("name")] = $(this).val();
         }
      }
   });
   return data;
}
function buildStartEndInputs(form_obj, start_selected, end_selected, start_allowed, end_allowed) {

   var time_obj = {
      '06:00:00' : {
         "name" : "6:00am", "number" : 6.0,
      }, '06:15:00' : {
         "name" : "6:15am", "number" : 6.15,
      }, '06:30:00' : {
         "name" : "6:30am", "number" : 6.30,
      }, '06:45:00' : {
         "name" : "6:45am", "number" : 6.45,
      }, '07:00:00' : {
         "name" : "7:00am", "number" : 7.0,
      }, '07:15:00' : {
         "name" : "7:15am", "number" : 7.15,
      }, '07:30:00' : {
         "name" : "7:30am", "number" : 7.30,
      }, '07:45:00' : {
         "name" : "7:45am", "number" : 7.45,
      }, '08:00:00' : {
         "name" : "8:00am", "number" : 8.0,
      }, '08:15:00' : {
         "name" : "8:15am", "number" : 8.15,
      }, '08:30:00' : {
         "name" : "8:30am", "number" : 8.30,
      }, '08:45:00' : {
         "name" : "8:45am", "number" : 8.45,
      }, '09:00:00' : {
         "name" : "9:00am", "number" : 9.0,
      }, '09:15:00' : {
         "name" : "9:15am", "number" : 9.15,
      }, '09:30:00' : {
         "name" : "9:30am", "number" : 9.30,
      }, '09:45:00' : {
         "name" : "9:45am", "number" : 9.45,
      }, '10:00:00' : {
         "name" : "10:00am", "number" : 10.0,
      }, '10:15:00' : {
         "name" : "10:15am", "number" : 10.15,
      }, '10:30:00' : {
         "name" : "10:30am", "number" : 10.30,
      }, '10:45:00' : {
         "name" : "10:45am", "number" : 10.45,
      }, '11:00:00' : {
         "name" : "11:00am", "number" : 11.0,
      }, '11:15:00' : {
         "name" : "11:15am", "number" : 11.15,
      }, '11:30:00' : {
         "name" : "11:30am", "number" : 11.30,
      }, '11:45:00' : {
         "name" : "11:45am", "number" : 11.45,
      }, '12:00:00' : {
         "name" : "12:00pm", "number" : 12.0,
      }, '12:15:00' : {
         "name" : "12:15pm", "number" : 12.15,
      }, '12:30:00' : {
         "name" : "12:30pm", "number" : 12.30,
      }, '12:45:00' : {
         "name" : "12:45pm", "number" : 12.45,
      }, '13:00:00' : {
         "name" : "1:00pm", "number" : 13.0,
      }, '13:15:00' : {
         "name" : "1:15pm", "number" : 13.15,
      }, '13:30:00' : {
         "name" : "1:30pm", "number" : 13.30,
      }, '13:45:00' : {
         "name" : "1:45pm", "number" : 13.45,
      }, '14:00:00' : {
         "name" : "2:00pm", "number" : 14.0,
      }, '14:15:00' : {
         "name" : "2:15pm", "number" : 14.15,
      }, '14:30:00' : {
         "name" : "2:30pm", "number" : 14.30,
      }, '14:45:00' : {
         "name" : "2:45pm", "number" : 14.45,
      }, '15:00:00' : {
         "name" : "3:00pm", "number" : 15.0,
      }, '15:15:00' : {
         "name" : "3:15pm", "number" : 15.15,
      }, '15:30:00' : {
         "name" : "3:30pm", "number" : 15.30,
      }, '15:45:00' : {
         "name" : "3:45pm", "number" : 15.45,
      }, '16:00:00' : {
         "name" : "4:00pm", "number" : 16.0,
      }, '16:15:00' : {
         "name" : "4:15pm", "number" : 16.15,
      }, '16:30:00' : {
         "name" : "4:30pm", "number" : 16.30,
      }, '16:45:00' : {
         "name" : "4:45pm", "number" : 16.45,
      }, '17:00:00' : {
         "name" : "5:00pm", "number" : 17.0,
      }, '17:15:00' : {
         "name" : "5:15pm", "number" : 17.15,
      }, '17:30:00' : {
         "name" : "5:30pm", "number" : 17.30,
      }, '17:45:00' : {
         "name" : "5:45pm", "number" : 17.45,
      }, '18:00:00' : {
         "name" : "6:00pm", "number" : 18.0,
      }, '18:15:00' : {
         "name" : "6:15pm", "number" : 18.15,
      }, '18:30:00' : {
         "name" : "6:30pm", "number" : 18.30,
      }, '18:45:00' : {
         "name" : "6:45pm", "number" : 18.45,
      }, '19:00:00' : {
         "name" : "7:00pm", "number" : 19.0,
      }, '19:15:00' : {
         "name" : "7:15pm", "number" : 19.15,
      }, '19:30:00' : {
         "name" : "7:30pm", "number" : 19.30,
      }, '19:45:00' : {
         "name" : "7:45pm", "number" : 19.45,
      }, '20:00:00' : {
         "name" : "8:00pm", "number" : 20.0,
      }, '20:15:00' : {
         "name" : "8:15pm", "number" : 20.15,
      }, '20:30:00' : {
         "name" : "8:30pm", "number" : 20.30,
      }, '20:45:00' : {
         "name" : "8:45pm", "number" : 20.45,
      }, '21:00:00' : {
         "name" : "9:00pm", "number" : 21.0
      }
   }
   var start_selected_split = start_selected.split(":");
   var end_selected_split = end_selected.split(":");
   var start_allowed_split = start_allowed.split(":");
   var end_allowed_split = end_allowed.split(":");

   var start_selected_num = Number(start_selected_split[0]) + Number("0." + start_selected_split[1]);
   var end_selected_num = Number(end_selected_split[0]) + Number("0." + end_selected_split[1]);
   var start_allowed_num = Number(start_allowed_split[0]) + Number("0." + start_allowed_split[1]);
   var end_allowed_num = Number(end_allowed_split[0]) + Number("0." + end_allowed_split[1]);

   var start_end_obj = {
      "start" : {}, "end" : {}
   };

   for (timestamp in time_obj) {
      if (time_obj.hasOwnProperty(timestamp) && time_obj[timestamp]["number"] >= start_allowed_num && time_obj[timestamp]["number"] <= end_allowed_num) {
         var s_selected = false;
         var e_selected = false;
         if (start_selected_num == time_obj[timestamp]["number"]) {
            s_selected = true;
         }
         else if (end_selected_num == time_obj[timestamp]["number"]) {
            e_selected = true;
         }
         start_end_obj["start"][timestamp] = {
            "name" : time_obj[timestamp]["name"], "selected" : s_selected
         };
         start_end_obj["end"][timestamp] = {
            "name" : time_obj[timestamp]["name"], "selected" : e_selected
         };
      }
   }
   form_obj["elements"].push({
      "type" : "group", "data" : [{
         "type" : "select", "label" : "Start: ", "name" : "start_time", "id" : "start_time", "data" : start_end_obj["start"], "label_class" : "control-label col-1", "input_class" : "col-4"
      }, {
         "type" : "select", "name" : "end_time", "id" : "end_time", "label" : "End: ", "data" : start_end_obj["end"], "label_class" : "control-label col-1", "input_class" : "col-4"
      }]
   });
}
