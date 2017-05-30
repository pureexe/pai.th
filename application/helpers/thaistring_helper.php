<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('random_thai_string'))
{
  /**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  alpha, alnum, alcar,carnum, numeric, nozero,
	 * @param	int	number of characters
	 * @return	string
	 */
  function random_thai_string($type = 'alnum',$len = 8)
  {
    switch ($type)
    {
      case 'alpha':
        $pool = 'กขฃคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮ';
        break;
      case 'alnum':
        $pool = '๐๑๒๓๔๕๖๗๘๙กขฃคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮ';
        break;
      case 'car':
        $pool = 'กขคฆงจฉชฌญฎฐฒณดตถทธนบผพภมยรลวศษสฬอฮ';
        break;
      case 'carnum':
        $pool = '๐๑๒๓๔๕๖๗๘๙กขคฆงจฉชฌญฎฐฒณดตถทธนบผพภมยรลวศษสฬอฮ';
        break;
      case 'numeric':
        $pool = '๐๑๒๓๔๕๖๗๘๙';
        break;
      case 'nozero':
        $pool = '๑๒๓๔๕๖๗๘๙';
        break;
    }
    $output = "";
    for($i=0;$i<$len;$i++){
       $output .= mb_substr($pool,rand(0, mb_strlen($pool) - 1),1);
    }
    return $output;
  }
}
