<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>

<script src="<? echo base_url() ?>js/plugins/jquery-ui-1.10.3.custom/js/jquery-1.9.1.js"></script>
<script src="<? echo base_url() ?>js/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
<script src="<? echo base_url() ?>js/plugins/fullcalendar-1.6.0/fullcalendar/fullcalendar.js"></script>
<script src="<? echo base_url() ?>js/plugins/bootstrap/js/bootstrap.js"></script>
<script src="<? echo base_url() ?>js/plugins/bootstrap/js/bootbox.js"></script>
<script src="<? echo base_url() ?>js/plugins/bootstrap/js/bootstrap-notify.js"></script>

<? if(isset($ignore_base)): ?>
<link rel="stylesheet" media="screen" href="<? echo base_url() ?>css/stats.css" />
<? else: ?>
<link rel="stylesheet" media="screen" href="<? echo base_url() ?>css/web_base.css" />
<? endif; ?>
<link rel="stylesheet" media="screen" href="<? echo base_url() ?>js/plugins/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" media='screen' href="<? echo base_url() ?>js/plugins/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" media='all' href="<? echo base_url() ?>js/plugins/fullcalendar-1.6.0/fullcalendar/fullcalendar.css" />
<link rel="stylesheet" media='screen' href="<? echo base_url() ?>js/plugins/bootstrap/css/bootstrap-notify.css" />
<link rel="stylesheet" media='screen' href="<? echo base_url() ?>js/plugins/bootstrap/css/alert-bangtidy.css" />

<!-- sparkline -->
<script src="<? echo base_url() ?>js/plugins/sparkline.js"></script>
<!-- morris charts and graphs 
<script src="<? echo base_url() ?>js/plugins/morris/morris.min.js"></script>
<script src="<? echo base_url() ?>js/plugins/morris/raphael.js"></script>
<link rel="stylesheet" media='all'	  href="<? echo base_url() ?>js/plugins/morris/morris.css" />
-->

</head>