<?php
class News extends CI_Controller
{
   public $data;
   function __construct()
   {
      parent::__construct();
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->model('newsfeed');
      $this->load->library('user_agent');
   }
   function deletePost()
   {
      $messageId = $this->input->get("messageId");
      echo $this->newsfeed->deletePost($messageId);
   }

   function getNewsfeed()
   {
      $this->data['mobile'] = $this->agent->is_mobile();
      $this->data['posts'] = $this->newsfeed->getPosts();
      $this->load->view("newsfeed.php", $this->data);
   }

   function addNewsfeedPost()
   {
      $employeeId = $this->input->get("employeeId");
      $message = mysql_real_escape_string($this->input->get('message'));
      echo $this->newsfeed->addPost($employeeId, $message);
   }

   function updateNewsfeedPost()
   {
      $employeeId = $this->input->get("employeeId");
      $message = mysql_real_escape_string($this->input->get('message'));
      $messageId = $this->input->get('messageId');
      echo $this->newsfeed->updatePost($employeeId, $message, $messageId);
   }

   function reloadNewsfeed()
   {
      $this->data['mobile'] = $this->agent->is_mobile();
      $this->data['posts'] = $this->newsfeed->getPosts();
      $html = $this->load->view("newsfeed.php", $this->data, true);
      echo $html;
   }

}
