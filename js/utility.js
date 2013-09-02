var global_ajax_requests = 0;
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
         return callback(msg);
      },
      error: function(jqXHR, textStatus, errorThrown) {
         error_handler(textStatus, errorThrown, url);
      },
      complete: function(jqXHR, textStatus) {
         global_ajax_requests--;
         if(showProgress)
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
      message: { text: "Oops, something went wrong... Quick, go get G! (ps, he is sorry in advance)" },
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
   form += "<form class='form-horizontal' id='" + form_obj.id + "' name='" + form_obj.name + "' style='" + form_obj.style + ";'>";
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
         form += "      <textarea style='width:100%; height: 200px;' id='" + form_obj.elements[i].id + "' name = '" + form_obj.elements[i].name + "' placeholder='" + form_obj.elements[i].placeholder + "'></textarea>";
         form += "   </div>";
         form += "</div>"; 
      }
      else if(form_obj.elements[i].type == "checkbox" || form_obj.elements[i].type == "radio")
      {
         var j = i + 1;
         form += "<div class='form-group'>";
         while(i <= j && i < form_obj.elements.length)
         {
            if (form_obj.elements[i].type == "checkbox" || form_obj.elements[i].type == "radio")
            {
               form += "   <label for='" + form_obj.elements[i].id + "' class='col-4 control-label border-label'>" + form_obj.elements[i].label;
               form += "   <input type='" + form_obj.elements[i].type + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'";
               if(typeof form_obj.elements[i].value != "undefined") 
               {
                  form+= "value='" + form_obj.elements[i].value + "'";
               }
               if(typeof form_obj.elements[i].attr != "undefined")
               {
                  form+= form_obj.elements[i].attr;
               } 
               form += "   ></label>";
               i++;
            }
         }
         i = j;
         form += "</div>";
      }
      else if(form_obj.elements[i].type == "obj")
      {
         var select_obj = form_obj.elements[i].data;
         form += "<div class='form-group'>";
         form += "   <label for='" + form_obj.elements[i].id + "' class='control-label col-2'>" + form_obj.elements[i].label + "</label>";
         form += "   <div class='col-6'>";
         form += "   <select class='form-control' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'>";
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
         form += "</div>";
      }
      else 
      {
         form += "<div class='form-group'>";
         form += "   <label for='" + form_obj.elements[i].id + "' class='col-3 control-label'>" + form_obj.elements[i].label + "</label>";
         form += "   <div class='col-9'>";
         form += "      <input type='" + form_obj.elements[i].type + "' class='form-control' placeholder='" + form_obj.elements[i].placeholder + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'>";
         form += "   </div>";
         form += "</div>";
      }
   };
   form += "</form>";
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
