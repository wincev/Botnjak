<?php
//=======>>>>>>> Initial paramter configuration.

require('CallSolidar.php');
require "vendor/autoload.php";
include 'credentials.php';
use Abraham\TwitterOAuth\TwitterOAuth;
use Analog\Analog;


ini_set('memory_limit', '2048M');

$log_file = 'bot.log';
//Analog::handler (Analog\Handler\File::init ($log_file));

	

$input = json_decode(file_get_contents("php://input")); //file_get_contents("php://input");// 
$idServer = $input->id;
$checkId = $input->check;
$type = $input->type;
$checkIdClient = substr(sha1($id.$checkKey),5,17);
$refHeight = bcdiv(microtime(),600000,0);
echo("check");
if ($checkIdClient != $idServer) {
	goto EndOfFile;
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_secret);

function connectDB() {	
	mysql_connect($host, $dbUser, $dbPassword);
	mysql_select_db($database);
}

function getNewBalance($balance, $oldTime, $newTime) {
	$dTime = $newTime - $oldTime;
	while ($dTime >= 100000){ 
		$balance = $balance * 0.69249573468584;
		$dTime = $dTime - 100000;
	}
	while ($dTime >= 20000){ 
		$balance = $balance * 0.92914484250231;
		$dTime = $dTime - 20000;
	}
	while ($dTime >= 5000){ 
		$balance = $balance * 0.98179508840687;
		$dTime = $dTime - 5000;
	}
	while ($dTime >= 1000){ 
		$balance = $balance * 0.99633221082889;
		$dTime = $dTime - 1000;
	}
	while ($dTime >= 200){ 
		$balance = $balance * 0.99926536357709;
		$dTime = $dTime - 200;
	}
	while ($dTime >= 50){ 
		$balance = $balance * 0.99981629027658;
		$dTime = $dTime - 50;
	}
	while ($dTime >= 10){ 
		$balance = $balance * 0.99996325535508;
		$dTime = $dTime - 10;
	}
	while ($dTime >= 2){ 
		$balance = $balance * 0.999992650963;
		$dTime = $dTime - 2;
	}
	while ($dTime >= 1){ 
		$balance = $balance * 0.99999632547475;
		$dTime = $dTime - 1;
	}
	
	return $balance;
}

$value = array(
	'id' => 930385067691700228// $idServer
	);

function getTweet(array $value) {
	global $connection;
	return $connection->get('statuses/show', $value);
}

function getDm(array $value) {
	global $connection;
	return $connection->get('direct_messages/show', $value);
}






if ($type == "DM") {
	$resultFromId = getDm($value);
} else {
	$resultFromId = getTweet($value);
}

if (!empty($id) && $type == "DM") {
	$req = $resultFromId->text;
	$sender = $resultFromId->sender->screen_name;
	$senderId = $resultFromId->sender->id_str;
	$recipient = $resultFromId->recipient->screen_name;
} elseif (!empty($id)) {
	$req = $resultFromId->text;
	$sender = $resultFromId->user->screen_name;
	$senderId = $resultFromId->user->id_str;
	$messageId = $resultFromId->id_str;
}
$answer = "Hello, i am your Solidar Tipbot, you can use the \"balance\", \"send\" or \"withdraw\" commands to interact
.";
connectDB();
$checkDouble = mysql_query("SELECT sender FROM transaction WHERE messageID = '$idServer'");
$rowDouble = mysql_fetch_row($checkdouble);

if (!empty($rowDouble)) {
	goto EndOfFile;
}

//======>>>>> Sinister request.
require('twitterWithdraw.php');
require('twitterBalance.php');
require('twitterSend.php');
require('twitterTip.php');
//=======>>>>>> Sending tweets section
$arrDm = array(
	'user_id' => $sender,
	'text' => $answer
	);

$arrTweet = array(
	'status' => $answer,
	'in_reply_to_status_id' => $messageId
	);
		
	
$status = $connection->post('direct_messages/new', $arrDm);



$status = $connection->post('statuses/update', $arrTweet);

EndOfFile:
?>
