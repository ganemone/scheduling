<? 
class settings_model extends CI_Model
{
   function __construct()
   {
      date_default_timezone_set("UTC");
      $this->load->model("validator");
   }
   function add_employee($insert_arr)
   {
      $query = $this->db->insert("employees", $insert_arr);
      $ret = "<tr>";
      $ret .= "<td></td>";
      $ret .= "<td>" . $insert_arr["firstName"] . "</td>";
      $ret .= "<td>" . $insert_arr["lastName"] . "</td>";
      $ret .= "<td>" . $insert_arr["password"] . "</td>";
      $ret .= "<td>" . $insert_arr["position"] . "</td>";
      $ret .= "<td>" . $insert_arr["permissions"] . "</td>";
      $ret .= "<td>" . $insert_arr["email"] . "</td>";
      $ret .= "<td>" . $insert_arr["wage"] . "</td>";
      $ret .= "<td>Refresh Page</td><td>To See More</td>";
      $ret .= "</tr>";
      return $ret;
   }
   function edit_employee($update_arr, $id)
   {
      $result = $this->db->where("id", $id)->update("employees", $update_arr);
      $ret = "<tr>";
      $ret .= "<td>$id</td>";
      $ret .= "<td>" . $update_arr["firstName"] . "</td>";
      $ret .= "<td>" . $update_arr["lastName"] . "</td>";
      $ret .= "<td>" . $update_arr["password"] . "</td>";
      $ret .= "<td>" . $update_arr["position"] . "</td>";
      $ret .= "<td>" . $update_arr["permissions"] . "</td>";
      $ret .= "<td>" . $update_arr["email"] . "</td>";
      $ret .= "<td>" . $update_arr["wage"] . "</td>";
      $ret .= "<td>Refresh Page</td><td>To See More</td>";
      $ret .= "</tr>";
      return $ret;
   }
   function delete_employee($employee_id)
   {
      $this->db->query("DELETE FROM requests USING requests LEFT JOIN scheduled ON requests.shiftId = scheduled.id WHERE scheduled.employeeId = '$employee_id'");
      $this->db->query("DELETE FROM hours WHERE employeeId = '$employee_id'");
      $this->db->query("DELETE FROM scheduled WHERE employeeId = '$employee_id'");
      $this->db->query("DELETE FROM employees WHERE id = '$employee_id'");
   }
   function add_employee_to_group($employee_id, $group_id)
   {
      if($this->db->query("SELECT Count(*) AS count FROM employee_groups WHERE employee_id = '$employee_id' && group_id = '$group_id'")->row()->count > 0) {
         return "employee already in group";
      }
      $name = $this->db->query("SELECT name FROM groups WHERE group_id = '$group_id'")->row()->abbr;
      $this->db->query("UPDATE employees SET groups = CONCAT(groups, '$name ') WHERE id='$employee_id'");
      $this->db->insert("employee_groups", array("employee_id" => $employee_id, "group_id" => $group_id));
      $employee = $this->db->query("SELECT firstName, lastName FROM employees WHERE id = '$employee_id'")->row();
      $ret = "<tr class='employee-$employee_id'>";
      $ret.= "<td>{$employee->firstName} {$employee->lastName}</td>";
      $ret.= "<td><button class='btn btn-primary' onclick='remove_employee_from_group($employee_id, $group_id);'>Remove</button></td></tr>";
      return $ret;
   }
   function remove_employee_from_group($employee_id, $group_id)
   {
      $this->db->query("DELETE FROM employee_groups WHERE employee_id = '$employee_id' && group_id = '$group_id'");
      return $this->reset_employee_group_values();
   }
   function create_employee_group($insert_arr)
   {
      if($this->db->query("SELECT Count(*) AS count FROM groups WHERE name = '" . $insert_arr["name"] . "' || abbr = '" . $insert_arr["abbr"] . "'")->row()->count > 0) {
         return "error";
      }
      $this->db->insert("groups", $insert_arr);
      $group_id = $this->db->query("SELECT group_id FROM groups WHERE abbr = '" . $insert_arr["abbr"] . "'")->row()->group_id;
      $ret = "<tr class='group-$group_id'>";
      $ret.= "<td class='group-name'>" . $insert_arr["name"] . "</td>";
      $ret.= "<td class='group-abbr'>" . $insert_arr["abbr"] . "</td>";
      $ret.= "<td class='employees'><table class='table'><tr class='remove'><td></td><td></td></tr></table>";
      $ret.= "<td><button class='btn btn-primary' onclick='edit_group($group_id);'>Edit Group</button></td>";
      $ret.= "<td><button class='btn btn-danger'  onclick='delete_group($group_id);'>Delete Group</button></td>";
      $ret.= "<td><button class='btn btn-success' onclick='add_employee_to_group($group_id);'>Add Employee</button></td>";
      $ret.= "</tr>";
      return $ret;
   }
   function edit_employee_group($update_arr, $group_id)
   {
      if($this->db->query("SELECT COUNT(*) AS count FROM groups WHERE name = '" . $update_arr["name"] . "' || abbr = '" . $update_arr["abbr"] . "' && group_id != '$group_id'")->row()->count > 0) {
         return "error";
      }
      $this->db->where("group_id", $group_id)->update("groups", $update_arr);
      $this->reset_employee_group_values();
      return array("<td class='group-name'>" . $update_arr["name"] . "</td>", "<td class='group-abbr'>" . $update_arr["abbr"] . "</td>");
   }
   function delete_employee_group($group_id)
   {
      $this->db->query("DELETE FROM groups WHERE group_id = '$group_id'");
      $this->db->query("DELETE FROM employee_groups WHERE group_id = '$group_id'");
      return $this->reset_employee_group_values();
   }
   function delete_shift_category($category_abbr)
   {
      $this->db->query("DELETE FROM event_settings WHERE category_abbr = '$category_abbr'");
      return $this->db->query("UPDATE scheduled SET category = 'SF' WHERE category = '$category_abbr'");
   }
   function add_shift_category($category_name, $category_abbr)
   {
      if($this->db->query("SELECT COUNT(*) AS count FROM event_settings WHERE category_abbr = '$category_abbr'")->row()->count > 0) {
         return "error";
      }
      $this->db->insert("event_settings", array("category_name" => $category_name, "category_abbr" => $category_abbr, "color" => "3366CC", "border" => "3366CC"));
      $ret = "<tr class='$category_abbr'>";
      $ret.= "<td>$category_name</td>";
      $ret.= "<td>$category_abbr</td>";
      $ret.= "<td><button class='btn btn-primary' onclick='edit_shift_category(\"$category_abbr\", \"$category_name\");'>Edit</button></td>";
      $ret.= "<td><button class='btn btn-danger'  onclick='delete_shift_category(\"$category_abbr\");'>Delete</button></td>";
      $ret.= "</tr>";
      return $ret;
   }
   function edit_shift_category($update_arr, $old_abbr)
   {
      if($update_arr["category_abbr"] != $old_abbr && $this->db->query("SELECT COUNT(*) AS count FROM event_settings WHERE category_abbr = '" . $update_arr["category_abbr"] . "'")->row()->count > 0) {
         return "error";
      }
      $this->db->where("category_abbr", $old_abbr)->update("event_settings", $update_arr);
      $this->db->query("UPDATE scheduled SET category = '" . $update_arr["category_abbr"] . "' WHERE category = '$old_abbr'");
      $ret = "<tr class='" .  $update_arr["category_abbr"] . "'>";
      $ret.= "<td>" . $update_arr["category_name"] . "</td>";
      $ret.= "<td>" . $update_arr["category_abbr"] . "</td>";
      $ret.= "<td><button class='btn btn-primary' onclick='edit_shift_category(\"" . $update_arr["category_abbr"] . "\", \"" . $update_arr["category_name"] . "\");'>Edit</button></td>";
      $ret.= "<td><button class='btn btn-danger'  onclick='delete_shift_category(\"" . $update_arr["category_abbr"] . "\");'>Delete</button></td>";
      $ret.= "</tr>";
      return $ret;
   }
   function reset_employee_group_values()
   {
      $this->db->query("UPDATE employees SET groups = ''");
      $query = $this->db->query("SELECT employee_groups.employee_id, groups.name FROM employee_groups, groups WHERE employee_groups.group_id = groups.group_id");
      foreach ($query->result() as $row) {
         $this->db->query("UPDATE employees SET groups = CONCAT(groups, '{$row->name} ') WHERE id='{$row->employee_id}'");
      }
      return $query;
   }
   function insert_goal($goal, $date)
   {
      $this->db->query("DELETE FROM goals WHERE date = '$date'");
      $this->db->insert("goals", array("date" => $date, "goal" => $goal));
   }
   function edit_missed_sale($sale_id, $update_arr)
   {
      return $this->db->where("id", $sale_id)->update("missedSales", $update_arr);
   }
   function delete_missed_sale($sale_id)
   {
      return $this->db->query("DELETE FROM missedSales WHERE id = '$sale_id'");
   }
   /* -------------- */
   /* Get Functions */
   /* -------------- */
   function get_missed_sales($order)
   {
      switch ($order) {
         case '1': $sql = "date, description"; break;
         case '2': $sql = "price, date"; break;
         case '3': $sql = "vendor, date"; break;
         case '4': $sql = "description, date"; break;
         case '5': $sql = "size, date"; break;
         case '6': $sql = "category, date"; break;
         case '7': $sql = "quantity, date"; break;
         case '8': $sql = "gender, date"; break;
         default: $sql = "date, description"; break;
      }
      return $this->db->query("SELECT id, description, date, size, price, quantity, category, vendor, gender FROM missedSales ORDER BY $sql")->result();
   }
   function get_employees()
   {
      return $this->db->query("SELECT id, firstName, lastName, password, position, permissions, email, wage FROM employees ORDER BY firstName")->result();
   }
   function get_employee_groups()
   {
      $query = $this->db->query("SELECT * FROM groups");
      $ret = array();
      foreach ($query->result() as $row) {
         $ret[] = array("name" => $row->name,
            "group_id" => $row->group_id,
            "abbr" => $row->abbr,
            "employees" => $this->db->query("SELECT employees.id, employees.firstName, employees.lastName 
            FROM employee_groups 
            LEFT JOIN employees 
            ON employee_groups.employee_id = employees.id
            WHERE employee_groups.group_id = '$row->group_id'")->result());
      }
      return $ret;
   }
   function get_shift_categories()
   {
      return $this->db->query("SELECT category_name, category_abbr FROM event_settings WHERE category_abbr != 'A' && category_abbr !='SFL' && category_abbr !='B' && category_abbr != 'C' && category_abbr !='SF' && category_abbr !='E'")->result();
   }
   function get_goals()
   {
      return $this->db->query("SELECT * FROM goals ORDER BY date")->result();
   }
   function get_employee_select()
   {
      $query = $this->db->query("SELECT firstName, lastName, id FROM employees ORDER BY firstName, lastName");
      $ret = "<select id='employee_select_list' class='form-control'>";
      foreach ($query->result() as $row) {
         $ret .= "<option value='{$row->id}'>{$row->firstName} {$row->lastName[0]}</option>";
      }
      return $ret;
   }
   function edit_goal($goal_id, $new_goal)
   {
      return $this->db->query("UPDATE goals SET goal = '$new_goal' WHERE id='$goal_id'");
   }
   function delete_goal($goal_id)
   {
      return $this->db->query("DELETE FROM goals WHERE id = '$goal_id'");
   }
   function read_csv($csvFile) 
   {
      $file_handle = fopen($csvFile, 'r');

      $file = "";
      while (!feof($file_handle)) {
         $file .= fgets($file_handle);
      }
      fclose($file_handle);
      
      $result = preg_split("/(\s|$|,)/", $file);
      array_pop($result);

      return $result;
   }
   function upload_goals($goals_arr)
   {
      for ($i=0; $i < count($goals_arr); $i = $i + 2) { 
         $date = date("Y-m-d", strtotime($goals_arr[$i]));
         $goal = trim(intval($goals_arr[$i + 1]));
         if($this->validator->valid_date($date) && $this->validator->valid_goal($goal)) {
            if($this->db->query("SELECT COUNT(*) AS count FROM goals WHERE date = '$date'")->row()->count > 0) {
               $this->db->query("UPDATE goals SET goal = '" . $goal . "' WHERE date = '$date'");
            }
            else {
               $this->db->insert("goals", array("date" => $date, "goal" => $goal));
            }
         }
         else {
            //return false;
         }
      }
      return true;
   }
   function get_vendor_graph($date)
   {
      $query = $this->db->query("SELECT vendor, SUM(quantity * price) as amount FROM missedSales GROUP BY vendor");
      $graph_data = array();
      foreach ($query->result() as $row) {
         $graph_data[] = array("vendor" => $row->vendor, "amount" => $row->amount);
      }
      return $graph_data;
   }
   function get_category_graph($date)
   {
      $query = $this->db->query("SELECT category, SUM(quantity * price) AS amount FROM missedSales GROUP BY category");
      $graph_data = array();
      foreach ($query->result() as $row) {
         $graph_data[] = array("category" => $row->category, "amount" => $row->amount);
      }
      return $graph_data;
   }

   function get_time_graph($date)
   {
      $ret_arr = array();
      while(strtotime($date) <= time()) {
         $date = date("Y-m-01", strtotime($date));
         $next_month = date("Y-m-01", strtotime($date . " next month"));
         $row = $this->db->query("SELECT SUM(quantity * price) AS amount FROM missedSales WHERE date >= '$date' && date <= '$next_month'")->row();
         $ret_arr[] = array("date" => $date, "amount" => $row->amount);
         $date = $next_month;
      }
      return $ret_arr;
   }

   function get_gender_graph($date)
   {
      $query = $this->db->query("SELECT gender, SUM(quantity * price) AS amount FROM missedSales GROUP BY gender");
      $graph_data = array();
      foreach ($query->result() as $row) {
         $graph_data[] = array("gender" => $row->gender, "amount" => $row->amount);
      }
      return $graph_data;
   }

   function purge_availability($date)
   {
      $this->db->query("DELETE FROM weekInfo WHERE month <= '$date'");
      return $this->db->query("DELETE FROM hours WHERE day <= '$date'");
   }

   function purge_scheduled($date)
   {
      return $this->db->query("DELETE FROM scheduled WHERE day <= '$date'");
   }

   function purge_goals($date)
   {
      return $this->db->query("DELETE FROM goals WHERE date <= '$date'");
   }

   function purge_missed_sales($date)
   {
      return $this->db->query("DELETE FROM missedSales WHERE date <= '$date'");
   }

   function purge_events($date)
   {
      return $this->db->query("DELETE FROM events WHERE date <= '$date'");
   }

   function purge_stories($date)
   {
      return $this->db->query("DELETE FROM stories WHERE date <= '$date'");
   }
}