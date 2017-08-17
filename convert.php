<?php
require_once('SvgSanitizer.php');

function compress($svg)
{
  $svg = preg_replace('/<!--.*-->/', '', $svg);
  $svg = preg_replace('/<g>[\n\r\s]*<\/g>/', '', $svg);
  $svg = preg_replace('/\n/', ' ', $svg);
  $svg = preg_replace('/\t/', ' ', $svg);
  $svg = preg_replace('/\s\s+/', ' ', $svg);
  $svg = str_replace('> <', '><', $svg);
  $svg = str_replace(';"', '"', $svg);

  return $svg;
}

function colorize($string){
  return "\033[1;35m". $string . "\033[0m";
}
function error($string){
  return "\033[1;31m". $string . "\033[0m\n";
}

echo "
\033[1;35m################################################################################

                        WELCOME TO THE SVG CONVERTER\033[0m

      This script will compress and convert in base 64 all the svg images who are in 
      the select folder, it's going to use the file template.scss.txt to create sass class
      in the _image.scss file. You can edit the file template.scss.txt to be of your liking but 
      be sure to use this 2 variable in it @@_IMAGE_NAME_@@ who use the name of your file, and 
      @@_IMAGE_DATA_@@ who will be replace by the base 64 string.
      
      \e[1;35m@@_IMAGE_NAME_@@\e[0m who use the name of your file.
      \e[1;35m@@_IMAGE_DATA_@@\e[0m who will be replace by the base 64 string.

################################################################################\033[0m\n\n
";
//$dir =  "../icons/";
$dir =  "./toConvert";


if (isset($argv[1])){
  $dir =  $argv[1];
}else{
  echo "You didn't specify a path the script is going to use the svg images in ".$dir."\n
You can specify the path like this:\n".colorize("   php convert.php ../../Downloads/")."
\nDo you want to continue [Y/n] ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  $reply=trim($line);
  if(!empty($reply) && $reply!='y' && $reply!='Y'){
    echo error("ABORDING!\n");
    exit;
  }
  fclose($handle);
}
echo "Folder used: ".colorize($dir)."\n";
// only min-width of cells is set
$mask = "|%-30.30s |%-10.10s  |%-10.10s |%-10.10s \n";
printf($mask, 'File name ', 'size before', 'size after', '% gain');

// Create a new sanitizer instance
$sanitizer = new SvgSanitizer();

$template = file_get_contents("template.scss.txt");

$arrayOfFiles = scandir($dir);

$htmlInner = "";
$return = "";
foreach ($arrayOfFiles as $filePath) {
  $fileInfo = pathinfo($dir."/" . $filePath);
  if (isset($fileInfo["extension"]) && strtolower($fileInfo["extension"]) == "svg") {
    $sizeBefore = filesize($dir."/" . $filePath);
    $sanitizer->load($dir."/" . $filePath );

    // sanitize!
    $sanitizer->sanitize();
    file_put_contents("./convert/" . $filePath, compress($sanitizer->saveSVG()));
    // Print out sanitized SVG
    $sizeafter = filesize("./convert/" . $filePath);

    $pourcent = (($sizeBefore - $sizeafter) / $sizeBefore) * 100;
    printf($mask, $filePath, $sizeBefore, $sizeafter, round($pourcent)." %");


    $data = "data:image/svg+xml;base64,".base64_encode(file_get_contents("./convert/" . $filePath));


    $fileName  = str_replace(" ", "_",$fileInfo["filename"]);

    $templateHidrate = $template;
    $templateHidrate = str_replace("@@_IMAGE_NAME_@@", $fileName, $templateHidrate);
    $templateHidrate = str_replace("@@_IMAGE_DATA_@@", $data, $templateHidrate);
    $return .= $templateHidrate."\n\n";
    unlink("./convert/" . $filePath);

    $htmlInner .= "<div class='block'>
        <span>".$fileName."</span>
        <div class='".$fileName."'>&nbsp;</div>
        </div>";
  }
}
file_put_contents("sass/_images.scss", $return);
exec("compass compile --sass-dir=sass --css-dir=css");

//file_put_contents("../_images.scss", $return);


// create index.html
$html = "<!DOCTYPE html>
<html lang='en'>
<head>
<style>".file_get_contents("css/style.css")."</style>
<body>
<div class='main'>";

$html .= $htmlInner;


$html.= "</div></body></html>";

file_put_contents("index.html", $html);
//file_put_contents("../index.html", $html);


echo colorize("Finished\n");
exit;