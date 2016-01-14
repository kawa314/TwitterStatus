<?php
/**
 * Created by PhpStorm.
 * User: kmasaya
 * Date: 2015/12/02
 * Time: 13:05
 */

if(empty($argv[1])){ exit('Usage:php cron.php screen_name'.PHP_EOL );}
$screen_name =  $argv[1];
if(!empty($argv[2])){
    $fcount = $argv[2];
}else{
    $fcount = 3;
}


require '../config.php';
require 'TwistOAuth.phar';

$to = new TwistOAuth(CK,CS,AT,ATS);
$count = '5000';    //取得数
$myscreen_name  =   'HaloDsny';  //自分のTwitter名

/* MyFollowing取得 */
$mefollowing = $to->get('friends/ids', array('screen_name'=>$myscreen_name, 'count'=>$count));
/* Target FollowerID取得 */
$target = $to->get('followers/ids', array('screen_name'=>$screen_name, 'count'=>$count));
$f_difference = array_diff($target->ids, $mefollowing->ids);     //自フォローとターゲットフォロワー差分
$following = array_slice($f_difference, 100, $fcount);

foreach($following as $fol){
    $to->post('friendships/create', array('user_id'=>$fol));
    print $fol . ' ';
    sleep(5);
}
