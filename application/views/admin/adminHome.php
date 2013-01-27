<html><head><h2>Welcome to the <?php echo $company ?> Admin Page</h2></head>
<body>
<?php
echo form_open('/manager/schedule');
echo form_submit("submit", "Review Scheduling Calendar");
?>
</body></html>