<?php
/**
 * Created by PhpStorm.
 * User: camille
 * Date: 07/07/17
 * Time: 11:53
 */

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