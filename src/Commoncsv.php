<?php
namespace Hoalqq\Commoncsv;

class Commoncsv
{
  use CollectionProviders;
  protected $filename;
  protected $header;
  protected $data;
  protected $show_header = true;
  protected $encoding = 'SJIS';
  protected $delimiter = ",";
  protected $enclosure = "\"";
  protected $escape = "\\";
  protected $enclose_numbers = true;
  protected $newline = "\n";
  protected $utf8 = 'UTF-8';

  public function setFileName($filename){
    $this->filename = $filename;
    return $this;
  }
  public function getFileName(){
    return $this->filename;
  }
  public function getHeader(){
    return $this->header;
  }
  public function setHeader($header){
    $this->header = $header;
    return $this;
  }
  public function setData($data){
    $this->data = $data;
    return $this;
  }
  public function getData(){
    return $this->data;
  }
  public function setShowHeader($flag){
    $this->show_header = $flag;
    return $this;
  }
  public function getShowHeader(){
    return $this->show_header;
  }
  public function setEncoding($encoding){
    $this->encoding = $encoding;
    return $this;
  }
  public function getEncoding(){
    return $this->encoding;
  }
  public function setDelimiter($delimiter){
    $this->delimiter = $delimiter;
    return $this;
  }
  public function getDelimiter(){
    return $this->delimiter;
  }
  public function setEnclosure($enclosure){
    $this->enclosure = $enclosure;
    return $this;
  }
  public function getEnclosure(){
    return $this->enclosure;
  }

  public function createFile(){
    $f = fopen($this->filename, 'w+');
    fwrite($f, $this->handleData());
    fclose($f);
  }
  /**
   * [downloadFile description]
   * @param  [string] $filename
   * @return null
   */
  public function downloadFile(){
    $this->createFile();
    $this->setheaderExport();
    readfile($this->filename);
    array_map('unlink', glob($this->filename));
    exit;
  }
  /**
   * [setheader description]
   * @param  [string] $filename
   * @param  string $encoding
   * @return null
   */
  public function setheaderExport(){
    header('Content-Encoding: '.$this->encoding);
    header('Content-Type: text/comma-separated-values');
    header('Content-Disposition: attachment; filename="'.$this->setFileNameForBrowser().'"');
    header('Cache-Control: max-age=0');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
  }
  /**
   * handleData
   * @param  [array] $datas   
   * @param  string $encoding
   * @return string          
   */
  public function handleData(){
    $fp = fopen('php://temp', 'w');
    $delimiters = $this->delimiter;
    $enclosures = $this->enclosure;
    $escapes = $this->escape;
    $enclose_number = $this->enclose_numbers;
    if (is_object($this->data) && ! $this->data instanceof \Iterator)
    {
      $this->data = CollectionProviders::to_array($this->data);
    }
    if($this->getShowHeader()){
      array_unshift($this->data, $this->header);// Prepend header
    }
    // escape, delimit and enclose function
    $escaper = function($items, $enclose_number) use($enclosures, $escapes, $delimiters) {
      return  implode($delimiters, array_map(function($item) use($enclosures, $escapes, $enclose_number) {
        if ( ! is_numeric($item) || $enclose_number)
        {
          $item = $enclosures.str_replace($enclosures, $escapes.$enclosures, $item).$enclosures;
        }
        return $item;
      }, $items));
    };

    foreach($this->data as $fields) {
      $output = $escaper($fields, $enclose_number).$this->newline;
      fwrite($fp, $output);
    }
    rewind($fp);
    // Convert CRLF
    $tmp = str_replace(PHP_EOL, "\r\n", stream_get_contents($fp));
    fclose($fp);
    // Convert row data from UTF-8 to Shift-JS
    return mb_convert_encoding($tmp, $this->encoding, $this->utf8);
  }
  /**
   * [setFileNameForBrowser description]
   * @param [string] $filename
   * @param [string] $browes  $_SERVER['HTTP_USER_AGENT']
   */
  public function setFileNameForBrowser()
  {
    if(isset($_SERVER['HTTP_USER_AGENT'])){
      $browes = $_SERVER['HTTP_USER_AGENT'];
      if (strpos($browes, 'Firefox') !== false) {
        return rawurldecode($this->filename);
      }
    }
    return urlencode($this->filename);
  }
}