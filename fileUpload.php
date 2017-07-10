<?php
require_once('lib/SvgSanitizer.php');
require_once('src/convert.php');

if (!empty($_FILES)) {
  $return = array();
  $template = $_POST["config"];
  $sanitizer = new SvgSanitizer();

  for($i=0; $i< count($_FILES['photo']['name']); $i++){
    $name = $_FILES['photo']['name'][$i];
    $type =  $_FILES['photo']['type'][$i];
    if ($type != "image/svg+xml"){
      continue;
    }
    $path = "/tmp/toConvert/".md5(uniqid()).".svg";
    $newPath = "/tmp/convert/".md5(uniqid()).".svg";
    $sanitizer->load($path);

    // sanitize!
    $sanitizer->sanitize();
    file_put_contents($newPath, compress($sanitizer->saveSVG()));

    $image = base64_encode(file_get_contents($newPath));
    if($image == false) continue;
    $data = "data:image/svg+xml;base64,".$image;


    $fileName  = str_replace(" ", "_",$name);
    $fileName  = str_replace(".svg", "",$fileName);
    $fileName  = str_replace(".SVG", "",$fileName);

    $templateHydrate = $template;
    $templateHydrate = str_replace("@@_IMAGE_NAME_@@", $fileName, $templateHydrate);
    $templateHydrate = str_replace("@@_IMAGE_DATA_@@", $data, $templateHydrate);
    unlink($newPath);
    unlink($path);
    $return[] = array(
      "name"        => $name,
      "sass"        => utf8_encode($templateHydrate)
    );
  }
  $json = json_encode($return);
  if ($json != false){
    header('Content-type:application/json;charset=utf-8');
    echo $json;
  }else{
    echo json_last_error();
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        echo ' - No errors';
        break;
      case JSON_ERROR_DEPTH:
        echo ' - Maximum stack depth exceeded';
        break;
      case JSON_ERROR_STATE_MISMATCH:
        echo ' - Underflow or the modes mismatch';
        break;
      case JSON_ERROR_CTRL_CHAR:
        echo ' - Unexpected control character found';
        break;
      case JSON_ERROR_SYNTAX:
        echo ' - Syntax error, malformed JSON';
        break;
      case JSON_ERROR_UTF8:
        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
      default:
        echo ' - Unknown error';
        break;
    }
  }
}
