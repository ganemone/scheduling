<?php
class Settings extends CI_Controller
{
   private $data;
   function __construct()
   {
      parent::__construct();
      $this->load->helper('cookie');
      $this->load->helper('url');
      $this->load->model('settings_model');
      $this->load->model('validator');
      $this->load->library('user_agent');
      $this->data['mobile'] = ($this->agent->is_mobile()) ? true : false;
      $employee_id = $this->input->cookie("EmployeeId");
      if(!$this->validator->valid_employee_id($employee_id)) {
         header("location: " . base_url() . "index.php/site");
      } 
      if(!$this->validator->valid_manager($employee_id)) {
         header("location: " . base_url() . "index.php/site");
      }
   }

   function index()
   {
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/user'" => "Home", 
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Settings";
      $this->load->view("includes.php");
      $this->load->view("header.php", $this->data);
      $this->load->view("settings/index.php");
   }
   function employees()
   {
      $this->data['employees'] = $this->settings_model->get_employees();
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Employees";
      $this->load->view("header.php", $this->data);
      $this->load->view("includes.php");
      $this->load->view("settings/employees.php", $this->data);
   }
   function groups()
   {
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Employee Groups";
      $this->load->view("header.php", $this->data);
      $this->data['groups'] = $this->settings_model->get_employee_groups();
      $this->load->view("includes.php");
      $this->load->view("settings/groups.php", $this->data);
   }
   function shifts()
   {
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Shift Categories";
      $this->load->view("header.php", $this->data);
      $this->data['shifts'] = $this->settings_model->get_shift_categories();
      $this->load->view("includes.php");
      $this->load->view("settings/shifts.php", $this->data);
   }
   function goals()
   {
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Sales Goals";
      $this->load->view("header.php", $this->data);
      $this->data['goals'] = $this->settings_model->get_goals();
      $this->load->view("includes.php");
      $this->load->view("settings/goals.php", $this->data);
   }
   function delete_employee()
   {
      if($this->validator->valid_employee_id($this->input->post("employeeId"))) {
         echo $this->settings_model->delete_employee($this->input->post("employeeId"));
      }
      else {
         echo "error";
      }
   }
   function edit_employee()
   {
      
   }
   function add_employee()
   {
      //$this->validator->valid_name($this->input->post("firstName")) && 
         //$this->validator->valid_name($this->input->post("lastName")) &&
      if($this->validator->valid_email($this->input->post("email")) &&
         //$this->validator->valid_wage($this->input->post("wage")) &&
         $this->validator->valid_position($this->input->post("position")) &&
         $this->validator->valid_permissions($this->input->post("permissions"))) 
      {
         echo $this->settings_model->add_employee(array("firstName" => $this->input->post("firstName"),
            "lastName" => $this->input->post("lastName"),
            "email" => $this->input->post("email"),
            "wage" => $this->input->post("wage"),
            "position" => $this->input->post("position"),
            "permissions" => $this->input->post("permissions")));
      }
      else {
         echo "error";
      }
   }
}