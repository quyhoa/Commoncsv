<?php

namespace Hoalqq\Commoncsv;

trait CollectionProviders
{

  /**
   * To array conversion
   *
   * Goes through the input and makes sure everything is either a scalar value or array
   *
   * @param   mixed  $data
   * @return  array
  */
  public static function to_array($data = null)
  {
    if ($data === null)
    {
      $data = $this->_data;
    }
    $array = array();
    if (is_object($data) && ! $data instanceof \Iterator)
    {
      $data = get_object_vars($data);
    }
    if (empty($data))
    {
      return array();
    }
    foreach ($data as $key => $value)
    {
      if (is_object($value) || is_array($value))
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