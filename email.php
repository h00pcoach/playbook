<?php
$subject = "Error in a play from basketballplaybook.org";

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo "<serverResponse>\n";
	if (mail('bastasys.info@gmail.com',$subject,"<p><a href='".$_GET['purl']."'>".$_GET['purl']."</a></p>")) {
		echo "\t<status>1</status>\n";
	} else {
		echo "\t<status>0</status>\n";
	}
	
	echo "</serverResponse>";
	/*$mail = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)
	$mail->IsMail(); // telling the class to use native PHP mail()
	$mail->SetFrom($customerEmail, '');
	$mail->AddAddress($receiverEmail, '');
	$mail->Subject = $subject;
	$mail->MsgHTML($customerMessage . "<p><a href='$playURL'>$playURL</a></p>");*/



?>