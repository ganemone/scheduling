<?php 
class Manager extends CI_Controller {
	public $companyInfo;
	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('admin');
	}
	function index()
	{
		$this->companyInfo['company'] = $this->input->cookie("Company");// Loads Company Id Number	
	
		$this->load->view("fullcalendar.php"); //Loads the full calendar js plug-in
		$this->load->view("fullcalendar_css.php"); // Loads the CSS
		$result = $this->admin->getEmployeeList();
		$companyInfo['company'] = $this->input->cookie("Company");
		$companyInfo['names'] = $result;
		$companyInfo['url'] = json_encode(base_url());
		$this->initialize();
		$this->load->view("/impromptu/prompt.php"); 
		$this->load->view("/impromptu/prompt_css.php");
		$this->load->view("/admin/adminMenu.php", $companyInfo);
		$this->load->view("/admin/adminCalendar.php", $companyInfo);
		
	}
	function toggleOption()
	{
		$busy = $this->input->cookie("busy");
		if($busy == "true")
		{
			$cookie = array(
			'name' => "busy",
			'value' => "false",
			'expire' => '86500');
		}
		else {
			$cookie = array(
			'name' => "busy",
			'value' => 'true',
			'expire' => '86500');
		}
		$this->input->set_cookie($cookie);
	}
	function eventSource()
	{	
		$json = $this->admin->getEventFeed();
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
	function scheduledEventSource()
	{
		$_json = $this->admin->getScheduledEventFeed();
		echo "[";
		while (count($_json) > 0)
		{
			echo  array_pop($_json);
			if(count($_json) > 0)
			{
				echo ",";
			}
		}
		echo "]";
	}
	function toggleDisplay()
	{
		$employeeId = $this->input->post('employeeId');
		$result = $this->admin->toggleEmployee($employeeId);
		return $result;
	}
	function initialize() 
	{
		$query = $this->db->query("UPDATE employees SET display='0' where id>'0'");
		$cookie = array(
		'name' => "busy",
		'value' => 'true',
		'expire' => '86500');
			
		$this->input->set_cookie($cookie);
		
		return $query;
	}
	function deleteEvent()
	{
		$employeeId = $this->input->post("employeeId");
		$day = $this->input->post('day');
		$date = Date('Y-m-d', strtotime($day));
		$query = $this->db->query("DELETE FROM scheduled WHERE employeeId = '$employeeId' && day = '$date'");
		return $query;
	}
	function scheduleEmployee()
	{
		$employeeId = $this->input->post("employeeId");
		$day = $this->input->post('day');
		$date = Date('Y-m-d', strtotime($day));
		$begin = Date("H:i:s", strtotime($this->input->post('begin')));
		$end = Date("H:i:s", strtotime($this->input->post('end')));
		$query = $this->db->query("DELETE FROM scheduled WHERE employeeId = '$employeeId' && day = '$date'");
		$query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end) values ('$employeeId','$date','$begin','$end')");
		return $query;
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
		$display = $this->input->post("display");
		$query = $this->db->query("UPDATE employees SET display='$display'");
		return $query;
	}
	function logOut()
	{
		delete_cookie("Company");
	    header("location: " . base_url() .  "index.php/site");
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
		$end = $this->input->post('end');
		$result = $this->admin->finalizeMonth($start, $end);
		return $result;
	}
	
}