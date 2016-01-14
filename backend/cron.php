<?php
/*

crontab -e
0 1 * * * cd /your_dir/backend/; php cron.php 39ff
*/
/*if (array_shift(get_included_files()) === __FILE__) die('Only cli'); */
if(empty($argv[1])){ exit('Usage:php cron.php screen_name'.PHP_EOL );}
$screen_name =  $argv[1];
require 'TwistOAuth.phar';
require '../config.php';
try {

    $pdo = new PDO(
        sprintf('mysql:dbname=%s;host=%s;charset=%s',DBNAME,DBHOST,DBCHARSET),
        DBUSER,
        DBPASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );

    $to = new TwistOAuth(CK,CS,AT,ATS);
    $data = $to->get('users/show',array('screen_name'=>$screen_name));
    //friends_count
    //followers_count

    $pdo->beginTransaction();
    $sql = 'INSERT INTO analytics(screen_name,twitter_id,follower,following,fetched_date) VALUES(?,?,?,?,?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($screen_name,$data->id,$data->followers_count,$data->friends_count,date('Y-m-d H:i:s')));
    $stmt->closeCursor();

    $pdo->commit();


} catch (Exception $e) {
    //var_dump($e->getMessage());
    $export['code'] = 500;

    //rollback when failed.
    $pdo->rollBack();

}
