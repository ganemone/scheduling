<?php 
class Site extends CI_Controller {
    public $data;
    function __construct()
    {
        parent::__construct();
		// Loads models
        $this->load->model('employee');
		$this->load->model('admin');
		// Loads form validation library
        $this->load->library('form_validation');
		// Initializes any errors
        $this->data['loginErrors'] = '';
        $this->data['createFormErrors'] = '';
		$this->data['adminLoginErrors'] = '';
		// Loads the cookie helper
		$this->load->helper('cookie');
		$this->load->helper('url');
    }
    function index()
    {
        // Loads the form helper
        $this->load->helper('form');
        // Loads the view of the home page
        $this->load->view('/indexForms/home', $this->data);
		$this->load->view('/indexForms/adminForm', $this->data);
        $this->load->view('/indexForms/createForm',$this->data);
    }
	function adminLogin()
	{
		$this->form_validation->set_rules('employeeId','employeeId','required');
        $this->form_validation->set_rules('password','password','required');
		if($this->form_validation->run() == FALSE)
        {
        	$this->data['adminLoginErrors'] = validation_errors();
			$this->load->view('/indexForms/home', $this->data);
			$this->load->view('/indexForms/adminForm', $this->data);
       		$this->load->view('/indexForms/createForm',$this->data);
		}
		else {
			// Gets the posted employeeId and password
            $employeeId = $this->input->post('employeeId');
            $password = $this->input->post('password');
            // Checks database for login 
            $result = $this->admin->login($employeeId, $password);
			$base = base_url();
			if($result)
			{
				header("location: " . base_url() .  "index.php/manager");
			}
			else {
				$this->data['adminLoginErrors'] = "Incorrect Password/ID Combination" . mysql_error();
                $this->load->view('/indexForms/home', $this->data);
				$this->load->view('/indexForms/adminForm', $this->data);
       			$this->load->view('/indexForms/createForm',$this->data); 
			}
            
		}
			
	}
    function login()
    {
        // loads library and sets rules to form
        $this->form_validation->set_rules('employeeId','employeeId','required');
        $this->form_validation->set_rules('password','password','required');
       
        // Validates the form
        if($this->form_validation->run() == FALSE)
        {
            $this->data['loginErrors'] = validation_errors(); 
            $this->load->view('/indexForms/home', $this->data);
			$this->load->view('/indexForms/adminForm', $this->data);
      	    $this->load->view('/indexForms/createForm',$this->data);
        }
        else
        {
            // Gets the posted employeeId and password
            $employeeId = $this->input->post('employeeId');
            $password = $this->input->post('password');
            // Checks database for login 
            $result = $this->employee->login($employeeId, $password);
            if($result)
            {   
                // Loads user view. (set session information here...?)
                //$this->data'loginErrors'];
                //$this->load->view('user', $this->data);
                //$this->load->view('createForm',$this->data);
            	header("location: " . base_url() .  "index.php/user");
			}
            else 
            {
                $this->data['loginErrors'] = "Incorrect Password/ID Combination";
                $this->load->view('/indexForms/home', $this->data);
				$this->load->view('/indexForms/adminForm', $this->data);
       			$this->load->view('/indexForms/createForm',$this->data);      
	        }
        }
    }
    // Executed when an account is created
    function create()
    {
        // Form Validation
        $this->form_validation->set_rules('firstName','First Name','required');
        $this->form_validation->set_rules('lastName','Last Name','required');
        $this->form_validation->set_rules('email','Email','required|valid_email');
        $this->form_validation->set_rules('password','Password','required');
        $this->form_validation->set_rules('retypePassword','Retype Password','required');
        
        // Reloads Page if validation is false, displaying errors
        if($this->form_validation->run() == FALSE)
        {
            $this->data['createFormErrors'] = validation_errors();
           	$this->load->view('/indexForms/home', $this->data);
			$this->load->view('/indexForms/adminForm', $this->data);
        	$this->load->view('/indexForms/createForm',$this->data);
        }
        else
        {
            $createFormData = $this->input->post();
            $result = $this->employee->createAccount($createFormData);
            if($result === False)
            {
                $this->data['createFormErrors'] = 'An error occurred when creating your account';
                $this->load->view('/indexForms/home', $this->data);
				$this->load->view('/indexForms/adminForm', $this->data);
        		$this->load->view('/indexForms/createForm',$this->data);
            }
            else 
            {
                header("location: " . base_url() .  "index.php/user");
            }
        }
    }
	function about()
	{
		
	}
}