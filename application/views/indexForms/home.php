<div class='jumbotron'>
   <div class='container'>
		<div class='row'>
         <div id='employeeLoginForm' class='loginForm col-lg-12'>
            <? echo form_open('/site/login', array("class" => "form-inline")); ?>
            <div class='text-warning'>
               <? echo $loginErrors ?>
            </div>
            <fieldset>
               <legend>Login</legend>
               <div class='form-group'>
                  <label for='employeeId'>Employee ID</label>
                  <input type='text' class='form-control' id='employeeId' name='employeeId' placeholder='Employee ID'>
               </div>
               <div class='form-group'>
                  <label for='password'>Password</label>
                  <input type='password' class='form-control' id='password' name='password' placeholder='Password'>
               </div>
               <input type='submit' id='submit' class='btn btn-primary' value='Log In'>
            </fieldset>  
            <? echo form_close(); ?>  
         </div>
      </div>
   </div>
</div>
</body>
</html>
