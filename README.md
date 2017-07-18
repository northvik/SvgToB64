# SVG to B64

This script will compress and convert in base 64 all the svg images who are in 
the select folder, it's going to use the file ```template.scss.txt``` to create sass class
in the ```_image.scss``` file. You can edit the file ```template.scss.txt``` to be of your liking but 
be sure to use this 2 variable in it ```@@_IMAGE_NAME_@@``` who use the name of your svg file, and 
```@@_IMAGE_DATA_@@``` who will be replace by the base 64 string.

```@@_IMAGE_NAME_@@``` who use the name of your svg file.
```@@_IMAGE_DATA_@@``` who will be replace by the base 64 string.

You can use the converter like this:

```bash
php convert.php
```
This will use the images in the toConvert Folder.

or you can use it like this:
```bash
php convert.php ../Path/to/specific/folder
```
The path can be relative or absolute

I hope this script will help you.