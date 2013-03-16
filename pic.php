<?
function parseArgs($argv){

 array_shift($argv);

 $out = array();

 foreach ($argv as $arg){

     if (substr($arg,0,2) == '--'){

         $eqPos = strpos($arg,'=');

         if ($eqPos === false){

             $key = substr($arg,2);

             $out[$key] = isset($out[$key]) ? $out[$key] : true;

         } else {

             $key = substr($arg,2,$eqPos-2);

             $out[$key] = substr($arg,$eqPos+1);

         }

     } else if (substr($arg,0,1) == '-'){

         if (substr($arg,2,1) == '='){

             $key = substr($arg,1,1);

             $out[$key] = substr($arg,3);

         } else {

             $chars = str_split(substr($arg,1));

             foreach ($chars as $char){

                 $key = $char;

                 $out[$key] = isset($out[$key]) ? $out[$key] : true;

             }

         }

     } else {

         $out[] = $arg;

     }

 }

 return $out;

}
$POST = parseArgs($argv);

$PATH = realpath($POST['path']);


$name = 'pic.hta';
$savePath = pathinfo($_SERVER["PHP_SELF"],PATHINFO_DIRNAME);
$imgdir = $PATH;
$readdir = realpath($imgdir);

ob_start();
	if(!is_dir($readdir)) {echo file_get_contents($readdir);exit;}
	$dd = opendir($readdir);
	$i = 0;
while (false !== ($entry = readdir($dd))) {

	if(is_img($entry)){
		$i++;
		$img_arr[] = $readdir.'/'.$entry;
	}
	if($i>=20)
		break;
	
  // echo "<img src='$imgdir".'/'."$entry'></br>\r\n";
}
function is_img($filename){
	return in_array(getExt($filename),array('gif','jpg','jpeg','png')) ? 1 : 0 ;
}
function getExt($filename){
	return pathinfo($filename, PATHINFO_EXTENSION);
}
function array_to_json( $array ){

  if( !is_array( $array ) ){

      return false;

  }



  $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));

  if( $associative ){



      $construct = array();

      foreach( $array as $key => $value ){



          // We first copy each key/value pair into a staging array,

          // formatting each key and value properly as we go.



          // Format the key:

          if( is_numeric($key) ){

              $key = "key_$key";

          }

          $key = "'".addslashes($key)."'";



          // Format the value:

          if( is_array( $value )){

              $value = array_to_json( $value );

          } else if( !is_numeric( $value ) || is_string( $value ) ){

              $value = "'".addslashes($value)."'";

          }



          // Add to staging array:

          $construct[] = "$key: $value";

      }



      // Then we collapse the staging array into the JSON form:

      $result = "{ " . implode( ", ", $construct ) . " }";



  } else { // If the array is a vector (not associative):



      $construct = array();

      foreach( $array as $value ){



          // Format the value:

          if( is_array( $value )){

              $value = array_to_json( $value );

          } else if( !is_numeric( $value ) || is_string( $value ) ){

              $value = "'".addslashes($value)."'";

          }



          // Add to staging array:

          $construct[] = $value;

      }



      // Then we collapse the staging array into the JSON form:

      $result = "[ " . implode( ", ", $construct ) . " ]";

  }



  return $result;

}

closedir($dd);
$jsonImgArr = array_to_json($img_arr);
?>
<title>Show Me</title>
<div id="PIC">
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" charset="utf-8" ></script>
<script>
var img_arr = <?php echo $jsonImgArr;?>;
function GoGet(){
	$.each(img_arr,function(i,n){
		addPic(n);
	});
}
function addPic($PATH){

	// var $IMG = document.createElement("img");
	$IMG = "<img src='"+$PATH+"' /><br>";
	$("#PIC").append($IMG);
	// $("#PIC").append($IMG);
}
function zoomImg(o) {
    var zoom = parseInt(o.style.zoom, 10) || 100;
    zoom += event.wheelDelta / 5; //可适合修改
    if (zoom > 0)
    o.style.zoom = zoom + '%';
}
function resizeImg(o){
    var per = $(o).height() / $(o).width();
    $(o).width($(window).width() -100);
    $(o).height( $(o).width() * per );

}
$(document).ready(function(){
	GoGet();
    // $("img").on("mousewheel",function(){
    //     zoomImg(this);
    //     return false;
    // });
    $("img").on("load",function(){
        resizeImg(this);
        // return false;
    });
});
</script>

<?

$html = ob_get_clean();
$ppath = $savePath.'/'.$name;
if(file_exists($ppath))	
	unlink($ppath);

file_put_contents($ppath, $html);
?>