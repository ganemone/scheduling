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
         message: textStatus + " " + errorThrown + " " + origin + " " + employeeId
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
function buildForm(elements)
{
}
