   <body>
		<div id='overlay' class='overlay'></div> 
      <div class='overlay-container'><h4>Loading... Please Wait</h4>  
     	   <div class='progress progress-striped active'>  
      		<div class='progress-bar progress-bar-info' style='width: 100%;'></div>  
  		   </div>
      </div>
		<div class='notifications top-right'></div>
      <div id='calendar'></div>
      <div class='slide'>
         <div class='leftNavOuter'>
   	      <div class='leftNav'>
               <button class="menu-toggle" id='menu-toggle-outer' type="button" onclick='showLeftNav();'>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <br>
               <div class="nav nav-pills">
                  <li class='active' onclick="showLeftMenuItem('newsfeed', this);"><a><small>Newsfeed</small></a></li>
                  <li onclick="showLeftMenuItem('colorCode', this);"><a><small>Color Code</small></a></li>
                  <li onclick="showLeftNav();">
                     <button class="menu-toggle" id='menu-toggle-inner' type="button" onclick='showLeftNav();'>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                     </button>
                  </li>
               </div>
               <hr style='margin: 10px;'>
               <? if (isset($newsfeed)): ?>
               <? echo $newsfeed ?>
               <? endif; ?>
               <div id='colorCode' class='leftMenu'>
                  <h4>Color Code</h4>
                  <div class='external-event fc-view' id='availableCode'>
                     Available All Day
                  </div>
                  <div class='external-event fc-view' id='busyCode'>
                     Busy All Day
                  </div>
                  <div class='external-event fc-view' id='customCode'>
                     Available for a custom time
                  </div>
                  <div class='external-event fc-view' id='scheduledCode'>
                     Scheduled to work
                  </div>
                  <div class='external-event fc-view' id='eventCode'>
                     Event
                  </div>
                  <div class='external-event fc-view' id='pickUpCode'>
                     Shift available to pick up
                  </div>
                  <div class='external-event fc-view' id='coverCode'>
                     Your shift up for cover
                  </div>
               </div>
            </div>
         </div>
      </div>
      <br class='clearfix'>
	</body>
</html>
<script type="text/javascript">
var url = "<? echo base_url() ?>";
var support = "<? echo $support ?>";
var events = "<? echo $events ?>";
var agent = {
   browser : "<? echo $browser ?>",
   version : "<? echo $version ?>"
}
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
$("#calendar").css("width", $(document).width() - 90);

</script>
<script src="<? echo base_url() ?>/js/utility.js"></script>
<script src="<? echo base_url() ?>/js/sfl/sflCalendar.js"></script>
