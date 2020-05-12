<?php
namespace Hoalqq\Commoncsv;

class Commoncsv
{
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

  public function __construct($filename, $header, $data)
  {
    $this->filename = $filename;
    $this->header   = $header;
    $this->data     = $data;
  }
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
    $data = $this->getData();
    $filename = $this->getFileName();
    $f = fopen($filename, 'w+');
    fwrite($f, $this->handleData());
    fclose($f);
  }
  /**
   * [downloadFile description]
   * @param  [string] $filename
   * @return null
   */
  public function downloadFile(){
    $filename = $this->getFileName();
    $this->createFile();
    $this->setheaderExport();
    readfile($filename);
    array_map('unlink', glob($filename));
    exit;
  }
  /**
   * [setheader description]
   * @param  [string] $filename
   * @param  string $encoding
   * @return null
   */
  public function setheaderExport(){
    $filename = $this->getFileName();
    $encoding = $this->getEncoding();
    $filename = $this->setFileNameForBrowser();
    header('Content-Encoding: '.$encoding);
    header('Content-Type: text/comma-separated-values');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
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
    $datas = $this->data;
    $encoding = $this->getEncoding();
    $fp = fopen('php://temp', 'w');
    $delimiter = $this->delimiter;
    $enclosure = $this->enclosure;
    $escape = $this->escape;
    $header = $this->getHeader();
    $newline = $this->newline;
    $enclose_numbers = $this->enclose_numbers;
    if (is_object($datas) and ! $datas instanceof \Iterator)
    {
      $datas = $this->to_array($datas);
    }
    if($this->getShowHeader()){
      array_unshift($datas, $header);// Prepend header
    }
    // escape, delimit and enclose function
    $escaper = function($items, $enclose_numbers) use($enclosure, $escape, $delimiter) {
      return  implode($delimiter, array_map(function($item) use($enclosure, $escape, $delimiter, $enclose_numbers) {
        if ( ! is_numeric($item) or $enclose_numbers)
        {
          $item = $enclosure.str_replace($enclosure, $escape.$enclosure, $item).$enclosure;
        }
        return $item;
      }, $items));
    };

    foreach($datas as $fields) {
      $output = $escaper($fields, $enclose_numbers).$newline;
      fwrite($fp, $output);
    }
    rewind($fp);
    // Convert CRLF
    $tmp = str_replace(PHP_EOL, "\r\n", stream_get_contents($fp));
    fclose($fp);
    // Convert row data from UTF-8 to Shift-JS
    return mb_convert_encoding($tmp, $encoding, 'UTF-8');
  }
  /**
   * [setFileNameForBrowser description]
   * @param [string] $filename
   * @param [string] $browes  $_SERVER['HTTP_USER_AGENT']
   */
  public function setFileNameForBrowser()
  {
    $filename = $this->getFileName();
    if(isset($_SERVER['HTTP_USER_AGENT'])){
      $browes = $_SERVER['HTTP_USER_AGENT'];
      if (strpos($browes, 'Firefox') !== false) {
        return rawurldecode($filename);
      }
    }
    return urlencode($filename);
  }
  /**
   * To array conversion
   *
   * Goes through the input and makes sure everything is either a scalar value or array
   *
   * @param   mixed  $data
   * @return  array
  */
  public function to_array($data = null)
  {
    if ($data === null)
    {
      $data = $this->_data;
    }
    $array = array();
    if (is_object($data) and ! $data instanceof \Iterator)
    {
      $data = get_object_vars($data);
    }
    if (empty($data))
    {
      return array();
    }
    foreach ($data as $key => $value)
    {
      if (is_object($value) or is_array($value))
      {
        $array[$key] = $this->to_array($value);
      }
      else
      {
        $array[$key] = $value;
      }
    }
    return $array;
  }

}