<?php
class Manager extends CI_Controller
{
   public $companyInfo;
   function __construct()
   {
      parent::__construct();
      $this->load->helper('cookie');
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->model('admin');
      $this->load->model('validator');
      $this->load->library('email');
      $this->load->library('user_agent');
      $this->companyInfo['mobile'] = $this->agent->is_mobile();
   }

   function index()
   {
      $employee_id = $this->input->cookie("EmployeeId");
      if(!$this->validator->valid_employee_id($employee_id)) {
         header("location: " . base_url() . "index.php/site");
      } 
      if(!$this->validator->valid_manager($employee_id)) {
         header("location: " . base_url() . "index.php/site");
      }
      $this->companyInfo["groups"] = json_encode($this->admin->getGroups());
      $this->companyInfo['names'] = json_encode($this->admin->getEmployeeList());
      $this->initialize();
      $this->load->view("includes.php");
      $this->load->view("/admin/adminCSS.html");
      $this->load->view("header.php", $this->companyInfo);
      $this->load->view("/admin/adminCalendar.php", $this->companyInfo);

   }
   function settings()
   {
      $this->load->view("includes.php");
      $this->load->view("/admin/settings.php");
   }
   function toggleOption()
   {
      $busy = $this->input->cookie("busy");
      if ($busy == "true")
      {
         $cookie = array(
            'name' => "busy",
            'value' => "false",
            'expire' => '86500'
         );
      }
      else
      {
         $cookie = array(
            'name' => "busy",
            'value' => 'true',
            'expire' => '86500'
         );
      }
      $this->input->set_cookie($cookie);
   }

   function eventSource()
   {
      $employee_obj = json_decode($this->input->get("employee_obj"));
      $start_date = date("Y-m-d", $this->input->get("start"));
      $end_date = date("Y-m-d", $this->input->get("end"));

      if($this->validator->valid_date($start_date) && $this->validator->valid_date($end_date)) {
         $json = $this->admin->getEventFeed($employee_obj, $start_date, $end_date);
         echo "[";
         while (count($json) > 0)
         {
            echo array_pop($json);
            if (count($json) > 0)
            {
               echo ",";
            }
         }
         echo "]";
      }
      else {
         echo "error";
      }
   }

   function scheduledEventSource()
   {
      $employee_obj = json_decode($this->input->get("employee_obj"));
      $_json = $this->admin->getScheduledEventFeed($employee_obj);
      echo "[";
      while (count($_json) > 0)
      {
         echo array_pop($_json);
         if (count($_json) > 0)
         {
            echo ",";
         }
      }
      echo "]";
   }
   function getGraphs()
   {
      $start = date("Y-m-d", strtotime($this->input->post("start")));
      $end   = date("Y-m-d", strtotime($this->input->post("end")));
      $view  = $this->input->post("view");

      $result = "error";

      if($this->validator->valid_date($start) && $this->validator->valid_date($end) && $this->validator->valid_view($view)) {
         $result = json_encode($this->admin->getGraphs($start, $end, $view));
      }
      echo $result;
   }
   function coEventSource()
   {
      if(json_decode($this->input->get("options_obj"))->events)
      {
         $json = $this->admin->coEventSource();
         echo "[";
         while (count($json) > 0)
         {
            echo array_pop($json);
            if (count($json) > 0)
            {
               echo ",";
            }
         }
         echo "]";
      }
      else
      {
         echo "[]";
      }
   }

   function initialize()
   {
      if($this->companyInfo['mobile'] || true) {
         $this->companyInfo["menu_items"] = array(
            "Shift Manipulation" => array(
               "onclick='mobile_addShift()'" => "Add Shift",
               "onclick='mobile_promptEditShiftCategory()'" => "Edit Shift Category",
               "onclick='mobile_promptEditShiftTime()'" => "Edit Shift Time"),
            "id='home'" => "Home", 
            "id='template'"   => "Make Template",
            "onclick='addExternalEvent();'" => "Add Event",
            "id='finalize'"   => "Finalize Schedule",
            "id='logOut'"     => "Log Out");
      }
      else {
         $this->companyInfo['menu_items'] = array("id='home'" => "Home", 
            "id='template'"   => "Make Template",
            "onclick='addExternalEvent();'" => "Add Event",
            "id='finalize'"   => "Finalize Schedule",
            "id='logOut'"     => "Log Out");
      }
      $this->companyInfo['brand'] = "Admin Home";
   }

   function deleteEvent()
   {
      $id = $this->input->post("id");
      $table = $this->input->post("table");
      echo $this->db->query("DELETE FROM $table WHERE id='$id'");
   }

   function scheduleEmployee()
   {
      $employee_arr = array();
      if(isset($_POST["employeeId"]))
      {
         $employee_arr[] = $this->input->post("employeeId");
      }
      if(isset($_POST["employees"]))
      {
         $employee_arr = array_merge($employee_arr, $this->input->post("employees"));
      }
      if(isset($_POST["employee_select_list"]))
      {
         $employee_arr[] = $this->input->post("employee_select_list");
      }

      $date = Date('Y-m-d', strtotime($this->input->post('day')));
      $begin = Date("H:i:s", strtotime($this->input->post('start_time')));
      $end = Date("H:i:s", strtotime($this->input->post('end_time')));
      $sfl = (isset($_POST['sfl'])) ? $this->input->post("sfl") : 0;
      $category = $this->input->post("category");

      $result = "error";

      if($this->validator->valid_date($date) && $this->validator->valid_time($begin) && $this->validator->valid_time($end)) {

         $eventTitle = (isset($_POST["eventTitle"])) ? $this->input->post("eventTitle") : -1;
         if ($eventTitle == -1)
         {
            $result = $this->admin->scheduleEmployeeFloor($employee_arr, $date, $begin, $end, $category, $sfl);
         }
         else
         {
            $result = $this->admin->scheduleEmployeeEvent($employee_arr, $date, $begin, $end, $eventTitle);
         }
      }

      echo $result;
   }
   function updateScheduledEvent()
   {
      $shift_id    = $this->input->post("shift_id");
      $employee_id = $this->input->post("employee_id");
      $day         = date("Y-m-d", strtotime($this->input->post("day")));
      $start       = date("H:i:s", strtotime($this->input->post("start_time")));
      $end         = date("H:i:s", strtotime($this->input->post("end_time")));

      $result = "error";

      if($this->validator->valid_employee_id($employee_id) && $this->validator->valid_date($day) && $this->validator->valid_time($start) && $this->validator->valid_time($end)) {
         $result = $this->admin->updateScheduledEvent($shift_id, $employee_id, $day, $start, $end);
      }
      echo $result;
   }
   function scheduleEmployeeTemplate()
   {
      $employeeId_arr = json_decode($this->input->post("employee_id_arr"));
      $day_arr        = json_decode($this->input->post('day_arr'));
      $begin_arr      = json_decode($this->input->post('begin_arr'));
      $end_arr        = json_decode($this->input->post('end_arr'));
      //$sfl_arr        = json_decode($this->input->post("sfl"));
      $category_arr   = json_decode($this->input->post("category_arr"));
      $result = "error";
      $valid = true;

      for ($i=0; $i < count($employeeId_arr); $i++) {
         if($this->validator->valid_employee_id($employeeId_arr[$i]) === false) {
            $valid = false;
         }
      }

      for ($i=0; $i < count($day_arr); $i++) {
         $day_arr[$i] = date("Y-m-d", strtotime($day_arr[$i]));
         $begin_arr[$i] = date("H:i:s", strtotime($begin_arr[$i]));
         $end_arr[$i] = date("H:i:s", strtotime($end_arr[$i]));

         if(!($this->validator->valid_date($day_arr[$i]) && $this->validator->valid_time($begin_arr[$i]) && $this->validator->valid_time($end_arr[$i]))) {
            $valid = false;
         }
      }
      if($valid === false) {
         echo "error";
      }
      else {
         echo $this->admin->scheduleEmployeeTemplate($employeeId_arr, $day_arr, $begin_arr, $end_arr, $category_arr);
      }
   }

   function getEmployeeWeekHours()
   {
      $employeeId = $this->input->post("employeeId");
      $date = Date('Y-m-d', strtotime($this->input->post("date")));
      $dayNum = $this->input->post("dayNum");
      
      $result = "error";
      if($this->validator->valid_employee_id($employeeId) && $this->validator->valid_date($date)) {
         $result = json_encode($this->admin->employeeHPW($employeeId, $date, $dayNum));
      }
      echo $result;
   }

   function setCookies($val)
   {
      foreach ($_COOKIE as $name => $value)
      {
         $this->input->set_cookie($cookie);
      }
   }

   function logOut()
   {
      delete_cookie("Company");
      header("location: " . base_url() . "index.php/site");
   }

   function tutorialEvents()
   {
      echo json_encode(array(
         array(
            'id' => 111,
            'title' => "Test Schedule",
            'start' => "2012-01-01 10:00",
            'end' => "2012-01-01 15:00",
            'allDay' => false,
            'color' => 'orange'
         ),
         array(
            'id' => 112,
            'title' => "Test Available",
            'start' => "2012-01-01",
            'allDay' => true,
            'color' => 'green'
         ),
         array(
            'id' => 113,
            'title' => "Test Busy",
            'start' => "2012-01-02",
            'allDay' => true,
            'color' => 'Black'
         ),
         array(
            'id' => 114,
            'title' => "Test Custom",
            'start' => "2012-01-03 10:30",
            'end' => "2012-01-03 18:00",
            'allDay' => false
         )
      ));
   }

   function finalize()
   {
      $start = date("Y-m-d", strtotime($this->input->post('start')));
      $result = "error";
      if($this->validator->valid_date($start)) {
         $result = $this->admin->finalizeSchedule($start);
      }
      echo $result;
   }

   function makeTemplate()
   {
      $employeeId = $this->input->post('id');
      $date = date("Y-m-d", strtotime($this->input->post('date')));
      $title = $this->input->post('title');

      $result = "error";

      if($this->validator->valid_employee_id($employeeId) && $this->validator->valid_date($date)) {
         $result = $this->admin->createTemplate($employeeId, $title, $date);
      }
      echo $result;
   }

   function getTemplates()
   {
      echo $this->admin->getTemplateList();
   }

   function loadTemplates()
   {
      echo json_encode($this->admin->loadTemplates());
   }

   function deleteTemplate()
   {
      echo $this->admin->deleteTemplates($this->input->post('templateId'));
   }

   function getTotalInfo()
   {
      $start = date("Y-m-d", strtotime($this->input->post('start')));
      $end   = date("Y-m-d", strtotime($this->input->post('end')));
      $view  = $this->input->post("view");

      $result = "error";
      if($this->validator->valid_date($start) && $this->validator->valid_date($end) && $this->validator->valid_view($view)) {
         $result = $this->admin->getEmployeeHourTotals($start, $end, $view);
      }
      echo $result;
   }

   function getHourWageInfo()
   {
      $start = date("Y-m-d", strtotime($this->input->post('start')));
      $end = date("Y-m-d", strtotime($this->input->post('end')));
      
      $result = "error";
      if($this->validator->valid_date($start) && $this->validator->valid_date($end)) {
         $result = $this->admin->getInfoSpan($start, $end);
      }
      echo $result;
   }

   function getGoal()
   {
      $startDate = Date("Y-m-d", strtotime($this->input->get('startDate')));
      $endDate = Date("Y-m-d", strtotime($this->input->get('endDate')));

      $result = "error";
      if($this->validator->valid_date($startDate) && $this->validator->valid_date($endDate)) {
         $result = $this->admin->getGoal($startDate, $endDate, false);
      }
      echo $result;
   }

   function getGoalArray()
   {
      $startDate = Date("Y-m-d", strtotime($this->input->post('startDate')));
      $endDate = Date("Y-m-d", strtotime($this->input->post('endDate')));
      $result = "error";

      if($this->validator->valid_date($startDate) && $this->validator->valid_date($endDate)) {
         $result = $this->admin->getGoalArray($startDate, $endDate);
      }
      echo $result;
   }

   function updateShiftCategory()
   {
      $id = $this->input->post("id");
      $cat = $this->input->post("category");
      echo $this->admin->updateShiftCategory($id, $cat);
   }

   function updateSFL()
   {
      $id = $this->input->post("id");
      $sfl = $this->input->post("sfl");
      echo $this->admin->updateSFL($id, $sfl);
   }

   function addExternalEvent()
   {
      $title     = $this->input->post("event_title");
      $date      = date("Y-m-d", strtotime($this->input->post("event_date")));
      $repeating = $this->input->post("event_repeat");
      $location  = $this->input->post("event_location");
      $start     = date("H:i:s", strtotime($this->input->post("start_time")));
      $end       = date("H:i:s", strtotime($this->input->post("end_time")));
      $endRepeat = date("Y-m-d", strtotime($this->input->post("repeat_end_date")));
      
      $result = "error";  
      if($this->validator->valid_date($date) && $this->validator->valid_time($start) && $this->validator->valid_time($end)) {
         $result = json_encode($this->admin->addExternalEvent($title, $date, $start, $end, $location, $repeating, $endRepeat));
      }

      echo $result;
   }

   function addEmptyShift()
   {
      $start = Date("H:i:s", strtotime($this->input->get("start")));
      $end = Date("H:i:s", strtotime($this->input->get("end")));
      $date = Date("Y-m-d", strtotime($this->input->get("date")));
      $category = $this->input->get("category");
      $sfl = $this->input->get("sfl");
      $result = "error";
      if($this->validator->valid_date($date) && $this->validator->valid_time($start) && $this->validator->valid_time($end)) {
         $result = $this->admin->addEmptyShift($start, $end, $date, $category, $sfl);
      }
      echo $result;
   }

   function getHoursLeft()
   {
      $start = Date("Y-m-d", strtotime($this->input->post("start")));
      $end = Date("Y-m-d", strtotime($this->input->post("end")));
      $employees = $this->input->post("employees");
      
      $valid = true;
      $result = "error";
      for ($i=0; $i < count($employees); $i++) { 
         if($this->validator->valid_employee_id($employees[$i]) === false) {
            $valid = false;
         }
      }
      if($this->validator->valid_date($start) && $this->validator->valid_date($end) && $valid === true) {
         $result = $this->admin->getHoursLeft($start, $end, $employees);
      } 
      echo $result;
   }
   
   function overrideAvailability()
   {
      $id = $this->input->post("id");
      $category = $this->input->post('category');
      $start = $end = null;

      $valid = true;
      $result = "error";
      if($category == "Custom")
      {
         $start = date("H:i:s", strtotime($this->input->post("start_time")));
         $end   = date("H:i:s", strtotime($this->input->post('end_time')));
         if(!($this->validator->valid_time($start) && $this->validator->valid_time($end))) {
            $valid = false;
         }
      }
      if($this->validator->valid_availability($category) && $valid === true) {
         $result = $this->admin->overrideAvailability($id, $category, $start, $end);
      }
      echo $result;
   }
   
   function initializeGoals()
   {
      echo json_encode($this->admin->initializeGoals());
   }

}
