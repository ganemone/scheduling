<h2>Create Account</h2>
<?php 
echo $createFormErrors;
echo form_open('/site/create');
echo form_label('First Name: ','firstName');
echo form_input('firstName');echo "<br>";
echo form_label('Last Name: ','lastName');
echo form_input('lastName');echo "<br>";
echo form_label('Email: ', 'email');
echo form_input('email');echo "<br>";
echo form_label('Password: ','password');
echo form_password('password');echo "<br>";
echo form_label('Retype Password: ','retypePassword');
echo form_password('retypePassword');echo "<br>";
echo form_submit('submit','Submit');
?>
</form>