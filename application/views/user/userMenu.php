<html>
	<div id='menu'>
		<ul id='list'>
			<li><button id='home' class='menuButton'>Home</button></li>
			<!--<li><button id='about'>About</button></li>-->
			<li><button id='tutorial' class='menuButton'>Tutorial</button></li>
			<li><button id='logOut' class='menuButton'>Log Out</button></li>
		</ul>
	</div>
	<div id='container'>
	</div>
	<div id='bottom'></div>
	<div id='span'><h2>Welcome <? echo $firstName ?>!</h2></div>
	<style type='text/css'>
		li {
		display:inline;
		list-style-type:none;
	}
	#menu {
		position:relative; right:5px; top:0px;
		float:right;
		z-index:1;
	}
	#span {
		width:100%;
		height:4em;
      float:none;
		background-color:#4E9CAF;
		border-bottom:2px solid black;
		opacity:0.5;
	}
	#span h2 {
	   float:left;
	   margin:0px;
	   padding:10px;
	}
	</style>

<script type='text/javascript'>
$(document).ready(function() {
	$("#home").button()
	.click(function()
	{
	   window.location = url + "index.php/user";
	});
	$("#about").button();
	$("#tutorial").button();
	$("#logOut").button();
	var url= "<? echo base_url() ?>";
	$("#logOut").click(function() {
	window.location = url + "index.php/user/logOut";
	});
/*	$("#about").click(function() {
		window.location = url + "index.php/site/about";
}); */
	$("#home").click(function () {
		window.location = url + "index.php/user";
	});
});
</script>
</html>