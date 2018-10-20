<?php
/*
function save_image($inPath, $outPath) { //Download images from remote server
	$ch = curl_init($inPath);
	$fh = fopen($outPath, "w");
	curl_setopt($ch, CURLOPT_FILE, $fh);
	curl_exec($ch);
	curl_close($ch);
}
*/

    //Alternative Image Saving Using cURL seeing as allow_url_fopen is disabled - bummer
    function save_image($img,$fullpath){
        $ch = curl_init ($img);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
		/*
        if(file_exists($fullpath)){
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $rawdata);
        fclose($fp);
		*/
		return $rawdata;
    }

/*
function http_get_file($url, $outPath) {
   $url_stuff = parse_url($url);
   $port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;

   $fp = fsockopen($url_stuff['host'], $port);

   $query = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
   $query .= 'Host: ' . $url_stuff['host'];
   $query .= "\n\n";

   fwrite($fp, $query);

   while ($line = fread($fp, 1024)) {
       $buffer .= $line;
   }

	return $buffer;
//   preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
//   return substr($buffer, - $parts[1]);
}
*/
?>