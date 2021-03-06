<?php

class exporter extends CI_Model
{
   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function getScheduledExports($employeeId, $start, $end, $google)
   {
      $query = $this->db->query("SELECT viewable FROM settings");
      $finalized = ($query->num_rows() > 0) ? Date('Y-m-d', strtotime($query->row()->viewable)) : Date('Y-m-d', mktime(0, 0, 0, Date('m') + 2, 1, Date('Y')));
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$start' && day <= '$end' && day <= '$finalized'");
      $array = array();
      foreach ($query->result() as $row)
      {
         if ($google == "on")
         {
            $start_stamp = strtotime($row->day . " " . $row->begin) + 4 * 60 * 60;
            $end_stamp = strtotime($row->day . " " . $row->end) + 4 * 60 * 60;
            $dtstart = gmdate('Ymd', $start_stamp) . 'T' . gmdate('His', $start_stamp) . "Z";
            $dtend = gmdate('Ymd', $end_stamp) . 'T' . gmdate('His', $end_stamp) . "Z";
         }
         else
         {
            $start_stamp = strtotime($row->day . " " . $row->begin);
            $end_stamp = strtotime($row->day . " " . $row->end);
            $dtstart = gmdate('Ymd', $start_stamp) . 'T' . gmdate('His', $start_stamp);
            $dtend = gmdate('Ymd', $end_stamp) . 'T' . gmdate('His', $end_stamp);
         }
         $array[] = array(
            "title" => "Scheduled $row->category",
            "start" => $dtstart,
            "end" => $dtend,
            "location" => ""
         );
      }
      
      return $array;
   }

}
