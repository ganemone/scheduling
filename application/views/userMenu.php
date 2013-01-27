<html>
	<div id='menu'>
		<ul id='list'>
			<li><button id='home'>Home</button></li>
			<li><button id='about'>About</button></li>
			<li><button id='tutorial'>Tutorial</button></li>
			<li><button id='logOut'>Log Out</button></li>
		</ul>
	</div>
	<div id='container'>
	</div>
	<div id='bottom'></div>
	<div id='span'></div>
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
		position:absolute; top:0px; left:0px;
		background-color:lightblue;
		border-bottom:2px solid black;
		opacity:0.5;
	}
	
	
	</style>

<script type='text/javascript'>
$(document).ready(function() {
	$("#home").button();
	$("#about").button();
	$("#tutorial").button();
	$("#logOut").button();
	$("#logOut").click(function() {
	window.location = 'http://localhost/ci-tutorial/index.php/user/logOut';
	});
	$("#about").click(function() {
		window.location = "http://localhost/ci-tutorial/index.php/site/about";
	});
	$("#home").click(function () {
		window.location = "http://localhost/ci-tutorial/index.php/user";
	});
});
</script>
</html>