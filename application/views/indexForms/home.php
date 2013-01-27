<html>
<head><h1>Welcome</h1></head>
<body>
<h2>Login</h2>
<?php 
echo $loginErrors;
// Outputs any validation errors
// Log in form below
echo form_open('/site/login'); 
echo form_label('Employee Id: ','employeeId');
echo form_input('employeeId');
echo form_label('Password: ','password');
echo form_password('password');
echo form_submit('submit','Submit');
?>
</form>
</body>
</html>