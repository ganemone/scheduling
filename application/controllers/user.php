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
      $this->employeeInfo['menu_items'] = array();
      $this->load->view("header.php", $this->employeeInfo);
      $this->load->view("home.php", $this->employeeInfo);
      //$this->load->view('footer.php');
      
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
      $this->employeeInfo['menu_items'] = array(
         "Calendar Actions" => array(
            "id='copyWeek' onclick='copyWeek();'" => "Copy Week",
            "id='pasteWeek' onclick='pasteWeek();'" => "Paste Week",
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
         "Sidebar Options" => array(
            "id='showNewsFeed'" => "Newsfeed",
            "id='showDraggable'" => "Drag Events",
            "id='showColorCode'" => "Color Code"
         ),
         base_url() . "href='index.php/user'" => "Home",
         "href='logout'" => "Log Out");

      $this->load->view("includes.php");
      $this->load->view("/user/userDialogs.html");
      if ($this->agent->is_mobile())
      {
         $this->load->view("/user/mobile/availabilityCalCSSMobile.html");
         $this->load->view('/user/userMenu.php', $this->employeeInfo);
         $this->load->view("/user/mobile/availabilityCalendarMobile.php", $this->employeeInfo);
      }
      else
      {
         $this->load->view("header.php", $this->employeeInfo);
         $this->load->view("/user/availabilityCalCSS.html");
         $this->load->view("/user/availabilityCalendar.php", $this->employeeInfo);
      }
      $this->getNewsfeed();
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
      $this->load->view('/user/userMenu.php', $this->employeeInfo);
      $this->load->view("/user/availabilityCalendar.php", $this->employeeInfo);
   }

   function availabilityEventSource()
   {
      $this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId');
      $json = $this->employee->getAvailableEvents($this->employeeInfo['employeeId']);

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
      $json = $this->employee->getScheduledEvents($this->input->cookie("EmployeeId"));
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
      $date = $_POST['day'];
      $available = $_POST['available'];
      $begin = $_POST['begin'];
      $end = $_POST['end'];
      $result = $this->employee->updateHour($employeeId, $date, $available, $begin, $end);
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
      $notes = mysql_escape_string($_POST['notes']);
      $result = $this->employee->insertMonthInfo($employeeId, $_date_, $min, $max, $notes);
      echo $min;
   }

   function populateMonthInfoForm()
   {
      $month = $this->input->post('month');
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
      $result = $this->employee->employeeShiftSwap($originalEmployeeId, $employeeId, $eventId);
      if ($result[0] !== "false")
         $this->emailer->shiftSwap($employeeId, $originalEmployeeId, $eventId);
      echo json_encode($result);
   }

   function logOut()
   {
      delete_cookie('EmployeeId');
      header("location: " . base_url() . "index.php/site");
   }

   function partialShiftSwap()
   {
      $start = $this->input->post('start');
      $end = $this->input->post('end');
      $employeeId = $this->input->post('employeeId');
      $oldId = $this->input->post('originalEmployeeId');
      $shiftId = $this->input->post('eventId');
      $result = $this->employee->partialShiftSwap($start, $end, $employeeId, $oldId, $shiftId);
      if($result[0] != "false")
         $this->emailer->shiftSwap($employeeId, $oldId, $shiftId, $start, $end);
      echo json_encode($result);
   }

   function coEventSource()
   {
      $json = $this->employee->coEventSource();
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
      $this->emailer->shiftCoverRequestEmail($shiftId, $this->employeeInfo['employeeId'], $requestStart, $requestEnd);
      echo $this->employee->requestPartialShiftCover($requestStart, $requestEnd, $employeeId, $shiftId);
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
      echo json_encode($this->employee->pickUpEmptyShift($employeeId, $start, $end, $date, $position, $sfl, $shiftId));
   }

   function getNewsfeed()
   {
      $this->data['mobile'] = $this->agent->is_mobile();
      $this->data['posts'] = $this->newsfeed->getPosts();
      $this->load->view("newsfeed.php", $this->data);
   }
}
