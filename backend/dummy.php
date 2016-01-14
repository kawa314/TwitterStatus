<?php
/**
 * Created by PhpStorm.
 * User: kouhei
 * Date: 15/11/28
 * Time: 22:18
 */
if (array_shift(get_included_files()) === __FILE__) die('Only cli');
require '../config.php';
try {

    $pdo = new PDO(
        sprintf('mysql:dbname=%s;host=%s;charset=%s', DBNAME, DBHOST, DBCHARSET),
        DBUSER,
        DBPASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );

    $screen_name = "39ff";

    for($i = 1; $i<31; $i++) {
        /* Create dummy data to highchart-test */
        $pdo->beginTransaction();
        $sql = 'INSERT INTO analytics(screen_name,twitter_id,follower,following,fetched_date) VALUES(?,?,?,?,?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($screen_name, 1, 2500 + $i * rand(1,100), 2200 + $i * rand(1,70), date("2015-11-$i 12:00:00")));
        $stmt->closeCursor();
        $pdo->commit();
    }

}catch (Exception $e){

}