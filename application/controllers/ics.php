<?php

class ics extends CI_Controller
{
   public $data;
   public $google;
   function __construct()
   {
      $this->data = "";

      parent::__construct();
      date_default_timezone_set("UTC");
      $this->load->model("exporter");
   }

   function save($name)
   {
      file_put_contents($name . ".ics", $this->data);
   }

   function index()
   {
      $this->ICS_Begin();
      $employeeId = $this->input->get("employeeId");
      $start = $this->input->get("start");
      $end = $this->input->get('end');
      $google = $this->input->get("google");
      $this->google = $google;
      $array = $this->exporter->getScheduledExports($employeeId, $start, $end, $google);
      foreach ($array as $value)
      {
         $title = $value['title'];
         $start = $value['start'];
         $end = $value['end'];
         $this->ICS_Event($value['title'], $value['start'], $value['end'], $value['location'], "");
      }
      $this->ICS_End();
      $this->save("calendar_file");
      header("Content-type: text/Calendar");
      header("Content-Disposition: inline; filename=calendar_file.ics");
      readFile("calendar_file.ics");
   }

   function ICS_Begin()
   {
      $this->data .= "BEGIN:VCALENDAR\n";
      $this->data .= "PRODID:-//Microsoft Corporation//Outlook 14.0 MIMEDIR//EN\n";
      $this->data .= "VERSION:2.0\n";
      $this->data .= "METHOD:PUBLISH\n";
   }

   function ICS_Event($title, $start, $end, $location, $description)
   {
      $this->data .= "BEGIN:VEVENT\n";
      $this->data .= "CLASS:PUBLIC\n";
      $this->data .= "CREATED:" . time() . "\n";
      $this->data .= "DESCRIPTION: $description\n";
      if ($this->google == "on")
      {
         $this->data .= "DTEND:$end\n";
         $this->data .= "DTSTAMP:" . gmdate('Ymd') . 'T' . gmdate('His') . "Z\n";
         $this->data .= "DTSTART:$start\n";
      }
      else
      {
         $this->data .= "DTEND;TZID=\"Detroit, U.S.A.\":$end\n";
         $this->data .= "DTSTAMP:" . gmdate('Ymd') . 'T' . gmdate('His') . "\n";
         $this->data .= "DTSTART;TZID=\"Detroit, U.S.A.\":$start\n";
      }
      $this->data .= "LAST-MODIFIED:" . time() . "\n";
      $this->data .= "LOCATION:$location\n";
      $this->data .= "PRIORITY:5\n";
      $this->data .= "SEQUENCE:0\n";
      $this->data .= "SUMMARY;LANGUAGE=en-us:$title\n";
      $this->data .= "TRANSP:OPAQUE\n";
      $this->data .= "UID:" . md5(time() * rand(1, 1000) . $start . $title) . "\n";
      $this->data .= "END:VEVENT\n";
   }

   function ICS_End()
   {
      $this->data .= "END:VCALENDAR\n";
   }

}
