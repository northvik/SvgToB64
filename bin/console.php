<?php
require_once('../lib/SvgSanitizer.php');
require_once('../src/convert.php');


echo "
\033[1;35m################################################################################

                        WELCOME TO THE SVG CONVERTER\033[0m

      This script will compress and convert in base 64 all the svg images who are in 
      the toConvert folder, it's going to use the file template.scss.txt to create sass class
      in the _image.scss file. You can edit the file template.scss.txt to be of your liking but 
      be sure to use this 2 variable in it @@_IMAGE_NAME_@@ who use the name of your file, and 
      @@_IMAGE_DATA_@@ who will be replace by the base 64 string.
      
      \e[1;35m@@_IMAGE_NAME_@@\e[0m who use the name of your file.
      \e[1;35m@@_IMAGE_DATA_@@\e[0m who will be replace by the base 64 string.

################################################################################\033[0m\n\n
";

// only min-width of cells is set
$mask = "|%-15.15s |%-10.10s  |%-10.10s |%-10.10s \n";
printf($mask, 'File name ', 'size before', 'size after', '% gain');

// Create a new sanitizer instance
$sanitizer = new SvgSanitizer();

$template = file_get_contents("template.scss.txt");

$arrayOfFiles = scandir("./toConvert");

$return = "";
foreach ($arrayOfFiles as $filePath) {
  $fileInfo = pathinfo("./toConvert/" . $filePath);
  if (strtolower($fileInfo["extension"]) == "svg") {
    $sizeBefore = filesize("./toConvert/" . $filePath);
    $sanitizer->load("./toConvert/" . $filePath );

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
  }
}
file_put_contents("_images.scss", $return);