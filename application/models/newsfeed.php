<?php

class newsfeed extends CI_Model
{

   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function getPosts()
   {
      $query = $this->db->query("SELECT * FROM messages ORDER BY timestamp DESC");
      $posts = array();
      foreach ($query->result() as $row)
      {
         $employee = $this->getEmployeeName($row->employeeId);
         $posted = Date("Y-m-d", strtotime($row->timestamp));
         $posts[] = array(
            "employee" => $employee,
            "datePosted" => $posted,
            "message" => $row->message,
            "messageId" => $row->id
         );
      }
      return $posts;
   }

   function getEmployeeName($id)
   {
      $query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$id'");
      if ($query->num_rows() == 0)
         return "false";
      $row = $query->row();
      return $row->firstName . " " . $row->lastName[0];
   }

   function addPost($employeeId, $message)
   {
      if ($this->getEmployeeName($employeeId) == "false")
         return "false";
      $query = $this->db->query("INSERT INTO messages (employeeId, message) VALUES ('$employeeId','$message')");
      return $query;
   }

   function updatePost($employeeId, $message, $messageId)
   {
      if ($this->getEmployeeName($employeeId) == "false")
         return "false";
      $query = $this->db->query("UPDATE messages SET message = '$message', employeeId = '$employeeId' WHERE id = '$messageId'");
      return $query;
   }

   function deletePost($messageId)
   {
      return $this->db->query("DELETE FROM messages WHERE id='$messageId'");
   }

}
?>