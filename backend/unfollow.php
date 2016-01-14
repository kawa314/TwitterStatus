<?php
/**
 * Created by PhpStorm.
 * User: kmasaya
 * Date: 30/11/2015
 * Time: 19:07
 */

if(empty($argv[1])){ exit('Usage:php cron.php screen_name'.PHP_EOL );}
$screen_name =  $argv[1];
if(!empty($argv[2])){
  $rcount = $argv[2];
}else{
  $rcount = 3;
}


require '../config.php';
require 'TwistOAuth.phar';

$to = new TwistOAuth(CK,CS,AT,ATS);
$count = '5000';    //取得数
//$rcount = '20';      //リムーブ数

/* Follow取得 */
$follow = $to->get('friends/ids', array('screen_name'=>$screen_name, 'count'=>$count));
/* FollowerID取得 */
$follower = $to->get('followers/ids', array('screen_name'=>$screen_name, 'count'=>$count));
$f_difference = array_diff($follow->ids, $follower->ids);
$remove = array_slice($f_difference, 0, $rcount);

foreach($remove as $rem){
    $to->post('friendships/destroy', array('user_id'=>$rem));
    print $rem . ' ';
    sleep(3);
}
