<?php
class sfl extends CI_Controller
{
   public $info;
   function __construct()
   {
      parent::__construct();
      $this->load->helper('cookie');
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->model("leader");
      $this->load->model("newsfeed");
      $this->load->library('email');
      $this->load->library('user_agent');
      $this->info['mobile'] = $this->agent->is_mobile();
   }

   function index()
   {
      $this->info['userId'] = $this->input->cookie("employeeId");
      if (!($this->info))
         header("location: " . base_url() . "index.php/site");
      else
      {
         $this->info['events'] = TRUE;
         $this->info['support'] = TRUE;
         $this->load->view("includes");
         $this->load->view("/sfl/sflCSS.html");
         $this->load->view("/sfl/sflCalendar", $this->info);
         $this->getNewsfeed();
      }
   }

   function printable()
   {
      $this->info['userId'] = $this->input->cookie("employeeId");
      if (!($this->info))
         header("location: " . base_url() . "index.php/site");
      else
      {
         $this->info['events'] = $this->input->get("events");
         $this->info['support'] = $this->input->get("support");

         $this->load->view("includes");
         //$this->load->view("/sfl/sflCSS.html");
         $this->load->view("/sfl/print.php");
         $this->load->view("/sfl/sflCalendar", $this->info);
      }
   }

   function logOut()
   {
      delete_cookie("EmployeeId");
      header("location: " . base_url() . "index.php/site");
   }

   function floorEventSource()
   {
      $json = $this->leader->floorEventSource();
      $this->echoJSON($json);
   }

   function echoJSON($json)
   {
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

   function supportEventSource()
   {
      $json = $this->leader->supportEventSource();
      $this->echoJSON($json);
   }
   
   function getNewsfeed()
   {
      $this->info['posts'] = $this->newsfeed->getPosts();
      $this->load->view("newsfeed.php", $this->info);
   }

   function addMissedSale()
   {
      $style = mysql_real_escape_string($this->input->post("style"));
      $color = mysql_real_escape_string($this->input->post("color"));
      $desc = mysql_real_escape_string($this->input->post('description'));
      $size = mysql_real_escape_string($this->input->post('size'));
      $date = Date("Y-m-d", strtotime($this->input->post('date')));
      $price = $this->input->post('price');
      echo $this->leader->addMissedSale($style, $color, $desc, $size, $price, $date);
   }
   
   function addStory()
   {
      $employeeId = mysql_real_escape_string($this->input->post("employeeId"));
      $story = mysql_real_escape_string($this->input->post("story"));
      $date = Date("Y-m-d", strtotime($this->input->post("date")));
      echo $this->leader->addStory($employeeId, $story, $date);
   }
   
   function getEmailTemplate()
   {
      echo $this->leader->getEmailTemplate(Date("Y-m-d", strtotime($this->input->get("date"))));
   }

}
