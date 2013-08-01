<?php
class Site extends CI_Controller
{
   public $data;
   function __construct()
   {
      parent::__construct();
      $this->load->model('employee');
      $this->load->model('admin');
      $this->load->model('leader');
      $this->load->library('form_validation');
      $this->load->helper('cookie');
      $this->load->helper('url');
      $this->data['loginErrors'] = '';
      //$this -> data['createFormErrors'] = '';
      $this->data['adminLoginErrors'] = '';
      $this->data['sflLoginErrors'] = '';
      $this->data['url'] = base_url();

   }

   function index()
   {
      // Loads the form helper
      $this->load->helper('form');
      // Navigation
      $this->data['brand'] = "Gazelle Sports Scheduling";
      $this->data['menu_items'] = array("id='test'" => "test");
      $this->load->view("includes.php");
      $this->load->view('header.php', $this->data);
      // Loads the view of the home page
      $this->load->view('/indexForms/home', $this->data);

      //$this -> load -> view('/indexForms/createForm', $this -> data);
   }
   function login()
   {
      // loads library and sets rules to form
      $this->form_validation->set_rules('employeeId', 'employeeId', 'required');
      $this->form_validation->set_rules('password', 'password', 'required');
      // Validates the form
      if ($this->form_validation->run() == FALSE)
      {
         $this->data['loginErrors'] = validation_errors();
         $this->load->view('/indexForms/home', $this->data);
         $this->load->view('footer.php');
         
      }
      else
      {
         //Gets the posted employeeId and password
         $employeeId = $this->input->post('employeeId');
         $password = $this->input->post('password');
         // Checks database for login
         $result = $this->employee->login($employeeId, $password);
         if($result === false)
         {
            $this->data['loginErrors'] = "Incorrect Password/ID Combination";
            $this->load->view('/indexForms/home', $this->data);
            $this->load->view('footer.php');
            
         }
         else
         {
            redirect(base_url() . 'index.php/user');
         }
      }
   }

   // Executed when an account is created
   /*
    function create()
    {
    // Form Validation
    $this -> form_validation -> set_rules('firstName', 'First Name', 'required');
    $this -> form_validation -> set_rules('lastName', 'Last Name', 'required');
    $this -> form_validation -> set_rules('email', 'Email', 'required|valid_email');
    $this -> form_validation -> set_rules('password', 'Password', 'required');
    $this -> form_validation -> set_rules('retypePassword', 'Retype Password', 'required');

    // Reloads Page if validation is false, displaying errors
    if ($this -> form_validation -> run() == FALSE)
    {
    $this -> data['createFormErrors'] = validation_errors();
    $this -> load -> view('/indexForms/home', $this -> data);
    $this -> load -> view('/indexForms/adminForm', $this -> data);
    $this -> load -> view('/indexForms/createForm', $this -> data);
    }
    else
    {
    $createFormData = $this -> input -> post();
    $result = $this -> employee -> createAccount($createFormData);
    if ($result === False)
    {
    $this -> data['createFormErrors'] = 'An error occurred when creating your account';
    $this -> load -> view('/indexForms/home', $this -> data);
    $this -> load -> view('/indexForms/adminForm', $this -> data);
    $this -> load -> view('/indexForms/createForm', $this -> data);
    }
    else
    {
    header("location: " . base_url() . "index.php/user");
    }
    }
    }

    function about()
    {

    }
    */
}
