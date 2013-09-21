<title>Gazelle Scheduling - <? echo $brand ?></title>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top" id="navbar" style='z-index: 500;'>
      <div class="container" style='width: 100%;'>
        	<a class="navbar-brand"><? echo $brand ?></a>
         <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
           <span class="icon-bar"></span>
           <span class="icon-bar"></span>
           <span class="icon-bar"></span>
         </button>
         <div class="nav-collapse collapse bs-navbar-collapse">
            <ul class="nav navbar-nav" id="nav">
            <? foreach ($menu_items as $action => $text) 
             {
               if (is_array($text))
               {
                 echo "<li class='dropdown'>\n";
                 echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>$action<b class='caret'></b></a>\n";
                 echo "<ul class='dropdown-menu'>\n";
                 foreach ($text as $action => $_text) 
                 {
                   echo "<li onclick='slideUpMenu();'><a $action>$_text</a></li>\n";
                 }
                 echo "</ul>\n</li>\n";
               }
               else
       	        echo "<li onclick='slideUpMenu();'><a $action>$text</a></li>\n";
             }
            ?>
          </ul>
          <ul class='nav navbar-nav pull-right'>
           <li>
             <a href='http://giancarloanemone.nfshost.com' id='copy_brand'>Gazelle Scheduling &copy 2013</a>
           </li>  
    		</ul>
         <br class='clear'>
         </div>
	   </div>    
  	</div>
  	<br>
  	<br>
    <script type='text/javascript'>
      var url = "<? echo base_url(); ?>";
    </script>

