$(document).ready(function() {
	$(".table-container").css("height", $(document).height() - 120 + "px");

	setTimeout(function() {
		$(".bottom-right").fadeOut();
	}, 3000);
});

// --------------------
// Employees
// --------------------
function edit_employee (element, employeeId) {
	var td_arr = $(element).parent("td").parent("tr").children("td");
	form_obj = {
		name : "employee_form",
		id   : "employee_form",
		elements : [{
			type : "hidden",
			id   : "employeeId",
			name : "employeeId",
			value : employeeId
		},
		{
			type : "text",
			id   : "firstName",
			name : "firstName",
			label : "First Name: ",
			placeholder : "Enter employee first name here...",
			value : $(td_arr[1]).text()
		},
		{
			type : "text",
			id   : "lastName",
			name : "lastName",
			label : "Last Name: ",
			placeholder : "Enter employee last name here...",
			value : $(td_arr[2]).text()
		},
		{
			type : "text",
			id   : "password",
			name : "password",
			label : "Password",
			placeholder : "Enter employee password here...",
			value : $(td_arr[3]).text()
		},
		{
			type : "text",
			id   : "email",
			name : "email",
			label : "Email",
			placeholder : "johndoe@example.com",
			value : $(td_arr[6]).text()
		},
		{
			type : "text",
			id   : "wage",
			name : "wage",
			label : "Wage",
			placeholder : "ex: 8.00",
			value : $(td_arr[7]).text()
		},
		{
			type : "select",
			id   : "position",
			name : "position",
			label : "Position",
			data : {
				"SA" : { 
					name : "Sales Associate",
					selected : ($(td_arr[4]).text() == "SA") ? true : false
				},
				"SP" : {
					name : "Special",
					selected : ($(td_arr[4]).text() == "SA") ? true : false
				},
				"SFL" : {
					name : "Sales Floor Leader",
					selected : ($(td_arr[4]).text() == "SFL") ? true : false
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
					selected : ($(td_arr[5]).text() == "1") ? true : false
				},
				"2" : {
					name : "User + SFL",
					selected : ($(td_arr[5]).text() == "2") ? true : false
				},
				"3" : {
					name : "User + SFL + Manager",
					selected : ($(td_arr[5]).text() == "3") ? true : false
				}
			}
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/edit_employee", buildPostDataObj("#employee_form"), function(msg) {
				$(element).parent("td").parent("tr").replaceWith(msg);
				successMessage("Updated Employee");
			}, false);
		}
	});
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
			id   : "password",
			name : "password",
			label : "Password",
			placeholder : "Enter employee password here..."
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
function edit_group (group_id) {
	var form = {
		name : "group_edit",
		id : "group_edit",
		elements : [{
			type : "hidden",
			name : "group_id",
			id : "group_id",
			value : group_id
		},
		{
			type : "text",
			name : "name",
			id : "group_name",
			label : "Group Name: ",
			value : $(".table.table-condensed > tbody > tr.group-" + group_id + " td.group-name").text(),
			placeholder : "Enter group name here..."

		},
		{
			type : "text",
			name : "abbr",
			id : "group_abbr",
			label : "Group Abbreviation: ",
			value : $(".table.table-condensed > tbody > tr.group-" + group_id + " td.group-abbr").text(),
			placeholder : "Enter abbreviation here..."
		}]
	};
	bootbox.confirm(buildForm(form), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/edit_group", buildPostDataObj("#group_edit"), function(msg) {
				successMessage("Edited Group");
				var result = jQuery.parseJSON(msg);
				$(".table.table-condensed > tbody > tr.group-" + group_id + " td.group-name").replaceWith(result[0]);
				$(".table.table-condensed > tbody > tr.group-" + group_id + " td.group-abbr").replaceWith(result[1]);
			}, false);
		}
	});
}

function add_employee_to_group (groupId) {
	var form = "<form id='employee_group' class='form-horizontal'>";
	form += "<div class='form-group'>";
	form += "<label for='employee_select_list' class='control-label col-3'>Employees: </label>";
	form += "<div class='col-8'>";
	form += employee_select;
	form += "</div>";
	form += "</div>";
	form += "<br>"
	form += "</form>";
	bootbox.confirm(form, function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/add_employee_to_group", {
				id : $("#employee_select_list").val(),
				group_id : groupId
			}, function(msg) {
				successMessage("Added Employee to Group");
				$(".remove").remove();
				$(".table.table-condensed > tbody > tr.group-" + groupId + " > td.employees > table > tbody").append(msg);
			}, false);		
		}
	});
}

function remove_employee_from_group (employee_id, group_id) {
	bootbox.confirm("Are you sure you want to remove employee #" + employee_id + " from this group?", function(result)
	{
		if(result) {
			sendRequest("POST", url + "index.php/settings/remove_employee_from_group", {
				id : employee_id,
				group_id : group_id
			}, function(msg) {
				successMessage("Removed Employee from group");
				$(".table.table-condensed > tbody > tr.group-" + group_id + " > td.employees > table > tbody > tr.employee-" + employee_id).remove();
			}, false);
		}
	});
}

function add_group () {
	var form_obj = {
		name : "create_group",
		id : "create_group",
		elements : [{
			type : "text",
			name : "name",
			id : "name",
			label : "Group Name: ",
			placeholder : "Enter group name here..."
		},
		{
			type : "text",
			name : "abbr",
			id : "abbr",
			label : "Group Abbreviation: ",
			placeholder : "Enter abbreviation here..."
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/create_group", buildPostDataObj("#create_group"), function(msg) {
				successMessage("Created Group");
				$(".table.table-condensed > tbody").append(msg);
			});
		}
	});
}

function delete_group (group_id) {
	bootbox.confirm("Are you sure you want to delete this group?", function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/delete_group", {
				group_id : group_id
			}, function(msg) {
				$(".group-" + group_id).remove();
			}, false);
		}
	});
}
// --------------------
// Shift Categories
// --------------------
function edit_shift_category (category_abbr, category_name) {
	var form_obj = {
		name : "edit_shift_category",
		id : "edit_shift_category",
		elements : [{
			type : "hidden",
			name : "old_category",
			id : "old_category",
			value : category_abbr
		},
		{
			type : "text",
			name : "category_name",
			id : "category_name",
			label : "Name",
			value : category_name,
			placeholder : "Enter category name here..."
		},
		{
			type : "text",
			name : "category_abbr",
			id : "category_abbr",
			label : "Abbreviation",
			value : category_abbr,
			placeholder : "Enter category abbreviation here..."
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/edit_shift_category", buildPostDataObj("#edit_shift_category"), function(msg) {
				successMessage("Updated Shift Category");
				$(".table.table-condensed > tbody > tr." + category_abbr).replaceWith(msg);
			}, false);
		}
	});
}

function delete_shift_category (category_abbr) {
	bootbox.confirm("Are you sure you want to delete the shift category " + category_abbr + "?", function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/delete_shift_category", { category_abbr : category_abbr }, function(msg) {
				successMessage("Deleted Shift Category");
				$(".table.table-condensed > tbody > tr." + category_abbr).remove();
			}, false);
		}
	});
}

function add_shift_category () {
	var form_obj = {
		name : "add_shift_category",
		id : "add_shift_category",
		elements : [{
			type : "text",
			name : "category_name",
			id : "category_name",
			label : "Name: ",
			placeholder : "Enter category name here..."
		},
		{
			type : "text",
			name : "category_abbr",
			id : "category_abbr",
			label : "Abbreviation: ",
			placeholder : "Enter category abbreviation here..."
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/add_shift_category", buildPostDataObj("#add_shift_category"), function(msg) {
				successMessage("Created Shift Category");
				$(".table.table-condensed > tbody").append(msg);
			}, false);
		}
	});
}

// --------------------
// Goals
// --------------------
function edit_goal (goal_id) {
	var form_obj = {
		name : "edit_goal",
		id : "edit_goal",
		elements : [{
			type : "hidden",
			name : "goal_id",
			id : "goal_id",
			value : goal_id
		},
		{
			type : "text",
			name : "goal",
			id : "goal",
			label : "Goal: ",
			placeholder : "ex: 10000",
			value : $(".table.table-condensed > tbody > tr.goal-" + goal_id + " > td.goal").text()
		}]
	};
	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			var data = buildPostDataObj("#edit_goal");
			sendRequest("POST", url + "index.php/settings/edit_goal", data, function(msg) {
				$(".table.table-condensed > tbody > tr.goal-" + goal_id + " > td.goal").text(data.goal);
				successMessage("Updated Goal");
			});
		}
	});
}

function delete_goal (goal_id) {
	bootbox.confirm("Are you sure you want to delete this goal?", function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/delete_goal", { goal_id : goal_id }, function(msg) {
				successMessage("Deleted goal");
				$(".table.table-condensed > tbody > tr.goal-" + goal_id).remove();
			});
		}
	});
}

function add_goal () {
	
}
// --------------------
// Missed Sales
// --------------------
function edit_missed_sale (sale_id) {
	var parent = $(".table.table-condensed > tbody > tr.sale-" + sale_id);
	var form_obj = {
		name : "edit_missed_sale",
		id : "edit_missed_sale",
		elements : [{
			type : "hidden",
			name : "sale_id",
			id : "sale_id",
			value : sale_id
		},
		{
			type : "text",
			name : "price",
			id : "price",
			label : "Price: ",
			placeholder : "ex: 100.00",
			value : parent.children("td.price").text()
		},
		{
			type : "text",
			name : "vendor",
			id : "vendor",
			label : "Vendor: ",
			placeholder : "Enter vendor here...",
			value : parent.children("td.vendor").text()
		},
		{
			type : "text",
			name : "description",
			id : "description",
			label : "Description: ",
			placeholder : "Enter description here...",
			value : parent.children("td.description").text()
		},
		{
			type : "text",
			name : "size",
			id : "size",
			label : "Size: ",
			placeholder : "Enter size here...",
			value : parent.children("td.size").text()
		},
		{
	        type : "select",
	        name : "category",
	        "label_class" : "control-label col-3",
	        "input_class" : "col-8",
	        id : "category",
	        label : "Category: ",
	        data : {
	            "Accessories"    : {
	               "name" : "Accessories",
	               "selected" : (parent.children("td.category").text() == "Accessories") ? true : false
	            },
	            "Casual Apparel" : {
	               "name" : "Casual Apparel",
	               "selected" : (parent.children("td.category").text() == "Casual Apparel") ? true : false
	            },
	            "Casual Shoes"   : {
	               "name" : "Casual Shoes",
	               "selected" : (parent.children("td.category").text() == "Casual Shoes") ? true : false
	            },
	            "Cross Training" : {
	               "name" : "Cross Training",
	               "selected" : (parent.children("td.category").text() == "Cross Training") ? true : false
	            },
	            "Nutrition"      : {
	               "name" : "Nutrition",
	               "selected" : (parent.children("td.category").text() == "Nutrition") ? true : false
	            },
	            "Run Apparel"    : {
	               "name" : "Run Apparel",
	               "selected" : (parent.children("td.category").text() == "Run Apparel") ? true : false
	            },
	            "Road Shoes"     : {
	               "name" : "Road Shoes",
	               "selected" : (parent.children("td.category").text() == "Road Shoes") ? true : false
	            },
	            "Soccer"         : {
	               "name" : "Soccer",
	               "selected" : (parent.children("td.category").text() == "Soccer") ? true : false
	            },
	            "Swim/Tri"       : {
	               "name" : "Swim/Tri",
	               "selected" : (parent.children("td.category").text() == "Swim/Tri") ? true : false
	            },
	            "Trail Shoes"    : {
	               "name" : "Trail Shoes",
	               "selected" : (parent.children("td.category").text() == "Trail Shoes") ? true : false
	            },
	            "Other"          : {
	               "name" : "Other",
	               "selected" : (parent.children("td.category").text() == "Other") ? true : false
	            }
         	}
      	},
      	{
	        type : "select",
	        name : "gender",
	        "label_class" : "control-label col-3",
	        "input_class" : "col-8",
	        id : "gender",
	        label : "Gender: ",
	        data : {
	            "M"    : {
	               "name" : "M",
	               "selected" : (parent.children("td.gender").text() == "M") ? true : false
	            },
	            "W"    : {
	               "name" : "W",
	               "selected" : (parent.children("td.gender").text() == "W") ? true : false
	            },
	            "NA"    : {
	               "name" : "NA",
	               "selected" : (parent.children("td.gender").text() == "NA") ? true : false
	            }
	        }
	    },
		{
			type : "text",
			name : "quantity",
			id : "quantity",
			label : "Quantity: ",
			placeholder : "Enter quantity here...",
			value : parent.children("td.quantity").text()
		}]
	};

	bootbox.confirm(buildForm(form_obj), function(result) {
		if(result) {
			var data = buildPostDataObj("#edit_missed_sale");
			sendRequest("POST", url + "index.php/settings/edit_missed_sale", data, function(msg) {
				
				parent.children("td.price").text(data.price);
				parent.children("td.vendor").text(data.vendor);
				parent.children("td.description").text(data.description);
				parent.children("td.size").text(data.size);
				parent.children("td.category").text(data.category);
				parent.children("td.gender").text(data.gender);
				parent.children("td.quantity").text(data.quantity);

				successMessage("Updated Missed Sale");

			}, false);
		}
	});
}

function delete_missed_sale (sale_id) {
	bootbox.confirm("Are you sure you want to delete this missed sale?", function(result) {
		if(result) {
			sendRequest("POST", url + "index.php/settings/delete_missed_sale", { sale_id : sale_id }, function(result) {
				$(".table.table-condensed > tbody > tr.sale-" + sale_id).remove();
				successMessage("Deleted missed sale");
			});
		}
	});
}