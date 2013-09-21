$(document).ready(function() {
	$(".table-container").css("height", $(document).height() - 170 + "px");
});
// --------------------
// Employees
// --------------------
function edit_employee (element, employeeId) {
	console.log($(element).parent("td").parent("tr"));
}

function delete_employee (element, name, employeeId) {
	bootbox.confirm("Are you sure you want to delete " + name + "? This will perminently delete all shifts, availability, and information about this employee.", function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/delete_employee", {
				employeeId: employeeId
			}, function(msg) {
				$(element).parent("td").parent("tr").remove();
				successMessage("Deleted Employee");
			}, false);
		}
	});
}

function add_employee () {
	form_obj = {
		name : "employee_form",
		id   : "employee_form",
		elements : [{
			type : "text",
			id   : "firstName",
			name : "firstName",
			label : "First Name: ",
			placeholder : "Enter employee first name here..."
		},
		{
			type : "text",
			id   : "lastName",
			name : "lastName",
			label : "Last Name: ",
			placeholder : "Enter employee last name here..."	
		},
		{
			type : "text",
			id   : "email",
			name : "email",
			label : "Email",
			placeholder : "johndoe@example.com"
		},
		{
			type : "text",
			id   : "wage",
			name : "wage",
			label : "Wage",
			placeholder : "ex: 8.00"
		},
		{
			type : "select",
			id   : "position",
			name : "position",
			label : "Position",
			data : {
				"SA" : { 
					name : "Sales Associate",
					selected : true
				},
				"SP" : {
					name : "Special",
					selected : false
				},
				"SFL" : {
					name : "Sales Floor Leader",
					selected : false
				}
			}
		},
		{
			type : "select",
			id   : "permissions",
			name : "permissions",
			label : "Calendar Permissions",
			data : {
				"1" : {
					name : "User",
					selected : true
				},
				"2" : {
					name : "User + SFL",
					selected : false,
				},
				"3" : {
					name : "User + SFL + Manager",
					selected : false
				}
			}
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/add_employee", buildPostDataObj("#employee_form"), function(msg) {
				successMessage("Created Employee");
				$(".table.table-condensed").append(msg);
			}, false);
		}
	});
}
// --------------------
// Groups
// --------------------
function edit_group () {

}

function add_employee_to_group () {

}

function remove_employee_from_group () {
	
}

function add_group () {
	
}

function delete_group () {
	
}
// --------------------
// Shift Categories
// --------------------
function edit_shift_category () {
	
}

function delete_shift_category () {
	
}

function add_shift_category () {
	
}

// --------------------
// Goals
// --------------------
function edit_goal () {
	
}

function delete_goal () {
	
}

function add_goal () {
	
}