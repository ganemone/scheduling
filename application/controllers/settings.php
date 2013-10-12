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
      $this->data["ignore_base"] = true;
      $this->load->view("includes.php", $this->data);
      $this->load->view("header.php", $this->data);
      $this->load->view("settings/index.php");
   }
   function employees()
   {
      $this->load->view("includes.php");
      $this->data['employees'] = $this->settings_model->get_employees();
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Employees";
      $this->load->view("header.php", $this->data);
      $this->load->view("settings/employees.php", $this->data);
   }
   function groups()
   {
      $this->load->view("includes.php");
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Employee Groups";
      $this->load->view("header.php", $this->data);
      $this->data['groups'] = $this->settings_model->get_employee_groups();
      $this->data['employee_select'] = $this->settings_model->get_employee_select();
      $this->load->view("settings/groups.php", $this->data);
   }

   function shifts()
   {
      $this->load->view("includes.php");
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Shift Categories";
      $this->load->view("header.php", $this->data);
      $this->data['shifts'] = $this->settings_model->get_shift_categories();
      $this->load->view("settings/shifts.php", $this->data);
   }
   function goals()
   {
      $this->load->view("includes.php");
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Sales Goals";
      $this->load->view("header.php", $this->data);
      $this->data['goals'] = $this->settings_model->get_goals();
      $this->load->view("settings/goals.php", $this->data);
   }
   function sales()
   {
      $this->load->view("includes.php");
      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Missed Sales";
      $this->load->view("header.php", $this->data);

      $order_by = (isset($_GET['order_by'])) ? $_GET['order_by'] : 0;
      $this->data['sales'] = $this->settings_model->get_missed_sales($order_by);
      $this->load->view("settings/sales.php", $this->data);  
   }
   function stats()
   {
      $this->data["ignore_base"] = true;
      $this->load->view("includes.php", $this->data);

      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Statistics";
      $this->load->view("header.php", $this->data);

      $date = ($this->validator->valid_date($this->input->get("date"))) ? $this->input->get("date") : "2013-01-01";

      $this->data["vendor"] = $this->settings_model->get_vendor_graph($date);
      $this->data["time"] = $this->settings_model->get_time_graph($date);
      $this->data["category"] = $this->settings_model->get_category_graph($date);
      $this->data["gender"] = $this->settings_model->get_gender_graph($date);

      $this->load->view("settings/stats.php", $this->data);
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
      if($this->validator->valid_name($this->input->post("firstName")) && 
         $this->validator->valid_name($this->input->post("lastName")) &&
         $this->validator->valid_email($this->input->post("email")) &&
         $this->validator->valid_wage($this->input->post("wage")) &&
         $this->validator->valid_position($this->input->post("position")) &&
         $this->validator->valid_password($this->input->post("password")) &&
         $this->validator->valid_permissions($this->input->post("permissions")) &&
         $this->validator->valid_employee_id($this->input->post("employeeId"))) 
      {
         echo $this->settings_model->edit_employee(array("firstName" => $this->input->post("firstName"),
            "lastName" => $this->input->post("lastName"),
            "email" => $this->input->post("email"),
            "wage" => $this->input->post("wage"),
            "position" => $this->input->post("position"),
            "permissions" => $this->input->post("permissions"),
            "password" => $this->input->post("password")), $this->input->post("employeeId"));
      }
      else {
         echo "error";
      }
   }
   function add_employee()
   {
      if($this->validator->valid_name($this->input->post("firstName")) && 
         $this->validator->valid_name($this->input->post("lastName")) &&
         $this->validator->valid_email($this->input->post("email")) &&
         $this->validator->valid_wage($this->input->post("wage")) &&
         $this->validator->valid_position($this->input->post("position")) &&
         $this->validator->valid_password($this->input->post("password")) &&
         $this->validator->valid_permissions($this->input->post("permissions"))) 
      {
         echo $this->settings_model->add_employee(array("firstName" => $this->input->post("firstName"),
            "lastName" => $this->input->post("lastName"),
            "email" => $this->input->post("email"),
            "wage" => $this->input->post("wage"),
            "position" => $this->input->post("position"),
            "permissions" => $this->input->post("permissions"),
            "password" => $this->input->post("password")));
      }
      else {
         echo "error";
      }
   }
   function add_employee_to_group()
   {
      if($this->validator->valid_employee_id($this->input->post("id")) &&
         $this->validator->valid_group_id($this->input->post("group_id"))) {
         echo $this->settings_model->add_employee_to_group($this->input->post("id"), $this->input->post("group_id"));
      }
      else {
         echo "error";
      }
   }
   function remove_employee_from_group()
   {
      if($this->validator->valid_employee_id($this->input->post("id")) &&
         $this->validator->valid_group_id($this->input->post("group_id"))) {
         echo $this->settings_model->remove_employee_from_group($this->input->post("id"), $this->input->post("group_id"));
      }
      else {
         echo "error";
      }
   }
   function edit_group()
   {
      if($this->validator->valid_group_id($this->input->post("group_id")) && 
         $this->validator->valid_group_name($this->input->post("name")) && 
         $this->validator->valid_name($this->input->post("abbr"))) {
         echo json_encode($this->settings_model->edit_employee_group(array("name" => $this->input->post("name"), "abbr" => $this->input->post("abbr")), $this->input->post("group_id")));
      }
      else {
         echo "error";
      }
   }
   function delete_group()
   {
      if($this->validator->valid_group_id($this->input->post("group_id"))) {
         echo $this->settings_model->delete_employee_group($this->input->post("group_id"));
      }
      else {
         echo "error";
      }
   }
   function create_group()
   {
      if($this->validator->valid_group_name($this->input->post("name")) &&
         $this->validator->valid_name($this->input->post("abbr"))) {
         echo $this->settings_model->create_employee_group(array("name" => $this->input->post("name"), "abbr" => $this->input->post("abbr")));
      }
      else {
         echo "error";
      }
   }
   function edit_shift_category()
   {
      if($this->validator->valid_shift_category($this->input->post("old_category")) &&
         $this->validator->valid_group_name($this->input->post("category_name")) && 
         $this->validator->valid_name($this->input->post("category_abbr"))) {
         echo $this->settings_model->edit_shift_category(array("category_name" => $this->input->post("category_name"), "category_abbr" => $this->input->post("category_abbr")), $this->input->post("old_category"));
      }
      else {
         echo "error";
      }
   }
   function add_shift_category()
   {
      if($this->validator->valid_group_name($this->input->post("category_name")) && 
         $this->validator->valid_name($this->input->post("category_abbr"))) {
         echo $this->settings_model->add_shift_category($this->input->post("category_name"), $this->input->post("category_abbr"));
      }
      else {
         echo "error";
      }
   }
   function delete_shift_category()
   {
      if($this->validator->valid_shift_category($this->input->post("category_abbr"))) {
         echo $this->settings_model->delete_shift_category($this->input->post("category_abbr"));
      }
      else {
         echo "error";
      }
   }
   function edit_goal()
   {
      if($this->validator->valid_goal_id($this->input->post("goal_id")) && $this->validator->valid_goal($this->input->post("goal"))) {
         echo $this->settings_model->edit_goal($this->input->post("goal_id"), $this->input->post("goal"));
      }
      else {
         echo "error";
      }
   }
   function delete_goal()
   {
      if($this->validator->valid_goal_id($this->input->post("goal_id"))) {
         echo $this->settings_model->delete_goal($this->input->post("goal_id"));
      }
      else {
         echo "error";
      }
   }

   function upload_goals()
   {
      if($this->validator->valid_csv($_FILES['file'])) {
         $result = $this->settings_model->read_csv($_FILES['file']['tmp_name']);
         $final = $this->settings_model->upload_goals($result);
         if($final) {
            $this->data['message'] = "Uploaded Goals";
            $this->data['message_type'] = "success";
         }
         else {
            $this->data['message'] = "Failed to upload goals.";
            $this->data['message_type'] = "danger";   
         }
      }
      else {
         $this->data['message'] = "Failed to upload goals.";
         $this->data['message_type'] = "danger";
      }
      
      $this->load->view("includes.php");

      $this->data["menu_items"] = array("href='" . base_url() . "index.php/settings'" => "Settings",
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->data["brand"] = "Sales Goals";
      $this->load->view("header.php", $this->data);
      $this->data['goals'] = $this->settings_model->get_goals();
      $this->load->view("settings/goals.php", $this->data);

   }

   function edit_missed_sale()
   {
      $result = "error";
      if($this->validator->valid_missed_sale($this->input->post("sale_id"))) {
         $result = $this->settings_model->edit_missed_sale($this->input->post("sale_id"), array(
            "price" => $this->input->post("price"), 
            "description" => $this->input->post("description"),
            "vendor" => $this->input->post("vendor"),
            "size" => $this->input->post("size"),
            "category" => $this->input->post("category"),
            "gender" => $this->input->post("gender"),
            "quantity" => $this->input->post("quantity")));
      }
      echo $result;
   }

   function delete_missed_sale()
   {
      $result = "error";
      if($this->validator->valid_missed_sale($this->input->post("sale_id"))) {
         $result = $this->settings_model->delete_missed_sale($this->input->post("sale_id"));
      }
      echo $result;
   }

   function purge_all()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         $this->settings_model->purge_availability($date);
         $this->settings_model->purge_scheduled($date);
         $this->settings_model->purge_goals($date);
         $this->settings_model->purge_missed_sales($date);
         $this->settings_model->purge_events($date);
         $this->settings_model->purge_stories($date);
         echo "success";
      }
      else {
         echo "error";
      }
      
   }

   function purge_availability()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_availability($date);
      }
      else {
         echo "error";
      }
   }

   function purge_scheduled()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_scheduled($date);
      }
      else {
         echo "error";
      }
   }

   function purge_goals()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_goals($date);
      }
      else {
         echo "error";
      }
   }

   function purge_missed_sales()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_missed_sales($date);
      }
      else {
         echo "error";
      }
   }

   function purge_events()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_events($date);
      }
      else {
         echo "error";
      }
   }

   function purge_stories()
   {
      $date = date("Y-m-d", strtotime($this->input->post("date")));
      if($this->validator->valid_date($date)) {
         echo $this->settings_model->purge_stories($date);
      }
      else {
         echo "error";
      }  
   }

}