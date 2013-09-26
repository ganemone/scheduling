<? 
class settings_model extends CI_Model
{
   function __construct()
   {
      date_default_timezone_set("UTC");
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
      $this->db->insert("event_settings", array("category_name" => $category_name, "category_abbr" => $category_abbr));
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
   function delete_goal($date)
   {
      $this->db->query("DELETE FROM goals WHERE date = '$date'");
   }
   /* -------------- */
   /* Get Functions */
   /* -------------- */
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
      return $this->db->query("SELECT date, goal FROM goals ORDER BY date")->result();
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
   function read_csv($csvFile) 
   {
      $file_handle = fopen($csvFile, 'r');

      while (!feof($file_handle) ) {

         $line_of_text[] = fgetcsv($file_handle, 1024);
      }

      fclose($file_handle);
      return $line_of_text;
   }
   function upload_goals($array)
   {
      return $array;
   }
}