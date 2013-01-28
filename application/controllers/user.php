<?php 
class User extends CI_Controller {
	public $employeeInfo;
	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('employee');
		
	}
	function test()
	{
		$employeeId = $this->input->cookie('EmployeeId');	
		$info = $this->employee->getInfo($employeeId);
		//$this->employee->getScheduledEvents($employeeId);
	}
	function index()
	{
		$this->employeeInfo['url'] = base_url();
		$employeeId = $this->input->cookie('EmployeeId');	
		$info = $this->employee->getInfo($employeeId);
		$this->employeeInfo['firstName'] = $info->firstName;
		$this->employeeInfo['lastName'] = $info->lastName;
		$this->employeeInfo['maxMonth'] = json_encode($this->employee->getMaxMonth($employeeId));
		$this->load->view("fullcalendar.php"); // loads the full calendar js plug-in
		$this->load->view("fullcalendar_css.php"); // loads the css
		$this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId'); // gets the employeeId from the cookie
		$this->load->view('userMenu.php', $this->employeeInfo);
		$this->load->view("availabilityCalendar.php", $this->employeeInfo); // loads the actual calendar view]
		
	}
	function eventSources()
	{
		$this->employeeInfo['employeeId'] = $this->input->cookie('EmployeeId'); // gets the employeeId from the cookie
		$json = $this->employee->getCalendarEvents($this->employeeInfo['employeeId']); // gets the calendar events already stored in the database
		
		// Loops through the json data and displays the events on the calendar
		echo "[";
		while (count($json) > 0)
		{
			echo  array_pop($json);
			if(count($json) > 0)
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
		$result = $this->employee->updateHour($employeeId,$date,$available,$begin,$end);	
	}
	function updateMonthInfo()
	{
		$employeeId = $_POST['employeeId'];
		$date = $_POST['date'];
		$_date = Date('Y-m',strtotime($date));
		$_date_ = $_date . "-1";
		$min = $_POST['min'];
		$max = $_POST['max'];
		$notes = $_POST['notes'];
		$result = $this->employee->insertMonthInfo($employeeId,$_date_,$min,$max,$notes);
		echo $result;
	}
	function populateMonthInfoForm()
	{
		$month = $this->input->post('month');
		$employeeId = $this->input->post('employeeId');
		$result = $this->employee->popMonthInfoForm($employeeId,$month);
		echo json_encode($result);
	}
}
	