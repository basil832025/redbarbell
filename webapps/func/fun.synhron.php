<?php
function start()
{ global $content,$javascript;
    $content= '<div >Старт синхронізації...</div>
    <div style="font-weight: bold;">Перший етап. Завантаження на сайт залишків товарів.</div>
    <div >1) Видалити старі залишки</div>';
    $javascript='clear_content();';

}
function del_old_ost()
{ global $content;
    db_query('update spr_tov_ost set iswork=1',DB_NAME_S);
    db_query('update spr_tov_ost set iswork=0, cnt=0 where iswork=1',DB_NAME_S);
    $date_stop= db_field('SELECT date_stop FROM `spr_history` order by date_stop desc limit 1','date_stop',DB_NAME_S);

    db_query('insert into spr_history set date_start="'.$date_stop.'", finish=0',DB_NAME_S);
    $content= '<div>Скрипт повернув: ОК</div>
<div>2) Вивантаження залишків з В52 до складу "магазин"</div>';
}

function set_data_B52_sklad_shop()
{ global $content;
    $cnt=0;
    $params = array('podr'=>'01', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);

    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content.= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>3) Вивантаження залишків з В52 до складу "Дніпро"</div>';


}
function set_data_B52_sklad_dnepr()
{ global $content;
    $cnt=0;

    $params = array('podr'=>'07', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);
    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content .= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>4) Вивантаження залишків з В52 до складу "Танюшка бухгалтерія"</div>';

}
function set_data_B52_sklad_tanya()
{ global $content;
    $cnt=0;
    $params = array('podr'=>'08', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);
    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content .= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>5) Вивантаження залишків з В52 до складу "Верхній вал"</div>';
}
function set_data_B52_sklad_irpen()
{ global $content;
    $cnt=0;
    $params = array('podr'=>'04', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);
    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content .= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>6) Вивантаження залишків з В52 до складу "Лавіна"</div>';
}
function set_data_B52_sklad_lavina()
{ global $content;
    $cnt=0;
    $params = array('podr'=>'18', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);
   // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content .= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>7) Вивантаження залишків з В52 до складу "FRANKA"</div>';
}
function set_data_B52_sklad_sm()
{ global $content;
    $cnt=0;
    $params = array('podr'=>'32', 'command'=>'get_sklad_ost');
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);
    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK')
    {
        //s($msg_res);
        work_arr_sklad($msg_res);
        $cnt = count ($msg_res);
        $content.= '<p>Разом записів: '.$cnt.' шт</p>';
    }else
    {
        $action_new='error';
        $content .= '<p>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</p>';
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    $content.='<div>8) Виконується скрипт, котрий вирівняє залишки</div>';
}
function set_cnt_work_tov()
{global $content;
//s('set_cnt_work_tov');
    func_setcnttov();
    $content= '<p>Скрипт повернув ОК</p>
<div>9) Вивантаження груп товарів</div>';
    // setcnttov();   
}
function work_arr_sklad($dataSQL)
{
    $sql='';
    foreach ($dataSQL as $data)
    {
        $sql= 'INSERT INTO spr_tov_ost (TOV,CNT,PODR) values("'.$data['TOV'].'","'.$data['CNT'].'","'.$data['PODR'].'" ) ON DUPLICATE KEY UPDATE TOV="'.$data['TOV'].'",CNT="'.$data['CNT'].'",PODR="'.$data['PODR'].'";
';   // s($sql);
        db_query($sql,DB_NAME_S);
    }
    //INSERT INTO spr_tov_ost (TOV,CNT,PODR) values('70736','1','01' ) ON DUPLICATE KEY UPDATE TOV='70736',CNT='1',PODR='01';
}

function set_history_finish()
{ global $content;
    $sql = 'select id from spr_history where finish=0 and date_stop is null order by id desc limit 1';
    $hist = db_row($sql,DB_NAME_S);
    s($hist);
    db_query('update spr_history set finish=1, date_stop=now() where id='.$hist['id'],DB_NAME_S);
    $content= '<div>Скрипт повернув: ОК</div>';
}
function finish()
{ global $content,$javascript;
    $content= '<p>----------------</p>
<div> Синхнонізація закінчена!!!</div>';
    $javascript='secundomer.resetStopwatch();';
}
function set_grp_to_b52_all()
{
    global $content,$socket;
    $cnt_error = 0;
    $sql_all ='';
    $cnt=0;
    $sql = 'SELECT d.variant_id as kod, d.variant as name FROM `cscart_product_features_values` f, cscart_product_feature_variant_descriptions d 
 WHERE f.feature_id=39 and f.variant_id=d.variant_id 
group by d.variant';
    $aData = db_list($sql,DB_NAME_S);
    $manydata=[];
    foreach ($aData as $row)
    {  $cnt++;
        $name =str_replace('"',' ',$row['name']);
        $manydata_['kod']=$row['kod'];
        $manydata_['name']=$name;
        $manydata[]=$manydata_;
       // $sql_all.='UPDATE OR INSERT INTO spr_group (kod,name) values(###'.$row['kod'].'###,###'.$name.'### ) matching (kod);'."\r\n";
    }
    // $sql_all='UPDATE OR INSERT INTO spr_group (kod,name) values(###6377###,###33 Element ### ) matching (kod);';
   // $manydata=[];
    $params = array('command'=>'UPD_INS_GRP_TOV','manydata'=>$manydata );
    //   s($params);
    list($status,$type_result,$msg_res)=jwt_request($params);

//    list($status,$type_result,$msg_res)= $socket->set_grp_timeshop_all($sql_all);
    // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка

// s($sql_all);
    if ($status=='OK')
    {
        //s($msg_res); 

    }else
    {
        $cnt_error++;
        // $action_new='error';
        $content = 'Помилка: '.$status.': '.$type_result. ' + ='.$msg_res;
        s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

    }
    sleep(1);


    $content= '<p>Кількість помилок: '.$cnt_error.', кількість записів: '.$cnt.'</p>
<div>10) Вивантаження товарів</div>';
}
function set_tovs_to_b52_all()
{
    global $content,$socket;
    $cnt_error = 0;
    $sql_all ='';
    $cnt_limit = 200 ; //количесвто лимита для отправки в В52
   // $cnt_limit = 50 ;
    $prop = !empty($_POST['prop']) ? $_POST['prop'] : 0;
    $sum_limits = !empty($_POST['sum_limits']) ? $_POST['sum_limits'] : 0;
    $cnt_tovs = !empty($_POST['cnt_tovs']) ? $_POST['cnt_tovs'] : 0;
    s($_POST);
    s('prop='.$prop);

    $cnt=0;
    if (empty($prop)){
        // получим количество записей
        $sql='SELECT count( * ) as cn

from cscart_product_descriptions p, cscart_products cp,cscart_product_prices pp
 where lang_code="ua"  and pp.product_id=cp.product_id and
cp.product_id=p.product_id 

         --  and pc.link_type="M"
and ( cp.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or cp.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or p.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or p.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or pp.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or pp.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
)
order by 1 desc 

';
        $cnt_tovs=db_field($sql,'cn',DB_NAME_S);
      //  $cnt_tovs=230;
        s('$cnt_tovs='.$cnt_tovs);
      //  $_SESSION['prop'] =0;
        if ($cnt_tovs>$cnt_limit) {
            $sum_limits=  intdiv( $cnt_tovs, $cnt_limit); // 230 % 50 = 4
            s('$sum_limits_firts='.$sum_limits);
        }
    }

    $sql = 'select cp.product_code as kod,p.product as name,
IFNULL((select f.variant_id from `cscart_product_features_values` f where feature_id = 39 and lang_code="ua" and f.product_id=p.product_id),216) as grp,
round(pp.price) as price_out,ROUND(pp.price) tare
from cscart_product_descriptions p, cscart_products cp,cscart_product_prices pp
 where lang_code="ua"  and pp.product_id=cp.product_id and
cp.product_id=p.product_id 

         --  and pc.link_type="M"
and ( cp.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or cp.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or p.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or p.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or pp.sc_update_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
or pp.sc_insert_date >= (select date_start from spr_history where finish=1 ORDER BY id DESC LIMIT 1)
)
order by 1 desc 

limit '. $prop*$cnt_limit . ', '.$cnt_limit;
  s($sql);
    $aData = db_list($sql,DB_NAME_S);
    $cnt_row_limit=0;
    $manydata=[];
    foreach ($aData as $row)
    {  $cnt++;
        $cnt_row_limit++;
       // $name =str_replace('"',' ',$row['name']);
        $manydata_['kod']=$row['kod'];
        $manydata_['name']=$row['name'];
        $manydata_['grp']=$row['grp'];
        $manydata_['price_out']=$row['price_out'];
        $manydata_['tare']=$row['tare'];
        $manydata[]=$manydata_;
       // $sql_all.='UPDATE OR INSERT INTO spr_tov (kod,name,grp,price_out,tare) values(###'.$row['kod'].'###,###'.$row['name'].'###,###'.$row['grp'].'###,###'.$row['price_out'].'###,###'.$row['tare'].'### ) matching (kod);'."\r\n";
     /*   if ($cnt_row_limit==50)
        {

            //    s($cnt_row_limit);
            //   s($cnt);
            //  s($sql_all);
            $cnt_row_limit=0;
          //  list($status,$type_result,$msg_res)= $socket->set_tovs_timeshop($sql_all);
            $params = array('command'=>'UPD_INS_TOV_PRICES','manydata'=>$manydata );
            //   s($params);
            list($status,$type_result,$msg_res)=jwt_request($params);

            if ($status=='OK')
            {
                s('set_tovs_to_b52_all=resut>>>1000=');
                s($msg_res);
                //  s($msg_res);
                $content.= '<p>Кількість помилок: '.$cnt_error.', кількість записів: '.$cnt.'</p>';

            }else
            {
                $cnt_error++;
                // $action_new='error';
                $content .= 'Помилка: '.$status.': '.$type_result. ' + ='.$msg_res;
                s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

            }
            sleep(1);
            $sql_all ='';$manydata=[];


        }*/
    }
    // $sql_all='UPDATE OR INSERT INTO spr_group (kod,name) values(###6377###,###33 Element ### ) matching (kod);';
  //  if ($sql_all!='')
    if (!empty($manydata))
    {
      //  list($status,$type_result,$msg_res)= $socket->set_tovs_timeshop($sql_all);
        $params = array('command'=>'UPD_INS_TOV_PRICES','manydata'=>$manydata );
        //   s($params);
        list($status,$type_result,$msg_res)=jwt_request($params);
        // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка

// s($sql_all);
        if ($status=='OK')
        {
            s('set_tovs_to_b52_all=resut<1000=');
            s($msg_res);

        }else
        {
            $cnt_error++;
            // $action_new='error';
            $content .= '<div>Помилка: '.$status.': '.$type_result. ' + ='.$msg_res.'</div>';
            s($status.': '.$type_result. ' + ='.$msg_res); // если не ОК то какая то проблема и ошибка

        }
        sleep(1);
    }
    s('$sum_limits='.$sum_limits);

    if ($prop<$sum_limits){
        $prop++;
        $content= '<div>Кількість помилок: '.$cnt_error.', кількість записів: '.$cnt*$prop.'/'.$cnt_tovs.'</div>';
        Ajax(array('content' => $content,
            'message_user' => '',
            'error' => '',
            'action_new' => 'set_tovs_to_b52_all',
            'java_script' => '',
            'post_return' => 'prop='.$prop.'&sum_limits='.$sum_limits.'&cnt_tovs='.$cnt_tovs,
        ));
    }
    $content= '<div>Кількість помилок: '.$cnt_error.', кількість записів: '.($cnt_limit*$prop + $cnt) .'/'.$cnt_tovs.'</div>
<div>11) Закінчуємо вивантаження</div>';
}