<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
	// require_once('../mydb.php');
	session_start();
if(isset($_REQUEST['id'])){
	//$pdf=json_decode($_POST['pdf']);
	require_once("dompdf_config.inc.php");

	$hostname = "localhost";

	// LIVE
	// $username = "hoopcoac_mainus";
	$dbname = "hoopcoac_basketball";
	// $dbname = "bas1330408070797";

	// $password = "@hoopcoach";

	// LOCAL
	$username = "bjtactor";
	$password = "rump";

	$conn = new PDO( $hostname, $username, $password );
	$conn->exec("set names utf8");

	// We check wether the user is accessing the demo locally
	$local = array("::1", "127.0.0.1");
	$is_local = in_array($_SERVER['REMOTE_ADDR'], $local);

	//if ( isset( $_POST["html"] ) && $is_local ) {
	$html='<!doctype html>
	<html>
	<head>
	<meta charset="UTF-8">
	<title>Report</title>
	<style>
	.header,.footer{
		position:fixed;
		top:0px;
		width:100%;
		text-align:center
	}
	.footer{
		bottom:0px
	}
	table tr:nth-child(2n+2){
		page-break-after: always;
	}
	table tr:nth-child(2n+1) td{
		padding-top:100px
	}
	</style>
	</head>

	<body style="margin:auto; text-align:center">
	<div class="header">
		<img src="../Model/hoopcoach120.png" height="88" />
	</div>';


	//$html='';
	$sql = "SELECT * FROM playdata WHERE id = :id";

	$st = $conn->prepare( $sql );
	$st->bindValue( ":id", $_REQUEST['id'], PDO::PARAM_INT );
	$st->execute();
	$res = $st->fetch();
	$conn = null;

	$i=1;
	// $query=mysql_query("SELECT * FROM playdata WHERE id='".$_REQUEST['id']."'");
	// $res=mysql_fetch_assoc($query);
	$nm=explode('`',$res['movements']);
	$file="../users/".$res['userid']."/".$res['file'];
	while(file_exists($file.'_'.$i.'.jpeg')){
		if($i%4==1) $html.='<table width="100%"cellspacing="0" cellpadding="0"><tr><td colspan="2" height="50">&nbsp;</td></tr><tr>';
		$html.='<td align="center" width="50%" height="300">';//<div style="float: left; margin-right: 10px; text-align: center;">
		if(array_key_exists($i,$nm))
		$html.='<h2 style="">'.$nm[$i].'</h2>';
		else

			$html.='<h2 style="">Movement '.$i.'</h2>';

		$sz=getimagesize($file.'_'.$i.'.jpeg');
		if($sz[0]>$sz[1])
			$html.='<img width="300" src="'.$file.'_'.$i.'.jpeg'.'" /></td>';
		else
			$html.='<img height="300" src="'.$file.'_'.$i.'.jpeg'.'" /></td>';
		if($i%2==0) $html.='</tr><tr>';
		if($i%4==0) $html.='</tr></table><div style="page-break-after: always;"></div>';
		$i++;
	}

	/*foreach ($pdf as $k => $v) {

	/*$html.='<div>
		<h1>'.$k.'</h1>
		<img width="70%" src="'.$v.'" />
		</div>';
	}*/
	//$html.=$_POST['pdf'];
	//echo $html;
	$html.='</body>
	</html>
	';
	echo $html;
	exit(0);
	  if ( get_magic_quotes_gpc() )
		$html = stripslashes($html);

	  $dompdf = new DOMPDF(); $dompdf->load_html($html);
	  /////////$dompdf->set_paper($_POST["paper"], $_POST["orientation"]);
	  $dompdf->render();
	 /* $pdf = $dompdf->output();
        //header and footer
        $obj = $pdf->open_object();

        //header text
        $font = Font_Metrics::get_font("Helvetica");
        $fontsize = 10;
        $fontcolor = array(0.4,0.4,0.4);
        $pdf->page_text(60, $pdf->get_height()-40, "Provided by HoopCoach, All Rights Reserved", $font, $fontsize, $fontcolor);
        $pdf->page_text($pdf->get_width()-100, $pdf->get_height()-40, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $fontsize, $fontcolor);

        //header image
        $image = "../Model/Img/hoopcoach_logo.png";
        $pdf->image($image, "png", 10, 10, 80, 53);

        $pdf->close_object();
        $pdf->add_object($obj, "all");*/

	////////$pdf = $dompdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.

////////file_put_contents("saved_pdf.pdf", $pdf);
	  $dompdf->stream($res['name'].".pdf", array("Attachment" => false));

	  exit(0);
}
?>
