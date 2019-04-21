<html>
<title>
Mangaidea
</title>
<?php
/*$GLOBALS['numbers']=0;
$html = "dddasdfdddasdffff";
$needle = "asdf";
$lastPos = 0;
$positions = array();

while (($lastPos = strpos($html, $needle, $lastPos))!== false) {
    $positions[] = $lastPos;
    $lastPos = $lastPos + strlen($needle);
}

// Displays 3 and 10
foreach ($positions as $value) {
    echo $value ."<br/>";
}*/
//error handler function
function customError($errno, $errstr) {
  echo "OOPs, sepertinya terdapat error.";
}
//set error handler
set_error_handler("customError");
function koneksiDB($isi){
	$servername = "localhost";$username = "root";
	$password = "";$dbname = "test";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$isi =  $conn->query($isi);
	$conn->close();
	return $isi;
}
$user = "ricky";
$sql = "SELECT no_img FROM test.manga_img;";
$GLOBALS['number'] = koneksiDB($sql)->num_rows;
$GLOBALS['lis_img'] = "";
$sql = "SELECT no FROM test.manga_user where user ='".$user."' ;";
$GLOBALS['numbers'] = koneksiDB($sql)->num_rows;
if(isset($_POST["url"])){
	isset($_POST['url']);
	$GLOBALS['NewManga'] = "";
	function carijpg($isi,$i,$user,$file){
		$jpg = stripos($isi,$file,$i);
		$quo2 = substr($isi,$i,$jpg-$i);
		$quo = strripos($quo2,'"',0);
		$qwo = strripos($quo2, '&quot;',0);
		$quo2 = stripos($isi,'"',$jpg);
		$qwo2 = stripos($isi,'&quot;',$jpg);
		if($qwo2<$quo2){
			if($qwo2>0){
				$quo2=$qwo2;
			}
		}
		if($quo*$jpg>0){
			$jpg = substr($isi,$i,$quo2-$i);
			if($qwo>$quo){
				$jpg = substr($jpg,$qwo+6);
				if(substr($jpg,0,23)!="//m.mangahere.cc/media/"){
					$GLOBALS['number']++;
					$GLOBALS['numbers']++;
					$GLOBALS['lis_img']=$GLOBALS['lis_img']."('".$GLOBALS['numbers']."','".$GLOBALS['number']."','".$user."'),";
					$GLOBALS['NewManga']=$GLOBALS['NewManga']."('".$GLOBALS['number']."','".$jpg."'),";
				}
			}else{
				$jpg = substr($jpg,$quo+1);
				if(substr($jpg,0,23)!="//m.mangahere.cc/media/"){
					$GLOBALS['number']++;
					$GLOBALS['numbers']++;
					$GLOBALS['lis_img']=$GLOBALS['lis_img']."('".$GLOBALS['numbers']."','".$GLOBALS['number']."','".$user."'),";
					$GLOBALS['NewManga']=$GLOBALS['NewManga']."('".$GLOBALS['number']."','".$jpg."'),";
				}
			}
		}
		$jpg =stripos($isi,$file,$i);
		if(strlen($jpg)>0){
			carijpg($isi,$jpg+1,$user,$file);
		}
	}
	$url = $_POST['url'];
	$hed = array(
		'http' => array(
			'method' => "GET",
			'header' => "Accept-Language: en-US,en;q=0.9\r\n" .
						"Cache-Control: max-age=0\r\n".
						"Cookie: foo=bar\r\n" .
						"Upgrade-Insecure-Requests: 1" .
						"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36\r\n" .
						"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8\r\n".
						
						"Connection: close"
						)
	);
	$konteks = stream_context_create($hed);
	$loe = @file_get_contents(str_replace(" ","%20",$url), false, $konteks);
	carijpg($loe,0,$user,".jpg");
	carijpg($loe,0,$user,".jpeg");
	carijpg($loe,0,$user,".png");
	if($loe){
		$sql = "INSERT INTO `manga_user`(`no`,`no_img`,`user`) VALUES ".substr($GLOBALS['lis_img'],0,-1).";";
		echo ($sql);
		$sql =  "INSERT INTO `manga_img`(`no_img`, `img`) VALUES ".substr($GLOBALS['NewManga'],0,-1).";";
		echo ($sql);
	}
}
?>
<link href='./mangaidea/bootstrap.min.css' rel='stylesheet'>
<style>
body{
	background-color: #ffffff;
	
}
#columns {
	width: 100%;
	max-width: 90%;
	margin: 50px auto;
	display: grid;
	grid-template-columns: auto auto auto auto;
}

div#columns figure {
	max-width : 100%;
	background: #fefefe;
	border: 2px solid #fcfcfc;
	margin: 0 2px 15px;
}

div#columns figure img {
	height: auto;
	max-width : 90%;
	border-bottom: 1px solid #ccc;
	padding-bottom: 15px;
	margin-bottom: 5px;
}
</style>
<form action='' method='post' align='center'>
<input name="url" size=50%></name>
<button type='submit' class='btn btn-primary'>Submit</button>
</form>
<div id="columns" class="board">
</div>
<script>
function inBoard(isi){
	isi = "<figure><img src='"+isi+"'></figure>";
	document.getElementsByClassName("board")[0].innerHTML=document.getElementsByClassName("board")[0].innerHTML+isi;
}
</script>
<script>
<?php
	$GLOBALS['listManga'] = array();
	function randomlink($i,$o,$e){
		if($i<1){
			return 0;
		}
		$roun = rand($o,$e);
		array_push($GLOBALS['listManga'],$roun);
		randomlink($i/2,$o,$roun-1);
		randomlink($i/2,$roun,$e);
	}
	$sql = "SELECT no FROM test.manga_user where user ='".$user."';";
	$sql = koneksiDB($sql)->num_rows;
	randomlink(25,1,$sql);
	shuffle($GLOBALS['listManga']);
	$sql = join("','",$GLOBALS['listManga']);
	$sql = "SELECT img FROM test.manga_img a LEFT JOIN test.manga_user b on a.no_img = b.no_img where b.user = '".$user."'and no IN ('".$sql."');";
	$result = koneksiDB($sql);
	if ($result === FALSE) {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}else{
		if($result->num_rows > 0) {
			$i = 0;
			while($row = $result->fetch_assoc()) {
				if($i==20){
					break;
				}
				echo "inBoard('".($row['img'])."');";
				$i++;
			}
		}
	}
?>
</script>
</html>