<?php
/**
 * CalendarCount
 *
 * @Author  TakashiKakizoe
 * @Version 1.0.0
 *
**/
class CalendarCount
{

  /**
   * Return the number of days in the month from the year and month
   *
   * @param  int $year
   * @param  int $month (1<=$month<=12)
   * @return int (28||29||30||31)
  **/
  public static function returnDate($year = null,$month = null){
    $d = array('',31,'',31,30,31,30,31,31,30,31,30,31);
    if($year === null || $month === null || $month < 1 || $month > 12 || !is_numeric($year) || !is_numeric($month) ){
      return false ;
    }else{
      if($month != 2){
        return $d[$month];
      }else{
        return 28 + (1 / ($year % 4 + 1)) * (1 - 1 / ($year % 100 + 1)) + (1 / ($year % 400 + 1));
      }
    }
  }

  /**
   * Return start and end in timestamp from year, month, day etc
   *
   * @param  int $year
   * @param  int $month
   * @param  int $date
   * @return array $return(timestamp,timestamp)
  **/
  public static function periodToExtract($year=null,$month=null,$date=null)
  {
    $return = array('0','2147483647');
    $s = '';
    $e = '';
    if($year === null){

    }elseif($month === null){
      $s = mktime( 0,  0,  0,  1,  1, $year);
      $e = mktime(23, 59, 59, 12, 31, $year);
      $return = array( $s , $e ) ;
    }elseif($date === null){
      $s = mktime( 0,  0,  0, $month, 1, $year);
      $e = mktime(23, 59, 59, $month, self::returnDate($year,$month), $year);
      $return = array( $s , $e ) ;
    }else{
      $s = mktime( 0,  0,  0, $month, $date, $year);
      $e = mktime(23, 59, 59, $month, $date, $year);
      $return = array( $s , $e ) ;
    }
    unset($year,$month,$date,$s,$e);
    return $return ;
  }

  /**
   * Returns from the array / key how many times the element included in the array falls within the year
   *
   * @param  array   $array
   * @param  string  $key
   * @param  boolean $countFlg
   * @param  int     $yPass
   * @return mix array|boolean
  **/
  public static function returnCalendarCountYear($array=null,$key=null,$countFlg=true,$yPass=3){
    if($array === null || $key === null ){
      return false ;
    }
    $returnArray = array();
    $returnCount = array();

    $passedYear = array();
    for ($i=0; $i <= $yPass; $i++) {
      $passedYear[date('Y')-$i] = self::periodToExtract(date('Y')-$i) ;
    }


    $callback = function($year,$key,$passedYear){
      return function ($var) use ($year,$key,$passedYear) {
        return ( $passedYear[$year][0] < $var[$key] )&&( $var[$key] < $passedYear[$year][1] ) ;
      };
    };

    foreach ($passedYear as $year => $timestamp) {
      $returnArray[$year] = array_filter($array,$callback($year,$key,$passedYear));
      $returnCount[$year] = count($returnArray[$year]);
    }
    unset($passedYear,$i,$callback,$year,$key,$var,$timestamp);
    if($countFlg){
      return $returnCount;
    } else {
      return $returnArray;
    }
  }

  /**
   * Returning from array / key how many times the elements included in the array fall within the year / month
   *
   * @param  array   $array
   * @param  string  $key
   * @param  boolean $countFlg
   * @param  int     $yPass
   * @return mix array|boolean
  **/
  public static function returnCalendarCountMonth($array=null,$key=null,$countFlg=true,$yPass=3){
    if($array === null || $key === null ){
      return false ;
    }
    $returnArray = array();
    $returnCount = array();

    $passedYear = array();
    for ($i=0; $i <= $yPass; $i++) {
      $passedYear[date('Y')-$i] = array();
      for ($j=1; $j <= 12; $j++) {
        $passedYear[date('Y')-$i][$j] = self::periodToExtract(date('Y')-$i,$j);
      }
    }

    $callback = function($year,$month,$key,$passedYear){
      return function ($var) use ($year,$month,$key,$passedYear) {
        return ( $passedYear[$year][$month][0] < $var[$key] )&&( $var[$key] < $passedYear[$year][$month][1] ) ;
      };
    };

    foreach ($passedYear as $year => $montharray) {
      foreach ($montharray as $month => $timestamp) {
        $returnArray[$year][$month] = array_filter($array,$callback($year,$month,$key,$passedYear));
        $returnCount[$year][$month] = count($returnArray[$year][$month]);
      }
    }

    unset($passedYear,$i,$j,$callback,$year,$month,$passedYear,$var,$key);
    if($countFlg){
      return $returnCount;
    } else {
      return $returnArray;
    }
  }
}

?>
