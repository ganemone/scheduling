<?php

class exporter extends CI_Model
{
   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function getScheduledExports($employeeId, $start, $end, $events, $google)
   {
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$start' && day <= '$end'");
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
      if ($events == "on")
      {
         $query = $this->db->query("SELECT * FROM events WHERE date >= '$start' && date <= '$end'");
         foreach ($query->result() as $row)
         {
            if ($google == "on")
            {
               $start_stamp = strtotime($row->date . " " . $row->start) + 4 * 60 * 60;
               $end_stamp = strtotime($row->date . " " . $row->end) + 4 * 60 * 60;
               $dtstart = gmdate('Ymd', $start_stamp) . 'T' . gmdate('His', $start_stamp) . "Z";
               $dtend = gmdate('Ymd', $end_stamp) . 'T' . gmdate('His', $end_stamp) . "Z";
            }
            else
            {
               $start_stamp = strtotime($row->date . " " . $row->start);
               $end_stamp = strtotime($row->date . " " . $row->end);
               $dtstart = gmdate('Ymd', $start_stamp) . 'T' . gmdate('His', $start_stamp);
               $dtend = gmdate('Ymd', $end_stamp) . 'T' . gmdate('His', $end_stamp);
            }
            $title = explode("(", $row->title);
            $array[] = array(
               "title" => $title[0],
               "start" => $dtstart,
               "end" => $dtend,
               "location" => $row->location
            );
         }
      }
      return $array;
   }

}
