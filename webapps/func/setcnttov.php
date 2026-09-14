<?php

function func_setcnttov(){
 //остатки по магазину
 s('setcnttov');
  db_query('delete from cscart_product_features_values where feature_id=18 and variant_id=113',DB_NAME_S);
 s('delete from cscart_product_features_values where feature_id=18 and variant_id=113');
//остатки по магазину ДНЕПР
   db_query('delete from cscart_product_features_values where feature_id=64 and variant_id=6367',DB_NAME_S);
   //остатки по магазину ЛЕВЫЙ БЕРЕГ
  db_query('delete from cscart_product_features_values where feature_id=65 and variant_id=6368',DB_NAME_S);
  // канцелярия ирпень
   db_query('delete from cscart_product_features_values where feature_id=66 and variant_id=6374',DB_NAME_S);
  //остатки по Таня магазин РЕЗЕРВ
 db_query('delete from cscart_product_features_values where feature_id=69 and variant_id=6375',DB_NAME_S);
 //остатки по .ЛАВИНА
  db_query('delete from cscart_product_features_values where feature_id=71 and variant_id=7023',DB_NAME_S);
//    db_query_('delete from cscart_product_features_values where feature_id=70 and variant_id=6377');
  //остатки по складу ирпень
   db_query('delete from cscart_product_features_values where feature_id=23 and variant_id=130',DB_NAME_S);
   //остатки по складу SM
   db_query('delete from cscart_product_features_values where feature_id=74 and variant_id=7036',DB_NAME_S);
// s('posle_del');
 $tovsCnt=array();
 $sTovOst='';
 $sql = 'update spr_tov_ost set product_id=(select product_id from cscart_products where product_code=tov limit 1) where product_id is null';
db_query($sql,DB_NAME_S);
 // проссируем все товары (select product_id from cscart_products where product_code=tov limit 1) as pr,
//  $sql='select product_id, tov, sum(cnt) as cnt from spr_tov_ost GROUP BY tov';
  $sql='SELECT product_id, tov,(select product_id from cscart_products where product_code=tov limit 1) as pr,cnt
FROM spr_tov_ost
where cnt>0
order by tov';
  $aTovsall=db_list($sql,DB_NAME_S);
   $fi=0;
   $prod=-1;
  // s('tyt11');
foreach ($aTovsall as $kall=>$tovVal)
{
    if (!empty($tovVal['tov'])  && $prod == $tovVal['tov']) continue;
 //   $sql=' select product_id from cscart_products where   product_code= '.$tovVal['tov'];
//    $product_id=db_field_($sql,'product_id'); 
//$product_id = $tovVal['product_id'];
//    if ($tovVal['tov']==9106634) print_r($tovVal);
  //  s($tovVal);
$product_id = $tovVal['pr'];
    $prod = $tovVal['tov'];
if ($product_id && $tovVal['cnt']>0)
{
 if ($fi) {  $sTovOst.=',';  }  
        $fi=1;

      // $sTovOst.=$v['tov'];
      $sTovOst.=$product_id;
 db_query('update cscart_products set status="A", amount='.$tovVal['cnt'].' where product_id='.$product_id,DB_NAME_S);
 //**************************************************************
 //--------------------------------------------------------------
  
 // выгружаем остатки по магазину
 $sql='select product_id, tov,cnt,podr from spr_tov_ost where podr="01" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 
 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(18,113,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(18,113,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
 
  // выгружаем остатки по магазину ДНЕПР
   $sql='select tov,cnt,podr from spr_tov_ost where podr="07" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 
 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(64,6367,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(64,6367,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
  
    // выгружаем остатки по магазину ЛЕВЫЙ БЕРЕГ
     $sql='select tov,cnt,podr from spr_tov_ost where podr="14" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(65,6368,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(65,6368,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
 

    // выгружаем остатки по Канцелярия Ирпень
       $sql='select tov,cnt,podr from spr_tov_ost where podr="11" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(66,6374,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(66,6374,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } 
 
  // выгружаем остатки по Таня магазин РЕЗЕРВ
   $sql='select tov,cnt,podr from spr_tov_ost where podr="08" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 
 
 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(69,6375,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(69,6375,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
 
   // выгружаем остатки по .ЛАВИНА
  $sql='select tov,cnt,podr from spr_tov_ost where podr="18" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 
 
 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(71,7023,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(71,7023,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================

 // выгружаем остатки по складу ирпень
  $sql='select tov,cnt,podr from spr_tov_ost where podr="04" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(23,130,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(23,130,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
 
 // выгружаем остатки по складу SM
  $sql='select tov,cnt,podr from spr_tov_ost where podr="32" and tov=' . $tovVal['tov'];
 $tovs=db_list($sql,DB_NAME_S);
 

 foreach ($tovs as $k=>$v)
 { 
 if ($v['cnt']>0) {
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(74,7036,'.$product_id.',"RU")',DB_NAME_S);
     db_query('insert into cscart_product_features_values (feature_id,variant_id,product_id,lang_code) values(74,7036,'.$product_id.',"UA")',DB_NAME_S);

    }  

 } //end ======================
 
 
 } // if product_id
} // 
//s('pered');
  $sql='update  cscart_products p set  amount=100 where EXISTS(SELECT * FROM `cscart_product_features_values` d 
where d.product_id=p.product_id and d.variant_id  in (130,133,134,135,137)) and 
 product_id not in ('.$sTovOst.') and status="A"';
  
 db_query($sql,DB_NAME_S);
 
  $sql='update  cscart_products p set status="H", amount=0 where not EXISTS(SELECT * FROM `cscart_product_features_values` d 
where d.product_id=p.product_id and d.variant_id  in (130,133,134,135,137)) and 
 product_id not in ('.$sTovOst.') and status="A"';
   
 db_query($sql,DB_NAME_S);
}
 //echo 'OK!';

?>