<?php
namespace Hoalqq\Commoncsv;

class ImportCsv
{
  protected $utf8 = 'UTF-8';

  public function __construct($csvfile, $filePath){
    if($this->checkExitFile($csvfile, $filePath)){
      $this->encordingConverter($csvfile, $filePath);
      $fp = fopen($filePath . $csvfile, 'r');
      do {
        $row_data = fgetcsv($fp);
        if ($row_data) {
          $array[] = $row_data;
        }
      } while ($row_data);
    }
    return "File not exits!!";
  }
  /**
   * [checkExitFile description]
   * @param  [string] $file [description]
   * @return [boolean] true is file exits, false is file not extis
   */
  public function checkExitFile($file, $filePath = null){
    if(empty($filePath)){
      $pathFileName = $file;
    }else{
      $pathFileName = $filePath.$file;
    }
    $file_headers = get_headers($pathFileName);
    return ($file_headers[0] == 'HTTP/1.1 200 OK' || file_exists($pathFileName));
  }
  /**
   * Convert character code of csv file to utf8
   *
   * @param $file
   * @param string $filePath
   */
  private function encordingConverter($file, $filePath) {
    $characodes = ["SJIS-win", $this->utf8];
    $characode = mb_detect_encoding($file, $characodes);
    $filedata = file_get_contents($filePath .$file);
    $filedata = mb_convert_encoding($filedata, $this->utf8, $characode);
    $fp = fopen($filePath . $file, 'w');
    fwrite($fp, $filedata);
    fclose($fp);
  }
}