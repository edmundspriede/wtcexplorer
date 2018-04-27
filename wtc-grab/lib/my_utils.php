<?php

  require_once (realpath(__DIR__).'/utils.php');

  //===============================================================================================

  //Define before this; please include in config.php
  //$MY_DB_SERV
  //$MY_DB_USER
  //$MY_DB_PASS
  //$MY_DB_NAME

  //===============================================================================================

  $my_conn = null;

  //Error handling
  function myError($num, $txt, $sql = '') {
    global $my_conn;
    //-----------------------
    $msg = 'MySQL error: '.$num.' - '.$txt;
    if ($sql != '') $msg .= ' / '.$sql;
    die($msg);
    //throw new Exception($msg);
  }

  //Pieslēguma parametru uzsdtādīšana, ja vajag
  function myStartupParams() {
    global $my_conn;
    //-----------------------
    //Data format
    //Decimal point
    //etc
  }

  function myParamType(&$val) {
    if (is_int($val)) return 'i';
    if (is_float($val)) return 'd';
    return 's';
  }

  function myMakeSql($sql, $params, $values) { //:p_aa => 'It\'s cool!'
    global $my_conn;
    //-----------------------
    $p = array();
    foreach ($params as &$tmp) $p[] = ':'.$tmp;
    $v = array();
    foreach ($values as &$tmp) {
      $pt = myParamType($tmp);
      switch ($pt) {
        case 'i': $v[] = strval($tmp); break;
        case 'd': $v[] = str_replace(DECIMAL, '.', strval($tmp)); break;
        default: $v[] = "'".$my_conn->real_escape_string($tmp)."'";
      }
    }
    //-----------------------
    return str_replace($p, $v, $sql);
  }

  //===============================================================================================

  //Pievienojas DB
  function myConnect() {
    global $my_conn;
    global $MY_DB_SERV, $MY_DB_USER, $MY_DB_PASS, $MY_DB_NAME;
    //-----------------------
    $my_conn = new mysqli($MY_DB_SERV, $MY_DB_USER, $MY_DB_PASS, $MY_DB_NAME);
    if ($my_conn->connect_errno > 0) myError($my_conn->connect_errno, $my_conn->connect_error);
    //-----------------------
    myStartupParams();
  }

  //Atvienojas no DB
  function myClose() {
    global $my_conn;
    //-----------------------
    $my_conn->close();
  }

  //===============================================================================================

  //Atgriez datus no DB
  function myGetQueryResult($sql) {
    global $my_conn;
    //-----------------------
    $query = $my_conn->query($sql, MYSQLI_USE_RESULT);
    if ($query === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    $rez = array();
    while ($row = $query->fetch_assoc()) $rez[] = $row;
    $query->free();
    //-----------------------
    return $rez;
  }

  //Atgriez lauka sarakstu no DB
  function myGetDataField($sql, $field) {
    global $my_conn;
    //-----------------------
    $query = $my_conn->query($sql, MYSQLI_USE_RESULT);
    if ($query === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    $rez = array();
    while ($row = $query->fetch_assoc()) $rez[] = $row[$field];
    $query->free();
    //-----------------------
    return $rez;
  }

  //Atgriez ierakstu no DB
  function myGetRecord($sql) {
    global $my_conn;
    //-----------------------
    $query = $my_conn->query($sql, MYSQLI_USE_RESULT);
    if ($query === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    $rez = $query->fetch_assoc();
    $query->free();
    //-----------------------
    return $rez;
  }

  //Atgriez vertibu no DB
  function myGetValue($sql, $field, $default = null) {
    global $my_conn;
    //-----------------------
    $query = $my_conn->query($sql, MYSQLI_USE_RESULT);
    if ($query === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    $rez = $query->fetch_assoc();
    if (!isset($rez[$field])) return $default;
    $query->free();
    //-----------------------
    return $rez[$field];
  }

  //Izpilda pieprasijumu PHP 5.6 +
  function myExeSql($sql, $params = null) { //params array or single value (?, ?, ?, ?, ...)->(int, int, str, flt, flt, str, str, ...)
    global $my_conn;
    //-----------------------
    $p_query = $my_conn->prepare($sql);
    if ($p_query === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    if (isset($params)) {
      $bind_types = '';
      $bind_params = array();
      //-----------------------
      if (is_array($params)) {
        foreach ($params as &$tmp) {
          $bind_types .= myParamType($tmp);
          $bind_params[] = $tmp;
        }
      } else { //single parameter
        $bind_types = myParamType($params);
        $bind_params = array($params);
      }
      //-----------------------
      //escape -> not needed with params
      //foreach ($bind_params as $n => &$tmp)
      //  if (substr($bind_types, $n, 1) == 's') $tmp = $my_conn->real_escape_string($tmp);
      //-----------------------
      //PHP >= 5.6
      //$p_query->bind_param($bind_types, ...$bind_params); //Need PHP 5.6 or newer
      //-----------------------
      //PHP < 5.6
      $tmp_arr = array(&$bind_types);
      foreach($bind_params as &$tmp) $tmp_arr[] = &$tmp;
      call_user_func_array(array($p_query, 'bind_param'), $tmp_arr);
      //-----------------------
    }
    //-----------------------
    $p_rez = $p_query->execute();
    if ($p_rez === false) myError($my_conn->errno, $my_conn->error, $sql);
    //-----------------------
    $p_query->close();
  }

?>