<?php
class User extends CI_Controller
{
   public $employeeInfo;
   public $data;
   function __construct()
   {
      parent::__construct();
      $this->load->helper('cookie');
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->model('employee');
      $this->load->model('emailer');
      $this->load->model('newsfeed');
      $this->load->model('validator');
      $this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId');
      $this->employeeInfo['permissions'] = $this->input->cookie('permissions');
      $cookie = array(
               "name" => "finalized",
               "value" => $this->employee->getFinalizedDate(),
               "expire" => "86400");
      $this->input->set_cookie($cookie);
      $this->validateUser();
      $this->initializeEmployeeInfo($this->employee->getInfo($this->employeeInfo['employeeId']));
      $this->load->library('user_agent');
   }

   function index()
   {
      $this->load->view("includes.php");
      $this->employeeInfo['menu_items'] = array("href='" . base_url() . "index.php/user/logout'" => "Log Out");
      $this->load->view("header.php", $this->employeeInfo);
      $this->load->view("home.php", $this->employeeInfo);
      
   }
   function initializeEmployeeInfo($info)
   {
      $this->employeeInfo['brand'] = "Welcome, " . $info['firstName'];
      $this->employeeInfo['firstName'] = $info['firstName'];
      $this->employeeInfo['lastName'] = $info['lastName'];
      $this->employeeInfo['start_select'] = $info['start_select'];
      $this->employeeInfo['end_select'] = $info['end_select'];
      $this->employeeInfo['maxMonth'] = json_encode($this->employee->getFinalizedDate());
      $this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId');
      $this->employeeInfo['availability'] = TRUE;
      $this->employeeInfo['staff'] = TRUE;
      $this->employeeInfo['events'] = TRUE;
      $this->employeeInfo['resize'] = TRUE;  
   }
   function validateUser()
   {
      $employeeId = $this->input->cookie('EmployeeId');
      $permissions = $this->input->cookie("permissions");
      if (!$employeeId || $permissions == 0)
         header("location: " . base_url() . "index.php/site");
   }
   function userCalendar()
   {
      $this->getNewsfeed();

      $this->employeeInfo['menu_items'] = array(
         "Calendar Actions" => array(
            "id='copyWeek' class='disabled' onclick='copyWeek();'" => "Copy Week",
            "id='pasteWeek' class='disabled' onclick='pasteWeek();'" => "Paste Week",
            "id='coverRequest'" => "Shift Cover",
            "id='showMonthInfoForm'" => "Update Info",
            "id='printableSchedule'" => "Printable Schedule", 
            "id='downloadCalendar'" => "Download Schedule"
         ),
         "Calendar Options" => array(
            "id='showEvents'" => "Store Events",
            "id='showAllStaff'" => "All Staff",
            "id='toggleAvailability'" => "Availability",
         ),
         "href='" . base_url() . "index.php/user'" => "Home",
         "href='logout'" => "Log Out");

      $this->load->view("includes.php");
      $this->load->view("header.php", $this->employeeInfo);
      $this->load->view("/user/availabilityCalendar.php", $this->employeeInfo);
   }

   function printable()
   {
      $employeeId = $this->input->cookie('EmployeeId');
      if (!$employeeId)
         header("location: " . base_url() . "index.php/site");
      $info = $this->employee->getInfo($employeeId);
      $this->employeeInfo['availability'] = $this->input->get("availability");
      $this->employeeInfo['staff'] = $this->input->get("staff");
      $this->employeeInfo['events'] = $this->input->get("events");
      $this->employeeInfo['firstName'] = $info['firstName'];
      $this->employeeInfo['lastName'] = $info['lastName'];
      $this->employeeInfo['resize'] = "\"\"";
      $this->employeeInfo['maxMonth'] = json_encode($this->employee->getFinalizedDate());
      $this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId');
      $this->load->view("includes.php");
      $this->load->view("/sfl/print.php");
      $this->load->view("/user/availabilityCalendar.php", $this->employeeInfo);
   }

   function availabilityEventSource()
   {
      $this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId');
      $json = $this->employee->getAvailableEvents($this->input->get("start"), $this->input->get("end"), $this->employeeInfo['employeeId']);

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

   function scheduledEventSource()
   {
      $json = $this->employee->getScheduledEvents($this->input->get("start"), $this->input->get("end"), $this->input->cookie("EmployeeId"));
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

   function updateHourAction()
   {
      $employeeId = $_POST['employeeId'];
      $date       = date("Y-m-d", strtotime($_POST['day']));
      $available  = $_POST['available'];
      $begin      = date("H:i:s", strtotime($_POST['begin']));
      $end        = date("H:i:s", strtotime($_POST['end']));

      $result = "error";

      if($this->validator->valid_employee_id($employeeId) &&
         $this->validator->valid_date($date) &&
         $this->validator->valid_availability($available) && 
         (($available == "Custom" && $this->validator->valid_time($begin) && $this->validator->valid_time($end)) || $available == "Available" || $available == "Busy")) {

         $result = json_encode($this->employee->updateHour($employeeId, $date, $available, $begin, $end));
      }
      
      echo $result;
   }

   function updateMonthInfo()
   {
      $employeeId = $_POST['employeeId'];
      $date = $_POST['date'];
      $_date = Date('Y-m', strtotime($date));
      $_date_ = $_date . "-1";
      $min = $_POST['min'];
      $max = $_POST['max'];
      $notes = mysql_real_escape_string($_POST['notes']);
      $result = "error";
      
      if($this->validator->valid_date($_date_) && $this->validator->valid_employee_id($employeeId)) {
         $result = $this->employee->insertMonthInfo($employeeId, $_date_, $min, $max, $notes);
      }
      
      echo $result;
   }

   function populateMonthInfoForm()
   {
      $month = date("Y-m-01", strtotime($this->input->post('month')));
      $employeeId = $this->input->post('employeeId');
      $result = $this->employee->popMonthInfoForm($employeeId, $month);
      echo json_encode($result);
   }

   function scheduleRequest()
   {
      $shiftId = $this->input->post('shiftId');
      $this->employeeInfo['employeeId'];
      $this->emailer->shiftCoverRequestEmail($shiftId, $this->employeeInfo['employeeId']);
      echo $this->employee->inputScheduleRequest($shiftId);
   }

   function formatDate($day, $start, $end)
   {
      $_start = explode(" ", $start);
      $_end = explode(" ", $end);
      return array(
         'day' => Date("Y-m-d", strtotime($day)),
         'start' => Date("H:i:s", strtotime($_start[0])),
         'end' => Date("H:i:s", strtotime($_end[0]))
      );
   }

   function shiftSwap()
   {
      $employeeId = $this->input->post('employeeId');
      $originalEmployeeId = $this->input->post('originalEmployeeId');
      $eventId = $this->input->post('eventId');
      
      $result = "error";

      if($this->validator->valid_employee_id($employeeId) && $this->validator->valid_employee_id($originalEmployeeId)) {
         $result = $this->employee->employeeShiftSwap($originalEmployeeId, $employeeId, $eventId);
         if ($result[0] != "false") {
            $this->emailer->shiftSwap($employeeId, $originalEmployeeId, $eventId);
         }
         $result = json_encode($result);
      }
      echo $result;
   }

   function logOut()
   {
      delete_cookie('EmployeeId');
      header("location: " . base_url() . "index.php/site");
   }

   function partialShiftSwap()
   {
      $start      = date("H:i:s", strtotime($this->input->post('start')));
      $end        = date("H:i:s", strtotime($this->input->post('end')));
      $employeeId = $this->input->post('employeeId');
      $oldId      = $this->input->post('originalEmployeeId');
      $shiftId    = $this->input->post('eventId');

      $result = "error";
      
      if($this->validator->valid_time($start) && $this->validator->valid_time($end) && $this->validator->valid_employee_id($employeeId) && $this->validator->valid_employee_id($oldId)) {
         $result = $this->employee->partialShiftSwap($start, $end, $employeeId, $oldId, $shiftId);
         if($result[0] != "false") {
            $this->emailer->shiftSwap($employeeId, $oldId, $shiftId, $start, $end);
         }
         $result = json_encode($result);
      }
      echo $result;
   }

   function coEventSource()
   {
      $json = $this->employee->coEventSource($this->input->get("start"), $this->input->get("end"));
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

   function requestPartialShiftCover()
   {
      $requestStart = $this->input->post('requestStart');
      $requestEnd = $this->input->post('requestEnd');
      $employeeId = $this->input->post('employeeId');
      $shiftId = $this->input->post('shiftId');

      $result = "error";

      if($this->validator->valid_employee_id($employeeId) && $this->validator->valid_time($requestStart) && $this->validator->valid_time($requestEnd)) {
         $this->emailer->shiftCoverRequestEmail($shiftId, $this->employeeInfo['employeeId'], $requestStart, $requestEnd);
         $result = $this->employee->requestPartialShiftCover($requestStart, $requestEnd, $employeeId, $shiftId);
      }
      echo $result;
   }

   function allStaffSource()
   {
      $json = $this->employee->allStaffSource($this->input->cookie('EmployeeId'));
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

   function pickUpEmptyShift()
   {
      $employeeId = $this->input->get("employeeId");
      $start = $this->input->get("start");
      $end = $this->input->get("end");
      $date = $this->input->get("date");
      $position = $this->input->get("position");
      $sfl = $this->input->get("sfl");
      $shiftId = $this->input->get("shiftId");

      $result = "error";

      if($this->validator->valid_employee_id($employeeId) && $this->validator->valid_time($start) && $this->validator->valid_time($end) && $this->validator->valid_date($date)) {
         $result = json_encode($this->employee->pickUpEmptyShift($employeeId, $start, $end, $date, $position, $sfl, $shiftId));
      }
      echo $result;
   }

   function getNewsfeed()
   {
      $this->data['mobile'] = $this->agent->is_mobile();
      $this->data['posts'] = $this->newsfeed->getPosts();
      $this->employeeInfo['newsfeed'] = $this->load->view("newsfeed.php", $this->data, true);
   }

   function pasteWeek()
   {
      $week_start = Date("Y-m-d", strtotime($this->input->post('week_start')));
      $week_end = Date("Y-m-d", strtotime($this->input->post('week_end')));

      $result = "error";
      
      if($this->validator->valid_date($week_start) && $this->validator->valid_date($week_end)) {
         $result = $this->employee->pasteWeek($week_start, $week_end, json_decode($this->input->post("week")));
      }
      echo $result;
   }
   function error_handler()
   {
      echo $this->emailer->emailError($this->input->post("message"));
   }
}
