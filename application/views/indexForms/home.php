<div class='jumbotron' style='margin: 20px;'>
   <div class='container'>
		<div class='row'>
         <div id='employeeLoginForm'> <!--class='loginForm col col-lg-12'>-->
            <? echo form_open('/site/login', array("class" => "form form-horizontal")); ?>
            <div class='text-warning'>
               <? echo $loginErrors ?>
            </div>
               <legend>Login</legend>
               <div class='form-group'>
                  <label for='employeeId' class='control-label'>Employee ID</label>
                  <input type='text' class='form-control' id='employeeId' name='employeeId' placeholder='Employee ID'>
               </div>
               <div class='form-group'>
                  <label for='password' class='control-label'>Password</label>
                  <input type='password' class='form-control' id='password' name='password' placeholder='Password'>
               </div>
               <input type='submit' id='submit' class='btn btn-primary btn-large' value='Log In'>
            <? echo form_close(); ?>  
         </div>
      </div>
   </div>
</div>
</body>
</html>