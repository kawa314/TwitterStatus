<?php
header("Content-Type: application/json; charset=utf-8");
header('X-Content-Type-Options: nosniff');

/**
 * Created by PhpStorm.
 * User: kouhei
 * Date: 15/11/28
 * Time: 18:28
 */
$screen_name = filter_input(INPUT_GET,'screen_name');
if (!preg_match("/^[a-zA-Z0-9_]+$/", $screen_name)) { exit; }
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

    //初期化
    $export = array(
        'code'=>403,
        'screen_name'=>$screen_name,
    );


    $sql = "SELECT * FROM analytics WHERE screen_name = ? AND fetched_date BETWEEN (CURDATE() - INTERVAL 15 DAY) AND (CURDATE() + INTERVAL 1 DAY) order by id asc";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1,$screen_name);
    $stmt->execute();
    $data = $stmt->fetchAll();
    //var_dump($data);

    $stmt->closeCursor();

    if($data && !empty($data)){

        $export['screen_name'] = $screen_name;
        $export['twitter_id'] = $data[0]['twitter_id'];
        $export['code'] = 200;
        $export['between']['A'] = $data[0]['fetched_date'];
        $export['between']['B'] = $data[count($data) - 1]['fetched_date'];

        foreach($data as $d){
            $export['data'][] = array(
                'data_id'=>$d['id'],
                'fetched_date'=>strtotime($d['fetched_date'])*1000,
                'follower'=>$d['follower'],
                'following'=>$d['following'],
            );
        }

    }




} catch (Exception $e) {
    //var_dump($e->getMessage());
    $export['code'] = 500;

    //rollback when failed.
    $pdo->rollBack();
}

//var_dump($export);

echo json_encode($export);
