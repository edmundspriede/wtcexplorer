<?php

  //=====================================================================================
  // ERRORS
  //=====================================================================================

  error_reporting(E_ALL);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);

  date_default_timezone_set('Europe/Riga');

  //=====================================================================================
  // SESSION
  //=====================================================================================

  function ldSessionStarted() {
    if (function_exists('session_status')) return !(session_status() == PHP_SESSION_NONE);
    return !(session_id() == '');
  }

  if (!ldSessionStarted()) session_start();

  if (!isset($SESSION_KEY)) {
    $SESSION_KEY = md5($_SERVER['SCRIPT_FILENAME']); //Set key to be able to save params into session vars for reuse with out resubmission
  }

  //=====================================================================================
  // DEBUG
  //=====================================================================================

  function debug($a, $die = true) {
    echo '<code>';
    echo str_replace(array(" ", "\n"), array("&nbsp;", "<br>"), htmlentities(print_r($a, true)));
    echo '</code><br>';
    if ($die) die;
  }

  //=====================================================================================
  // RELOAD
  //=====================================================================================

  $RELOAD_HEADER =
    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n".
    '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n".
    '<meta http-equiv="X-UA-Compatible" content="IE=8" >'."\n".
    '<html><head><title>Gaidiet...</title>'."\n".
    '<link rel="stylesheet" type="text/css" href="css/main.css">'."\n".
    '</head>'."\n".
    '<body>'."\n";

  $RELOAD_FOOTER =
    '</body>'."\n".
    '</html>'."\n";

  $RELOAD =
    $RELOAD_HEADER.
    '<div>%TXT%</div>'."\n".
    '<script type="text/javascript">window.location="%URL%";</script>'."\n".
    $RELOAD_FOOTER;

  $RELOAD_MSG =
    $RELOAD_HEADER.
    '<div>%TXT%</div>'.
    '<script type="text/javascript">alert(\'%MSG%\');window.location="%URL%";</script>'."\n".
    $RELOAD_FOOTER;

  $GOBACK =
    $RELOAD_HEADER.
    '<div>Gaidiet, notiek savienojums ar serveri...</div>'."\n".
    '<script type="text/javascript">alert(\'%TXT%\');history.back();</script>'."\n".
    $RELOAD_FOOTER;

  //=====================================================================================
  // GLOBALS
  //=====================================================================================

  define("CURR", '€');

  $locale_info = localeconv();
  define("DECIMAL", $locale_info['decimal_point']);
  unset($locale_info);

  //=====================================================================================
  // NEW LINE
  //=====================================================================================

  define("NL", '<br>'."\n");

  //=====================================================================================
  // DAZADI SIKUMI
  //=====================================================================================

  function MinMax($n, $n_min, $n_max) {
    if ($n < $n_min) $n = $n_min;
    if ($n > $n_max) $n = $n_max;
    return $n;
  }

  //=====================================================================================
  // PARAM
  //=====================================================================================

  function ldTrim($str, $trim_enter = false, $trim_tab = false, $max_len = 0) {
    if ($str == '') return '';
    //Remove invalid UTF8 chars
    $rez = iconv("UTF-8", "UTF-8//IGNORE", strval($str));
    //Remove control chars except: tab 9; new line 10; enter 13;
    $pattern = '/[\x00-\x08\x0E-\x1F\x0B\x0C\x7F]/u'; //0 - 8, 14 - 31, 11, 12, 127
    if ($trim_tab && !$trim_enter) $pattern = '/[\x00-\x09\x0E-\x1F\x0B\x0C\x7F]/u'; //0 - 9, 14 - 31, 11, 12, 127
    if (!$trim_tab && $trim_enter) $pattern = '/[\x00-\x08\x0A-\x1F\x7F]/u'; //0 - 8, 10 - 31, 11, 12, 127
    if ($trim_tab && $trim_enter) $pattern = '/[\x00-\x1F\x7F]/u'; //0-31; 127
    $rez = preg_replace($pattern, '', $rez);
    //Replace nbsp with regular space
    $rez = str_replace(chr(194).chr(160), ' ', $rez);
    //Trim ends
    $rez = trim($rez);
    //Cut off end over max len
    if ($max_len > 0) $rez = mb_substr($rez, 0, $max_len, 'UTF-8');
    //-----------------------
    return $rez;
  }

  function ldTrimSL($str) {
    return ldTrim($str, true, true);
  }

  function ldTrimInt($str) {
    return intval(preg_replace("/[^0-9\-]/", '', $str));
  }

  function ldTrimNum($str) {
    $rez = str_replace(array(',', '.'), DECIMAL, $str);
    return floatval(preg_replace("/[^0-9\-".DECIMAL."]/", '', $rez));
  }

  function ldTrimBool($str) {
    $rez = strtoupper($str);
    if (trim($rez) == 'ON') return true;
    $rez = substr(preg_replace("/[^01JYNX]/", '', $rez), 0, 1);
    if ($rez == '1' || $rez == 'J' || $rez == 'Y' || $rez == 'X') return true;
    //if ($rez == '0' || $rez == 'N' || $rez == '')
    return false;
    //return boolval($str);
  }

  function ldTrimDate($str, $order = 'YMD') { // 0000.00.00 - 9999.12.31 $order - to do
    if ($str == '') return '';
    //-----------------------
    $rez = str_replace(array('/', '-'), '.', $str);
    $rez = preg_replace("/[^0-9.]/", '', $rez);
    //-----------------------
    if ($str == '') return '';
    //if (substr_count($rez, '.') > 2) return '';
    //-----------------------
    $a = explode('.', $rez, 3);
    $c = getdate();
    //-----------------------
    if (!isset($a[0])) {
      return '';
    } else $g = intval($a[0]);
    if (!isset($a[1])) {
      $m = intval($c['mon']);
    } else $m = intval($a[1]);
    if (!isset($a[2])) {
      $d = intval($c['mday']);
    } else $d = intval($a[2]);
    //-----------------------
    if ($g < 0 || $g > 9999) return '';
    if ($m < 1 || $m > 12) return '';
    if ($d < 1 || $d > 31) return '';
    //-----------------------
    return sprintf('%04d.%02d.%02d', $g, $m, $d); //str_pad($g, 4, '0', STR_PAD_LEFT);
  } //echo (ldTrimDate('312')); die;

  function ldCleanStr($val, $allowed_chars = '') {
    if ($val == '') return '';
    if ($allowed_chars == '') $allowed_chars = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnmĒŪĪĀŠĢĶĻŽČŅēūīāšģķļžčņ 01234567890!@#$%^&*()-=_+[]{},.?|<>/~;:';
    //-----------------------
    $rez = '';
    $len = mb_strlen($val, 'UTF-8');
    for ($q = 0; $q < $len; $q++) {
      $char = mb_substr($val, $q, 1, 'UTF-8');
      if (mb_strpos($allowed_chars, $char, 0, 'UTF-8') === false) continue;
      $rez .= $char;
    }
    return $rez;
  }

  //=====================================================================================
  // PARAM
  //=====================================================================================

  //if (!isset($argv[1])) die;
  //$unid = $argv[1];

  //if($argc > 1) parse_str(implode('&', array_slice($argv, 1)), $_GET); //Hack

  //if (PHP_SAPI === 'cli') {
  //  $argument1 = $argv[1];
  //  $argument2 = $argv[2];
  //} else {
  //  $argument1 = $_GET['argument1'];
  //  $argument2 = $_GET['argument2'];
  //}

  function FixCLInput($debug = false) {
    if (PHP_SAPI != 'cli') return;
    //-----------------------
    global $_REQUEST, $argv;
    //-----------------------
    if ($debug) echo "--cli input translation-start-----\n";
    foreach ($argv as $n => $arg) {
      if ($n == 0) continue;
      $eq = strpos($arg, '=');
      if ($eq === false) continue;
      if ($eq < 1) continue;
      $a = substr($arg, 0, $eq);
      $v = substr($arg, $eq + 1);
      if ($debug) echo '['.$a.'] => "'.$v.'"'."\n";
      $_REQUEST[$a] = $v;
    }
    if ($debug) echo "--cli input translation-end-------\n";
  }

  //=====================================================================================
  // Params
  //=====================================================================================

  function ldGP($name) {
    $rez = null;
    if (isset($_REQUEST[$name])) $rez = $_REQUEST[$name];
      else if (isset($_REQUEST[strtolower($name)])) $rez = $_REQUEST[strtolower($name)];
        else if (isset($_REQUEST[strtoupper($name)])) $rez = $_REQUEST[strtoupper($name)];
    return $rez;
  }

  function ldGetParamS($name, $type = "none", $default = "") {
    global $SESSION_KEY;
    //-----------------------
    if (!isset($_SESSION[$SESSION_KEY]) || !is_array($_SESSION[$SESSION_KEY])) $_SESSION[$SESSION_KEY] = array();
    $SV = &$_SESSION[$SESSION_KEY];
    $SN = strtolower($name);
    //-----------------------
    //Param
    $rez = ldGP($name);
    //if user input we save, if null get saved
    if ($rez == null) {
      if (isset($SV[$SN])) $rez = $SV[$SN];
    } else $SV[$SN] = $rez;
    //Still null, return default - do not process or save
    if ($rez == null) {
      if (strtolower($type) == 'date') return ldTrimDate($default);
      return $default;
    }
    //-----------------------
    //Process result by type
    switch (strtolower($type)) {
      case 'none': return $rez;
      /*
      case 'trim': return ldTrimInt($rez);
      case 'lower': return ldTrimInt($rez);
      case 'upper': return ldTrimInt($rez);
      case 'db': return ldTrimInt($rez);
      case 'find': return ldTrimInt($rez);
      function ldGetParamTrim($name, $default = "") {    $rez = ldGP($name);    if ($rez == null) return $default; else return trim($rez);  }
      function ldGetParamLower($name, $default = "") {    $rez = ldGP($name);    if ($rez == null) return $default; else return strtolower(trim($rez));  }
      function ldGetParamUpper($name, $default = "") {    $rez = ldGP($name);    if ($rez == null) return $default; else return strtoupper(trim($rez));  }
      function ldGetParamDB($name, $default = "") {    $rez = ldGP($name);    if ($rez == null) return $default; else return pg_escape_string(utf8_encode((trim($rez)))); }
      function ldGetParamFiltered($name, $default = "") {         //$find_str = preg_replace("/[^A-Za-z0-9\s_ĒŪĪĀŠĢĶĻŽČŅēūīāšģķļžčņ\-\%]/", '', $find_str); //Stops hacking    $rez = ldGP($name);    if ($rez == null) $rez = $default;    return Trim(ldCleanStr($rez));  }
      */
      case 'str': return ldTrimSL($rez);
      case 'txt': return ldTrim($rez);
      case 'int': return ldTrimInt($rez);
      case 'num': return ldTrimNum($rez);
      case 'bool': return ldTrimBool($rez);
      case 'date': return ldTrimDate($rez);
    }
    return $rez;
  }

  function ldClearParamS($name = "") {
    global $SESSION_KEY; //debug($_SESSION[$SESSION_KEY]);
    //-----------------------
    if ($name == '') {
      if (isset($_SESSION[$SESSION_KEY])) {
        unset($_SESSION[$SESSION_KEY]);
        $_SESSION[$SESSION_KEY] = array();
      }
    } else {
      if (isset($_SESSION[$SESSION_KEY][$name])) unset($_SESSION[$SESSION_KEY][$name]);
    }
  }

  function ldGetParam($name, $default = "") {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return $rez;
  }

  function ldGetParamTrim($name, $default = "") {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return trim($rez);
  }

  function ldGetParamLower($name, $default = "") {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return strtolower(trim($rez));
  }

  function ldGetParamUpper($name, $default = "") {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return strtoupper(trim($rez));
  }

  function ldGetParamDB($name, $default = "") {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return pg_escape_string(utf8_encode((trim($rez))));
  }

  function ldGetParamFiltered($name, $default = "") {
    //$find_str = preg_replace("/[^A-Za-z0-9\s_ĒŪĪĀŠĢĶĻŽČŅēūīāšģķļžčņ\-\%]/", '', $find_str); //Stops hacking
    $rez = ldGP($name);
    if ($rez == null) $rez = $default;
    return Trim(ldCleanStr($rez));
  }

  //=====================================================================================
  // DB params
  //=====================================================================================

  function ldGetParamStr($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return ldTrimSL($rez);
  }

  function ldGetParamTxt($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return ldTrim($rez);
  }

  function ldGetParamInt($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return ldTrimInt($rez);
  }

  function ldGetParamNum($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return ldTrimNum($rez);
  }

  function ldGetParamBool($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return $default; else return ldTrimBool($rez);
  }

  function ldGetParamDate($name, $default = '') {
    $rez = ldGP($name);
    if ($rez == null) return ldTrimDate($default); else return ldTrimDate($rez);
  }

  //=====================================================================================
  // TEMPLATES
  //=====================================================================================

  //Atgriez sagatavi ar nomainitiem mainigajiem
  function ldGetTemplate($file, $args = null, $vals = null) {
    $file = file_get_contents("templates/".$file.".htm");
    $file = str_replace($args, $vals, $file);
    return $file;
  }

  //Izdruka sagatavi
  function ldPutTemplate($file, $args = null, $vals = null) {
    print ldGetTemplate($file, $args, $vals);
    return;
  }

  //Atgriez sagatavi ar ciklu
  function ldGetTemplateFor($file, $args, $vals, $data, $on_row) {
    $file = file_get_contents("templates/".$file.".htm");
    $file = str_replace($args, $vals, $file);
    $start = strpos($file, "%FOR_START%");
    $end = strpos($file, "%FOR_END%");
    $file_start = substr($file, 0, $start);
    $file_for = substr($file, $start + 11, $end - $start - 11);
    $file_end = substr($file, $end + 9);
    unset($file);
    $file_fors = "";
    foreach ($data as $key => $rec) $file_fors = $file_fors.strval(call_user_func($on_row, $file_for, $rec, $key));
    return $file_start.$file_fors.$file_end;
  }

  //Izdruka sagatavi ar ciklu
  function ldPutTemplateFor($file, $args, $vals, $data, $on_row) {
    print ldGetTemplateFor($file, $args, $vals, $data, $on_row);
    return;
  }

  //Pārlasa lapu ar norādīto adresi
  function ldReloadPage($url, $txt = '', $msg = '') {
    global $RELOAD;
    global $RELOAD_MSG;
    $url = str_replace('&amp;', '&', $url);
    if (strlen(strval($msg)) > 0)
      print str_replace(array('%URL%', '%TXT%', '%MSG%'), array($url, $txt, $msg), $RELOAD_MSG);
    else
      print str_replace(array('%URL%', '%TXT%'), array($url, $txt), $RELOAD);
    return;
  }

  //Pārlasa lapu ar norādīto adresi
  function ldGoBack($txt = 'Error') {
    global $GOBACK;
    print str_replace(array('%TXT%'), array($txt), $GOBACK);
    return;
  }

  //=====================================================================================
  // SQL
  //=====================================================================================

  function ldMakeSelectFind($fields, $find, $delimiter = ';') { //fields always delimited by ","
    $rez = '';
    //-----------------------
    if (!is_array($fields)) $fields = explode(',', $fields);
    if (!is_array($find)) $find = explode($delimiter, str_replace(array("\r", "\n"), array('', $delimiter), $find));
    //-----------------------
    $f = array();
    foreach ($fields as $tmp_val) if (trim($tmp_val) != '') $f[] = trim($tmp_val);
    $v = array();
    foreach ($find as $tmp_val) if (trim($tmp_val) != '') $v[] = trim($tmp_val);
    //-----------------------
    if (count($f) < 1 || count($v) < 1) return $rez;
    //-----------------------
    $w = array();
    foreach ($f as $tmp_f) {
      foreach ($v as $tmp_v) {
        if (strpos($tmp_v, '%') === false) $tmp_v = '%'.$tmp_v.'%';
        $w[] = "UPPER(".$tmp_f.") LIKE UPPER('".$tmp_v."')";
      }
    }
    $w = implode(" OR \n", $w); //debug($w);
    //-----------------------
    $rez = ' ( '.$w.' ) ';
    return $rez;
  }

  function ldMakeSelect($fields, $from, $where, $order) {
    $sql = '';
    //-----------------------
    $f = '*';
    if (is_array($fields)) {
      $tmp = array();
      foreach ($fields as $tmp_val) if (trim($tmp_val) != '') $tmp[] = trim($tmp_val);
      $fields = implode(', ', $tmp);
    }
    $fields = trim($fields);
    if ($fields != '') $f = $fields;
    //-----------------------
    if (is_array($from)) $from = implode(' ', $from);
    $t = $from;
    if ($t == '') return $sql;
    //-----------------------
    $w = '';
    if (is_array($where)) {
      $tmp = array();
      foreach ($where as $tmp_val) if (trim($tmp_val) != '') $tmp[] = trim($tmp_val);
      $where = implode(' AND '."\n", $tmp);
    }
    $w = trim($where);
    //-----------------------
    $o = '';
    if (is_array($order)) {
      $tmp = array();
      foreach ($order as $tmp_val) if (trim($tmp_val) != '') $tmp[] = trim($tmp_val);
      $order = implode(', ', $tmp);
    }
    $o = trim($order);
    //-----------------------
    $sql .= 'SELECT '.$f." \n";
    $sql .= 'FROM '.$t." \n";
    if ($w != '') $sql .= 'WHERE '."\n".$w." \n";
    if ($o != '') $sql .= 'ORDER BY '.$o;
    //-----------------------
    return $sql;
  }


  //=====================================================================================
  // UTILS
  //=====================================================================================

  function MakeMultiLine($txt) {
    return str_replace(array('|', "\n"), array('<br>', '<br>'), htmlspecialchars($txt)); // htmlspecialchars();
  }

  function MakeMultiLineHi($txt) {
    return str_replace(array('|', "\n", '[', ']'), array('<br>', '<br>', '<font class="font_hi">', '</font>'), htmlspecialchars($txt)); // htmlspecialchars();
  }

  function MakeSingleLineHi($txt) {
    return str_replace(array('|', "\n", '[', ']'), array(' ', ' ', '<font class="font_hi">', '</font>'), htmlspecialchars($txt)); // htmlspecialchars();
  }

  function TrimMultiLineHi($txt) {
    return str_replace(array('|', "\n", '[', ']'), array(';', ';', '', ''), htmlspecialchars($txt));
  }

  function MakeUrl($str) {
    $rez = strtolower($str);
    if (substr($rez, 0, 7) != 'http://') $rez = 'http://'.$rez;
    return $rez;
  }

  //function in_array2(&$element, &$array) { :(
  // foreach($array as $k => $r) if($r == $element) return true;
  // return false;
  //}

  function ldRandomString($length = 16) {
    $s = '';
    $c = '0123456789abcdefghijklmnopqrstuvwxyz';
    for ($i = 0; $i < $length; $i++) $s .= $c[rand(0, strlen($c) - 1)];
    return $s;
  }

  function ldRandomString2($length = 16) {
    $s = '';
    $c = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_=+[{]};:\|/?`~';
    for ($i = 0; $i < $length; $i++) $s .= $c[rand(0, strlen($c) - 1)];
    return $s;
  }

  //-------------------------------------------------------------------------------------------------

  function ldTrimLV($str) {
    $A = array('Ē', 'Ū', 'Ī', 'Ā', 'Š', 'Ģ', 'Ķ', 'Ļ', 'Ž', 'Č', 'Ņ',
               'ē', 'ū', 'ī', 'ā', 'š', 'ģ', 'ķ', 'ļ', 'ž', 'č', 'ņ');
    $B = array('E', 'U', 'I', 'A', 'S', 'G', 'K', 'L', 'Z', 'C', 'N',
               'e', 'u', 'i', 'a', 's', 'g', 'k', 'l', 'z', 'c', 'n');
    return str_replace($A, $B, $str);
  }

  function ldCleenFileName($name) {
    if ($name == '') return '';
    //-----------------------
    $allowed_chars = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm 01234567890()-=_+[]{},.~';
    //-----------------------
    $name = ldTrimLV($name);
    $len = strlen($name);
    //-----------------------
    $rez = '';
    for ($q = 0; $q < $len; $q++) if (strpos($allowed_chars, $name[$q]) !== false) $rez .= $name[$q];
    $rez = substr($rez, 0, 250);
    //-----------------------
    return $rez;
  }

  function ldImplodeFormat($glue, &$pieces, $format) {
    $pieces2 = array();
    foreach ($pieces as $tmp_val) $pieces2[] = sprintf($format, $tmp_val);
    return implode($glue, $pieces2);
  }

  //-------------------------------------------------------------------------------------------------

	//Izveido no tabulas pivotu
  //function oraMakePivot($base, $rows, $cols, $data, $op) {

  //  $new_array = Array();
  //  foreach( $old_array as $v ) {
  //    if(!isset( $new_array[$v["month"]][($v["department"].'-'.$v["property"])] )) {
  //      $new_array[$v["month"]][($v["department"].'-'.$v["property"])] = 0;
  //    }
  //    $new_array[$v["month"]][($v["department"].'-'.$v["property"])] += $v["total_no_students"];
  //  }
  //}

  //-------------------------------------------------------------------------------------------------

  function ToCBox($a) {
    if ($a == true) return ' checked ';
    return ' ';
  }

  function ToSBox($a, $n) {
    if ($a == $n) return ' selected ';
    return ' ';
  }

  //-------------------------------------------------------------------------------------------------

  $EXCEL_BR = '<br style="mso-data-placement:same-cell;" />';

  $NBSP = chr(194).chr(160);

  $TAB = str_repeat($NBSP, 8); //$tab = str_repeat('&nbsp;', 8);

  function SlashAddBR($a) {
    global $EXCEL_BR;
    return str_replace('/', $EXCEL_BR, $a);
  }

  function SlashDeleteBR($a) {
    $rez = $a;
    $rez = str_replace('-/', '', $rez);
    $rez = str_replace('/', ' ', $rez);
    return $rez;
  }

  function SemicolAddBR($a) {
    global $EXCEL_BR;
    if ($a == '') return $a;
    return str_replace(';', ';'.$EXCEL_BR, $a);
  }

  //function FixUserInfo($a) {
  //  global $wd, $EXCEL_BR;
  //  if ($wd == false) return wordwrap($a, 36, $EXCEL_BR, false);
  //  return $a;
  //}

  function ldDeleteFolderContent($dir) {
    $files = glob($dir.'/*'); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }
  }

  //=====================================================================================
  // FORMS
  //=====================================================================================

  function ldMakeSelCombo($name, $i_name, $add_all, $VALS, $num, $label_f) {
    $S = '';
    $all = '';
    if ($add_all) $all = '<option value="-1" '.ToSBox($num, -1).'>-=Visi=-</option>'."\n";;
    //-----------------------
    $S .= $name.': <select name="'.$i_name.'">'."\n";
    $S .= $all;
    if ($label_f == '') foreach ($VALS as $n => $tmp_row) $S .= '<option value="'.$n.'" '.ToSBox($num, $n).'>'.$tmp_row.'</option>'."\n";
                   else foreach ($VALS as $n => $tmp_row) $S .= '<option value="'.$n.'" '.ToSBox($num, $n).'>'.$tmp_row[$label_f].'</option>'."\n";
    $S .= '</select>'."\n";
    //-----------------------
    return $S;
  }

  //=====================================================================================
  // DATA ARRAYS & HTML
  //=====================================================================================

  function ldSortDataArray(&$DATA, $sort_fields) {

    function SortDataArrayComp($a, $b) { //wont sort on win, can not handle numbers
      global $sort_fields;
      //---------------------
      foreach ($sort_fields as $field) {
        $af = mb_strtolower(strval($a[$field])); //, mb_detect_encoding($string));
        $bf = mb_strtolower(strval($b[$field])); //, mb_detect_encoding($string));
        $n = strcoll($af, $bf);
        if ($n != 0) return $n;
      }
      return 0;
    }

    return usort($DATA, 'SortDataArrayComp');
  }

  function ldArrayGroup(&$DATA, $fields, $add_data = true, $start_key = null, $end_key = null) {
    $R = array();
    //-----------------------
    $first_key = 0;
    $last_key = count($DATA) - 1;
    if (!isset($start_key) || $start_key < $first_key) $start_key = $first_key;
    if (!isset($end_key) || $end_key > $last_key) $end_key = $last_key;
    //-----------------------
    $first = true;
    $last = array();
    $current = array();
    foreach ($fields as $tmp_field) {
      $last[$tmp_field] = '';
      $current[$tmp_field] = '';
    }
    //-----------------------
    //foreach ($DATA as $n => $tmp_row) {
    for ($n = $start_key; $n <= $end_key; $n++) {
      $tmp_row = &$DATA[$n];
      //Detect change
      $change = false;
      foreach ($fields as $tmp_field) {
        $current[$tmp_field] = $tmp_row[$tmp_field];
        if ($last[$tmp_field] != $current[$tmp_field]) {
          $last[$tmp_field] = $current[$tmp_field];
          $change = true;
        }
      }
      //Act on new or change
      if ($first || $change) {
        $R[] = $current; end($R); $tmp_key = key($R);
        $R[$tmp_key]['__START_KEY'] = $n;
        $R[$tmp_key]['__END_KEY'] = -1; //temp val
        if (!$first) $R[$tmp_key - 1]['__END_KEY'] = $n - 1;
      }
      //Data
      if ($add_data) $R[$tmp_key]['__ARRAY_DATA'][] = &$tmp_row;
      //not first any more
      $first = false;
    }
    if (!$first) $R[$tmp_key]['__END_KEY'] = $n - 1;
    //-----------------------
    return $R;
  }

  //=====================================================================================
  // HTML TABLES
  //=====================================================================================

  function FormatNumberStr($num) {
    $rez = floatval($num);
    $rez = number_format($rez, 2, '.', ' ');
    return $rez;
  }

  function FormatIntStr($num) {
    $rez = intval($num);
    $rez = number_format($rez, 0, '.', ' ');
    return $rez;
  }

  function FormatBoolStr($val, $true = true, $yes = 'Jā', $no = 'Nē') {
    $rez = $no;
    if ($val == $true) $rez = $yes;
    return $rez;
  }


  function MakeDataTable($DATI, $LAUKI, $NOSAUKUMI, $add_npk = true, $c_h = 'c_cel_yb', $c_b = 'c_cel_wl', $s_bn = 'c_cel_wr', $c_t = '') {
    $rez = '';
    //-----------------------
    if ($c_t != '') $rez .= '<table class="'.$c_t.'">'."\n";
               else $rez .= '<table>'."\n";
    $rez .= '<thead>'."\n";
    $rez .= '<tr>'."\n";
    if ($add_npk) $rez .= '<th class="'.$c_h.'">Npk.</th>'."\n";
    foreach ($NOSAUKUMI as $tmp_row) {
      $hh = $tmp_row;
      if (isset($hh[0]) && ($hh[0] == '@' || $hh[0] == '#')) $hh = substr($hh, 1);
      $rez .= '<th class="'.$c_h.'">'.$hh.'</th>'."\n";
    }
    $rez .= '</tr>'."\n";
    $rez .= '</thead>'."\n";
    //-----------------------
    $rez .= '<tbody>'."\n";
    foreach ($DATI as $n => $tmp_row) {
      $rez .= '<tr>'."\n";
      if ($add_npk) $rez .= '<th class="'.$c_b.'">'.strval($n + 1).'.</th>'."\n";
      foreach ($LAUKI as $m => $tmp_row2) {
        $dd = $tmp_row[$tmp_row2];
        $cc = $c_b;
        if (isset($NOSAUKUMI[$m][0]) && $NOSAUKUMI[$m][0] == '@') { $dd = FormatNumberStr($dd); $cc = $s_bn; }
        if (isset($NOSAUKUMI[$m][0]) && $NOSAUKUMI[$m][0] == '#') { $dd = FormatIntStr($dd); $cc = $s_bn; }
        $rez .= '<td class="'.$cc.'">'.$dd.'</td>'."\n";
      }
      $rez .= '</tr>'."\n";
    }
    $rez .= '<tbody>'."\n";
    //-----------------------
    $rez .= '</table>'."\n";
    //-----------------------
    return $rez;
  }



?>