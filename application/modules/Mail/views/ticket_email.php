<!DOCTYPE html>
<html>
<head>
	<title>Email Template</title>
</head>
<body>
	<p style = "font-size: 10px;">Hi Technician, </p>

	<p style = "font-size: 10px;">The NOC Engineer just reported a fault at <b><?php echo $station; ?></b> and assigned you as the head technician to deal with the fault.</p>

	<p style = "font-size: 10px;">Use this ticket number to confirm that you are on site and that you are actually going to handle the fault</p>
	<br/>
	<a style = "" href="<?php echo base_url(); ?>FaultReporting/confirmationLInk/<?php echo $ticket; ?>"><h1 style = "text-align:center"><?php echo $ticket; ?></h1></a>

	<p>Regards,</p>
	<p>Fault Reporting Team</p>
</body>
</html>