<html>
<head>
Welcome <?php 
echo $firstName . " " . $lastName . "!";
echo form_open('/user/availability');
echo form_submit("submit", "Enter Availability");

?>
</head>
</html>