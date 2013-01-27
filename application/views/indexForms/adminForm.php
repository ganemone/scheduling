<html><head><h2>Admin Login</h2></head>
<body>
<?php 
echo $adminLoginErrors;
// Outputs any validation errors
// Log in form below
echo form_open('/site/adminLogin'); 
echo form_label('Employee Id: ','employeeId');
echo form_input('employeeId');
echo form_label('Password: ','password');
echo form_password('password');
echo form_submit('submit','Submit');
?>
</form></body></html>
