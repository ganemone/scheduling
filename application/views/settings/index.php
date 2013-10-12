    	<script src="<? echo base_url() ?>js/utility.js"></script>
      <div class='notifications top-right'></div>
      <br>
    	<div class='jumbotron'>
    		<h2>Select from one of the options below.</h2>
    		<hr>
   			<a href='<? echo base_url() . "index.php/settings/employees" ?>' class='btn btn-primary btn-large calendar-button'>Employees</a>
   			<a href='<? echo base_url() . "index.php/settings/groups" ?>' class='btn btn-primary btn-large calendar-button'>Employee Groups</a>
   			<a href='<? echo base_url() . "index.php/settings/shifts" ?>' class='btn btn-primary btn-large calendar-button'>Shift Categories</a><br>
   			<a href='<? echo base_url() . "index.php/settings/goals" ?>' class='btn btn-primary btn-large calendar-button'>Sales Goals</a>
   			<a href='<? echo base_url() . "index.php/settings/sales" ?>' class='btn btn-primary btn-large calendar-button'>Missed Sales List</a>
        <a href='<? echo base_url() . "index.php/settings/stats" ?>' class='btn btn-primary btn-large calendar-button'>Missed Sales Stats</a>
    	
      </div>
      <hr>
      <br>
      <div class='jumbotron'>
        <h2>Purge Options</h2>
        <hr>
        <button onclick="purge_all();"          class='btn btn-danger btn-large calendar-button'>All</button>
        <button onclick="purge_availability();" class='btn btn-danger btn-large calendar-button'>Availability</button>
        <button onclick="purge_scheduled();"    class='btn btn-danger btn-large calendar-button'>Scheduled</button><br>
        <button onclick="purge_events();"       class='btn btn-danger btn-large calendar-button'>Events</button>
        <button onclick="purge_missed_sales();" class='btn btn-danger btn-large calendar-button'>Missed Sales</button>
        <button onclick="purge_goals();"        class='btn btn-danger btn-large calendar-button'>Sales Goals</button>
      </div>
    </body>
    <script type='text/javascript'>

    function purge_all () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all availability, scheduled, events, missed sales, and goal information? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_all", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    function purge_availability () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all availability events? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_availability", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    function purge_scheduled () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all scheduled events? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_scheduled", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    function purge_events () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all events? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_events", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    function purge_missed_sales () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all missed sales? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_missed_sales", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    function purge_goals () {
      bootbox.prompt("Enter the date to purge records from.  All records from before this date will be removed.", function(date) {
        if(date) {
          bootbox.confirm("Are you sure you want to purge all sales goals? This action is final and cannot be undone.", function(result) {
            if(result) {
              sendRequest("POST", url + "index.php/settings/purge_goals", { date : date }, function(msg) {
                successMessage("Purged records");
              });
            }
          });
        }
      });
    }

    </script>
</html>
