<?PHP

//$str1 = 'String with "double quote" within';
$arr1 = Array ("NAME" => "String with \"double quote\" within");
$arr2 = Array ("NAME" => "String with \"double quote\" within");

if ( $arr1["NAME"] != $arr2["NAME"] ) {
  echo "not equal\n";
  echo "arr1=".$arr1["NAME"]."\n";
  echo "arr2=".$arr2["NAME"]."\n";
} else {
  echo "EQUAL" ;
}

echo "\n";

?>
