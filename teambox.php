<?php
/*$moduledir = dirname(__FILE__);
require($moduledir."/lib/container.class.php");


$teambox_new = new teambox();
*/


	$app_key = 'FxzSnAyxv41iwbMZqQdlxVifZ4lzOo8ifk5wWo23';
	$app_secret = 'GJvA1Yft7wMXsInqq4ROvPULc1lOLyEPQXaLCPfh';
	$token_url = 'https://teambox.com/oauth/token';
	$auth_url = 'https://teambox.com/oauth/authorize';
	$client_id = ;
	$returned_code = ;
	$auth_code = ;
	
	$apiurl   = '' ; 
	$apiurl   = $token_url . "?client_id=" .$client_id. "&client_secret=" .$app_secret. "&code=" .$returned_code. "&grant_type=" .$auth_code. "&redirect_uri=http%3A%2F%2Fthewebsiteguys.co.nz%2Fauth";
    $httphead = array('User-agent: WHMCSTeamboxModule (sysadmin@thewebsiteguys.co.nz)');
    
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiurl);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "andrewgood:Gianttcr11");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httphead);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$content  = curl_exec($ch);
	$response = curl_getinfo($ch);
	echo $response;
	
?>
