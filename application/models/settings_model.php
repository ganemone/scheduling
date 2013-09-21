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
      return $this->db->where("id", $id)->update("employees", $update_arr);
   }
   function delete_employee($employee_id)
   {
      $this->db->query("DELETE FROM requests USING requests LEFT JOIN scheduled ON requests.shiftId = scheduled.id WHERE scheduled.employeeId = '$employee_id'");
      $this->db->query("DELETE FROM hours WHERE employeeId = '$employee_id'");
      $this->db->query("DELETE FROM scheduled WHERE employeeId = '$employee_id'");
      $this->db->query("DELETE FROM employees WHERE id = '$employee_id'");
   }
   function delete_employee_group($group_id, $group_abbr)	
   {
      $this->db->query("DELETE FROM employee_groups WHERE group_id = '$group_id'");
      $this->db->query("UPDATE employees SET groups = ''");
      return $this->reset_employee_group_values();
   }
   function add_employee_to_group($employee_id, $group_id, $group_abbr)
   {
      $this->db->query("UPDATE employees SET groups = CONCAT(groups, '$group_abbr') WHERE id='$employee_id'");
      return $this->db->insert("employee_groups", array("employee_id" => $employee_id, "group_id" => $group_id));
   }
   function remove_employee_from_group($employee_id, $group_id)
   {
      $this->db->query("DELETE FROM employee_groups WHERE employee_id = '$employee_id' && group_id = '$group_id'");
      return $this->reset_employee_group_values();
   }
   function create_employee_group($group_name, $group_abbr)
   {
      return $this->db->insert("groups", array("name" => $group_name, "abbr" => $group_abbr));
   }
   function delete_shift_category($category_abbr)
   {
      return $this->db->query("DELETE FROM event_settings WHERE category_abbr = '$category_abbr'");
   }
   function add_shift_category($category_name, $category_abbr)
   {
      if($this->db->get_where("event_settings", array("category_abbr", $category_abbr))->count_all_results() > 0) {
         return "error";
      }
      return $this->db->insert("event_settings", array("category_name" => $category_name, "category_abbr" => $category_abbr, ""));
   }
   function edit_shift_category($update_arr, $where_arr)
   {
      return $this->db->where($where_arr)->update("event_settings", $update_arr);
   }
   function reset_employee_group_values()
   {
      $query = $this->db->query("SELECT employee_groups.employee_id, groups.abbr FROM employee_groups, groups, WHERE employee_groups.group_id = groups.group_id");
      foreach ($query->result() as $row) {
         $this->db->query("UPDATE employees SET groups = CONCAT(groups, '{$row->abbr}') WHERE id='{$row->employeeId}'");
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
      return $this->db->query("SELECT id, firstName, lastName, position, permissions, email, wage FROM employees ORDER BY firstName")->result();
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
      return $this->db->query("SELECT event_id, category_name, category_abbr FROM event_settings WHERE category_abbr != 'A' && category_abbr !='SFL' && category_abbr !='B' && category_abbr != 'C' && category_abbr !='SF' && category_abbr !='E'")->result();
   }
   function get_goals()
   {
      return $this->db->query("SELECT date, goal FROM goals ORDER BY date")->result();
   }
}