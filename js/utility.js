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
function buildForm(form_obj)
{
   var form = "<form class='form-horizontal' id='" + form_obj.id + "' name='" + form_obj.name + "' style='" + form_obj.style + ";'>";
<<<<<<< HEAD
   for (var i = 0; i < form_obj.elements.length; i++) 
   {
=======
   for (var i = 0; i < form_obj.elements.length; i++) {
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
      if (form_obj.elements[i].type == "textarea")
      {
         form += "<div class='form-group'>";
         form += "   <div class='col-12'>";
         form += "      <textarea style='width:100%; height: 200px;' id='" + form_obj.elements[i].id + "' name = '" + form_obj.elements[i].name + "' placeholder='" + form_obj.elements[i].placeholder + "'></textarea>";
         form += "   </div>";
         form += "</div>"; 
      }
<<<<<<< HEAD
      else if(form_obj.elements[i].type == "checkbox" || form_obj.elements.type == "radio")
      {
         form += "<div class='form-group'>";
         form += "   <label for='" + form_obj.elements[i].id + "' class='col-4 control-label'>" + form_obj.elements[i].label;
         form += "   <input type='" + form_obj.elements[i].type + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'";
         if(typeof form_obj.elements[i].value != "undefined") 
         {
            form+= "value='" + form_obj.elements[i].value + "'";
         } 
         form += "   ></label>";

         if(i + 1 < form_obj.elements.length)
         {
            i++;
            form += "   <label for='" + form_obj.elements[i].id + "' class='col-4 control-label'>" + form_obj.elements[i].label;
            form += "   <input type='" + form_obj.elements[i].type + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'";
            if(typeof form_obj.elements[i].value != "undefined") 
            {
               form+= "value='" + form_obj.elements[i].value + "'";
            } 
            form += "   ></label>";
         }
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
=======
      else {
         form += "<div class='form-group'>";
         form += "   <label for='" + form_obj.elements[i].id + "' class='col-3 control-label'>" + form_obj.elements[i].label + "</label>";
         form += "   <div class='col-9'>";
         form += "      <input type='" + form_obj.elements[i].type + "' + class='form-control' placeholder='" + form_obj.elements[i].placeholder + "' name='" + form_obj.elements[i].name + "' id='" + form_obj.elements[i].id + "'>";
         form += "   </div>";
         form += "</div>";
      }
    };
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
   form += "</form>";
   return form;
}
