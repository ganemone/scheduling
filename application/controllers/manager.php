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
      $this->load->library('email');

   }

   function index()
   {
      $this->companyInfo['firstLoad'] = "firstLoad";
      $this->companyInfo['company'] = $this->input->cookie("Company");
      
      $this->companyInfo['peoplePerHour'] = json_encode($this->admin->getShiftsPerHour());
      $this->companyInfo["groups"] = json_encode($this->admin->getGroups());
      $this->companyInfo['names'] = json_encode($this->admin->getEmployeeList());
      $this->initialize();
      $this->load->view("includes.php");
      $this->load->view("/admin/adminCSS.html");
      $this->load->view("header.php", $this->companyInfo);
      $this->load->view("/admin/adminCalendar.php", $this->companyInfo);
      $cookie = array(
         'name' => "firstLoad",
         'value' => "true",
         'expire' => '86500'
      );
      $this->input->set_cookie($cookie);
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

   function coEventSource()
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

   function toggleDisplay()
   {
      $employeeId = $this->input->post('employeeId');
      $display = $this->input->post('display');
      $cookie = array(
         'name' => $employeeId,
         'value' => "$display",
         'expire' => '86500'
      );
      $this->input->set_cookie($cookie);
      return true;
   }

   function initialize()
   {
      $cookie = array(
         'name' => "busy",
         'value' => 'true',
         'expire' => '86500'
      );
      $cookie2 = array(
         'name' => 'display',
         'value' => 'false',
         'expire' => '86500'
      );
      $this->input->set_cookie($cookie);
      $this->input->set_cookie($cookie2);

      $this->companyInfo['menu_items'] = array("id='home'" => "Home", 
         "id='template'"   => "Make Template",
         "id='tutorial'"   => "Tutorial",
         "id='addCOEvent'" => "Add Event",
         "id='finalize'"   => "Finalize Schedule",
         "id='logOut'"     => "Log Out");
      $this->companyInfo['brand'] = "Admin Home";
   }

   function deleteEvent()
   {
      $id = $this->input->post("id");
      $table = $this->input->post("table");
      $result = $this->db->query("DELETE FROM $table WHERE id='$id'");
   }

   function scheduleEmployee()
   {
      $employeeId = $this->input->post("employeeId");
      $day = $this->input->post('day');
      $date = Date('Y-m-d', strtotime($day));
      $begin = Date("H:i:s", strtotime($this->input->post('begin')));
      $end = Date("H:i:s", strtotime($this->input->post('end')));
      $sfl = $this->input->post("sfl");
      $category = $this->input->post("category");
      $eventTitle = $this->input->post("eventTitle");

      if ($eventTitle == -1 || $eventTitle == null)
         echo $this->admin->scheduleEmployeeFloor($employeeId, $date, $begin, $end, $category, $sfl);
      else
         echo $this->admin->scheduleEmployeeEvent($employeeId, $date, $begin, $end, $eventTitle);
   }
   function scheduleEmployeeTemplate()
   {
      $employeeId_arr = json_decode($this->input->post("employee_id_arr"));
      $day_arr        = json_decode($this->input->post('day_arr'));
      $begin_arr      = json_decode($this->input->post('begin_arr'));
      $end_arr        = json_decode($this->input->post('end_arr'));
      //$sfl_arr        = json_decode($this->input->post("sfl"));
      $category_arr   = json_decode($this->input->post("category_arr"));
      echo json_encode($this->admin->scheduleEmployeeTemplate($employeeId_arr, $day_arr, $begin_arr, $end_arr, $category_arr));
   }

   function getEmployeeWeekHours()
   {
      $employeeId = $this->input->post("employeeId");
      $date = Date('Y-m-d', strtotime($this->input->post("date")));
      $dayNum = $this->input->post("dayNum");
      $hourArray = $this->admin->employeeHPW($employeeId, $date, $dayNum);
      echo json_encode($hourArray);
   }

   function toggleAll()
   {
      $display = $this->input->post('disp');
      $cookie2 = array(
         'name' => 'display',
         'value' => "$display",
         'expire' => '86500'
      );
      $this->input->set_cookie($cookie2);
      $this->setCookies($display);
      return $display;
   }

   function setCookies($val)
   {
      foreach ($_COOKIE as $name => $value)
      {
         if (!($name == 'display') && !($name == 'busy'))
         {
            $cookie = array(
               'name' => $name,
               'value' => "$val",
               'expire' => '86500'
            );
            $this->input->set_cookie($cookie);
         }
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
      $start = $this->input->post('start');
      $result = $this->admin->finalizeSchedule($start);
      echo $result;
   }

   function makeTemplate()
   {
      $employeeId = $this->input->post('id');
      $date = $this->input->post('date');
      $title = $this->input->post('title');
      echo $this->admin->createTemplate($employeeId, $title, $date);
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
      echo $this->admin->getEmployeeHourTotals($this->input->post('start'), $this->input->post('end'), $this->input->post("view"));
   }

   function getHourWageInfo()
   {
      echo $this->admin->getInfoSpan($this->input->post('start'), $this->input->post('end'));
   }

   function getGoal()
   {
      $startDate = Date("Y-m-d", strtotime($this->input->get('startDate')));
      $endDate = Date("Y-m-d", strtotime($this->input->get('endDate')));
      echo $this->admin->getGoal($startDate, $endDate, false);
   }

   function getGoalArray()
   {
      $startDate = Date("Y-m-d", strtotime($this->input->post('startDate')));
      $endDate = Date("Y-m-d", strtotime($this->input->post('endDate')));
      echo $this->admin->getGoalArray($startDate, $endDate);
   }

   function showSFLOnly()
   {
      $sfl_arr = $this->admin->getSFLArray();
      $this->setCookies("false");
      $cookie = array(
         "name" => "display",
         "value" => "false",
         "expire" => "86500"
      );
      $this->input->set_cookie($cookie);
      $value = $this->input->post("value");
      for ($i = 0; $i < count($sfl_arr); $i++)
      {
         $cookie = array(
            "name" => $sfl_arr[$i],
            "value" => "$value",
            "expire" => "86500"
         );
         $this->input->set_cookie($cookie);
      }
      echo json_encode($sfl_arr);
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

   function addCOEvent()
   {
      $title = $this->input->post("title");
      $date = $this->input->post("date");
      $repeating = $this->input->post("repeating");
      $location = $this->input->post("location");
      $start = $this->input->post("start");
      $end = $this->input->post("end");
      $endRepeat = $this->input->post("endRepeat");
      echo json_encode($this->admin->addCOEvent($title, $date, $start, $end, $location, $repeating, $endRepeat));
   }

   function addEmptyShift()
   {
      $start = Date("H:i:s", strtotime($this->input->get("start")));
      $end = Date("H:i:s", strtotime($this->input->get("end")));
      $date = Date("Y-m-d", strtotime($this->input->get("date")));
      $category = $this->input->get("category");
      $sfl = $this->input->get("sfl");
      echo $this->admin->addEmptyShift($start, $end, $date, $category, $sfl);
   }

   function getHoursLeft()
   {
      $start = Date("Y-m-d", strtotime($this->input->post("start")));
      $end = Date("Y-m-d", strtotime($this->input->post("end")));
      $employees = $this->input->post("employees");
      echo $this->admin->getHoursLeft($start, $end, $employees);
   }
   
   function overrideAvailability()
   {
      $id = $this->input->post("id");
      $category = $this->input->post('category');
      $start = $end = null;
      if($category == "Custom")
      {
         $start = $this->input->post("start");
         $end = $this->input->post('end');
      }
      echo $this->admin->overrideAvailability($id, $category, $start, $end);
   }
   
   function initializeGoals()
   {
      echo json_encode($this->admin->initializeGoals());
   }

}
