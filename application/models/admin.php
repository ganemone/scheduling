<?php

class admin extends CI_Model {
    
	function __construct()
	{
		date_default_timezone_set("UTC");
	}
	function login($employeeId, $password)
	{
		$query = $this->db->query("SELECT * FROM companies WHERE company = '$employeeId' && password = '$password'");
		if($query->num_rows() == 1)
		{
			// Sets cookie, should md5 encode latter... 
			$cookie = array(
			'name' => 'Company',
			'value' => $employeeId,
			'expire' => '86500');
			$this->input->set_cookie($cookie);
			return $query->result();
		}
		else {
			return false;	
		}
	}
	function getEmployeeList()
	{
		$query = $this->db->query("SELECT firstName, lastName, id FROM employees ORDER BY lastName");
		$array = array();
		foreach ($query->result() as $row)
		{
			array_push($array, "$row->firstName " . $row->lastName[0] . ":$row->id");
		}
		return $array;
	}	
	function toggleEmployee($employeeId)
	{
		$query = $this->db->query("SELECT firstName FROM employees WHERE id='$employeeId' && display='1'");
		if($query->num_rows() > 0)
		{
			$query = $this->db->query("UPDATE employees SET display='0' where id='$employeeId'");
		}
		else {
			$query = $this->db->query("UPDATE employees SET display='1' where id='$employeeId'");
		}
		//return $query->result();
	}
	function getScheduledEventFeed()
	{
		$query = $this->db->query("SELECT id, firstName, lastName FROM employees WHERE display='1'");
		$_json = array();
		$busy = $this->input->cookie("busy");
		foreach($query->result() as $row)
		{
			$name = $row->firstName[0] . ". $row->lastName" . " ID:$row->id";
			$_query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$row->id'");
			foreach($_query->result() as $_row)
			{
				$begin = $_row->begin;
				$end = $_row->end;
				$start = Date('Y-m-d H:i:s', strtotime("$_row->day $begin"));
				$_end = Date('Y-m-d H:i:s', strtotime("$_row->day $end"));
				array_push($_json, json_encode(array(
					"title" => $name,
					"start" => $start,
					"end" => $_end,
					"allDay" => false,
					'color' => "ORANGE")));	
				
			}
		}
		return $_json;
	}
	function getEventFeed()
	{
		$query = $this->db->query("SELECT id, firstName, lastName FROM employees WHERE display='1'");
		$json = array();
		$busy = $this->input->cookie('busy');
		
		foreach($query->result() as $row)
		{
			$name = $row->firstName[0] . ". $row->lastName";
			$_query = $this->db->query("SELECT * FROM hours WHERE employeeId = '$row->id'");
			// Create json stuff and add it here...
			foreach($_query->result() as $_row)
			{
				$date = $_row->day;
				$begin = $_row->begin;
				$end = $_row->end;
				$availability = $_row->available;
				
				$title = $name;// . " $availability";
				if($availability == 'Available')
				{
					$color = "#32CD32";
					array_push($json, json_encode(array(
					"title" => $title,
					"start" => $date,
					"allDay" => true,
					'color' => $color)));	
				}
				else if($availability == 'Busy')
				{
					if($busy == 'true')
					{
						$color = "BLACK";
						array_push($json, json_encode(array(
						"title" => $title,
						"start" => $date,
						"allDay" => true,
						'color' => $color)));
					}
				}
				else {
					$color = "#3366CC";
					array_push($json, json_encode(array(
					"title" => $title,
					"start" => $date . ' ' . $begin,
					"end" => $date . ' ' . $end,
					"allDay" => false,
					'color' => $color)));
				}

			}
		}
		return $json;
	}
	function employeeHPW($employeeId, $date, $dayNum)
	{
		if($dayNum == 0)
		{
			$weekStart = $date;
		}
		else {
		$weekStart = Date('Y-m-d', strtotime($date . "last Sunday"));	
		}
		if($dayNum == 6)
		{
			$weekEnd = $date;
		}
		else {
			$weekEnd = Date('Y-m-d', strtotime($date . "next Saturday"));
		}
		$split = explode("-", $weekStart);
		$month = $split[0] . "-" . $split[1];
		$query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$weekStart' && day <= '$weekEnd'");
		$_query = $this->db->query("SELECT * FROM weekInfo WHERE employeeId = '$employeeId' && month like '$month-%%'");
		$result = array();
		if($_query->num_rows() < 1)
		{
			$result['desired'] = 'Not Updated By User';
			$result['notes'] = 'Not Updated By User';
		}
		else {
			$_row = $_query->row();
			$result['desired'] = $_row->minHours . '-' . $_row->maxHours;
			$result['notes'] = $_row->notes;
		}
		$hours = 0;
		foreach($query->result() as $row)
		{
			$end = explode(":", $row->end);
			$begin = explode(":", $row->begin);
			
			$h = $end[0] - $begin[0];
			$m = $end[1] - $begin[1];
			
			$hours += $h;
			$hours += ($m/60);
		}
		$result['scheduled'] = $hours;
		return $result;
		
	}
}
