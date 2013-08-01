<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
   <head>
      <span id='top'>
         <div id='toolbar'>
            <button id='home'>Home</button>
            <button id='logOut'>Log Out</button>
         </div>
         <h1>Gazelle Schedule</h1>
      </span>
   </head>
   <br style="clear:left;">
   <body>
      <div id='calendar'></div>
      <div id='buttons'>
         <button class='mainButton' id='toggleSupportStaff'>Toggle Support Staff</button><br>
         <button class='mainButton' id='toggleStoreEvents'>Toggle Store Events</button><br>
         <button class='mainButton' id='printSchedule'>Printable Schedule</button><br>
         <button class='mainButton' id='missedSale' onclick='addMissedSale()'>Missed Sale</button><br>
         <button class='mainButton' id='story' onclick='addStory()'>Employee Actions</button><br>
         <button class='mainButton' id='nightlyEmail' onclick='getEmailTemplate()'>Nightly Email</button><br>
      </div>
      <br style="clear:left">
   </body>
   
</html>
<script type="text/javascript">
var url = "<? echo base_url() ?>";
var support = <? echo $support ?>;
var events = <? echo $events ?>;
$("#logOut").button()
.click(function()
{
   window.location = url + "index.php/sfl/logOut";
});
$("#home").button();
$("#home").click(function()
{
   window.location = url + "index.php/user";
});
$(".mainButton").each(function() {
   $(this).button();
});
$("#calendar").css("width", $(document).width() - 335);
$(window).resize(function()
{
   $("#calendar").css("width", $(document).width() - 335);
});
</script>
<script src="<? echo base_url() ?>/js/sfl/sflCalendar.js"></script>
