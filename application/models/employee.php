<?php

class employee extends CI_Model {
    
	function __construct()
	{
		date_default_timezone_set("UTC");
	}
    function login($employeeId, $password)
    {
        $this->db->select('password');
        $this->db->from('employees');
        $this->db->where('id = ' . "'" . $employeeId . "'");
		$this->load->helper('cookie');
		$this->load->helper('form');       // $this->db->where('password = ' . "'" MD5($password) . "'");
        
        $query = $this->db->get();
        
        if($query->num_rows() == 1)
        {
        	$cookie = array(
   				'name'   => "EmployeeId",
    			'value'  => $employeeId,
    			'expire' => '86500',
    			);

			$this->input->set_cookie($cookie); 
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    function createAccount($data)
    {
        $md5email= MD5($data['email']);
        $md5password = MD5($data['password']);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        
        $this->db->query("INSERT INTO employees (firstName, lastName,
        email, password) VALUES ('$firstName','$lastName',
        '$md5email','$md5password')");
        
        return true;
    }
	function getInfo($employeeId)
	{
		$query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$employeeId'");
		return $query->row();
	}
	function updateHour($employeeId, $date, $available, $begin, $end)
	{
		$this->db->query("DELETE FROM hours WHERE employeeId='$employeeId' && day = '$date'");

		$result = $this->db->query("INSERT INTO hours (employeeId,day,available,begin,end)
		VALUES ('$employeeId','$date','$available','$begin','$end')");
	
		return "TRUE";
	}
	function getMaxMonth($employeeId)
	{
		$query = $this->db->query("SELECT month, MAX(month) FROM weekInfo");
		$next = Date('Y-m', strtotime($query->row()->month . "Next Month")) . '-1';
		return $next;
	}
	function getScheduledEvents($month, $employeeId)
	{
		$json = array();
		$_query = $this->db->query("SELECT * FROM scheduled WHERE day < '$month' && employeeId = '$employeeId'");
		foreach($_query->result() as $row)
		{
			array_push($json, json_encode(array(
			"title" => "",
			"start" => "$row->day $row->begin",
			"end"   => "$row->day $row->end",
			"allDay"=> false,
			"color" => 'Orange'
			)));
		}
		return $json;
	}
	function getCalendarEvents($employeeId)
	{
		$finalizedMonth = $this->getMaxMonth($employeeId);
		$json = $this->getScheduledEvents($finalizedMonth, $employeeId);
		$query = $this->db->query("SELECT * FROM hours WHERE employeeId='$employeeId' && day >= '$finalizedMonth' ORDER BY day");	
		foreach ($query->result() as $row)
		{
			if($row->available == "Custom")
			{
				array_push($json, json_encode(array(
				"title" => "",
				"start" => "$row->day $row->begin",
				"end" => "$row->day $row->end",
				"allDay" => FALSE)));
			}
			else if ($row->available == "Available") {
				array_push($json, json_encode(array(
				"title" => "$row->available",
				"start" => "$row->day",
				"color" => "#32CD32")));
			}
			else 
			{
				array_push($json, json_encode(array(
				"title" => "$row->available",
				"start" => "$row->day",
				"color" => "BLACK")));	
			}
		}
		return $json;
	}
	function insertMonthInfo($employeeId, $date, $min, $max, $notes)
	{
		$query = $this->db->query("DELETE FROM weekInfo WHERE employeeId = '$employeeId' && month ='$date'");
		$_query = $this->db->query("INSERT INTO weekInfo (employeeId, month, minHours, maxHours, notes) VALUES ('$employeeId','$date','$min','$max','$notes')");
		return $query;
	}
	function popMonthInfoForm($employeeId, $month)
	{
		$employeeId = $this->input->cookie('EmployeeId');
		$_month = Date("Y-m-d", strtotime($month));
		$month2 = Date("Y-m-d", strtotime($_month . "Next Month"));
		$query = $this->db->query("SELECT * FROM weekInfo WHERE employeeId = '$employeeId' && month >= '$_month' && month <= '$month2'");
		
		if($query->num_rows() > 0)
		{
		$row = $query->row();
		return $row;
		}
		else 
		{
			return "";	
		}
	}
}

