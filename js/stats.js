$(document).ready(function() {
  time_graph();
  category_graph();
  gender_graph();
  vendor_graph();
});

function time_graph () {

  var data = [];
  $("#date_table > tbody > tr").each(function() {
    data.push({ 
      date : $(this).children("td.date").text(),
      amount : ($(this).children("td.amount").text() == "") ? 0 : (Number($(this).children("td.amount").text()))
    });
  });
  
  new Morris.Line({
    // ID of the element in which to draw the chart.
    element: 'time',
    // Chart data records -- each entry in this array corresponds to a point on
    // the chart.
    data: data,
    // The name of the data record attribute that contains x-values.
    xkey: 'date',
    // A list of names of data record attributes that contain y-values.
    ykeys: ['amount'],
    // Labels for the ykeys -- will be displayed when you hover over the
    // chart.
    labels: ['Amount']
  });
}

function category_graph () {
  var data = [];
  $("#category_table > tbody > tr").each(function() {
    data.push({
      category : $(this).children("td.category").text(), amount: (Number($(this).children("td.amount").text()))
    });
  });
  Morris.Bar({
    element: 'category',
    data: data,
    xkey: 'category',
    ykeys: ['amount'],
    labels: ['Category']
  });
}

function gender_graph () {
  var data = [];
  $("#gender_table > tbody > tr").each(function() {
    data.push({
      label : $(this).children("td.gender").text(), value: (Number($(this).children("td.amount").text()))
    });
  });
  Morris.Donut({
    element: 'gender',
    data: data,
    formatter: function (y, data) { return '$' + y } 
  });
}

function vendor_graph () {
  var data = [];
  $("#vendor_table > tbody > tr").each(function() {
    data.push({
      vendor : $(this).children("td.vendor").text(), amount: (Number($(this).children("td.amount").text()))
    });
  });
  Morris.Bar({
    element: 'vendor',
    data: data,
    xkey: 'vendor',
    ykeys: ['amount'],
    labels: ['Vendor']
  });
}
