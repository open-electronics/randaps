<?php

	require_once "common/db_class.php";

	$filename = "RandA-PhotoSharing_Data".date("d-m-Y", time())."_".date("H-i-s", time()).".xls"; 
	
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: inline; filename=".$filename); 

	echo "<table border='1'>";
	echo "<tr>";
	echo "<td><b>DateTime</b></td>";
	echo "<td><b>File</b></td>";
	echo "<td><b>Social</b></td>";
	echo "<td><b>eMail</b></td>";
	echo "</tr>";
	
	$db = new DataB();
	$db->OpenDb();
	
	$sql = "SELECT File, DateTime, Social, eMail FROM photos ORDER BY DateTime DESC";
	$result = $db->QueryDb($sql);
	while($row = $db->FetchArray($result)) {
		echo "<tr>";
		echo "<td>".$row['DateTime']."</td>";
		echo "<td>".$row['File']."</td>";
		echo "<td>".$row['Social']."</td>";
		echo "<td>".$row['eMail']."</td>";
		echo "</tr>";
	}
	
	$db->CloseDb();
	
	echo "</table>";
	
?>