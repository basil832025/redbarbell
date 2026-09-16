<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class new_reg_user extends ActionModule
{
    public $userinfo;
    public $arrDataAnswer;

    function __construct (){
        //        s('actionMYYY_CONSTR');
        $this->userinfo = ActionModule::$UserInfo;
        $this->arrDataAnswer = SystemClass::getarrDataAnswer();
    }
    function init (){

        if (!empty($this->arrDataAnswer['form'])){
            $form = $this->arrDataAnswer['form'];
            slog('form_new_reg_user');
            slog($form);
            $leadSource = $form['lead_source'] ?? '';
            // Джерело обов'язкове лише у повній анкеті нового клієнта.
            if (!empty($form['surname']) && !in_array($leadSource, ['nearby', 'social', 'city_ads', 'mall_radio'], true)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'mess' => 'Виберіть, звідки ви про нас дізналися.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $dop_phone='';
            // если у челоаек был не стандарний номер и он передал правильный
            if (!empty($form['phone']) ){
                $new_phone = $form['phone'];
                // Оставляем только цифры:
                $new_phone = preg_replace('/\D+/', '', $new_phone);

// Если вам нужен украинский формат с «380», можно дописать:
            /*    if (strlen($new_phone) === 10) {
                    $new_phone = '38' . $new_phone; // 380505585932
                }*/
                $dop_phone = !empty($new_phone) ? ' or phone = "'.$new_phone.'"' : '';
                $sql = 'update spr_users set phone="'.$new_phone.'", phone_orig="'.$this->userinfo['phone'].'" where id='.$this->userinfo['id'];
                db_query($sql);
                SystemClass::$phone = $new_phone;
            //    ActionModule::$phone = $new_phone;
            }else
                $new_phone = $this->userinfo['phone'];

            //$date_birtday= ('00.00.0000'!=$form['birthday'] && trim($form['birthday'])!='') ? ' birthday="'. date_for_sql_format($form['birthday']).'",' :'';
            //     $dat_vudachi= ('00.00.0000'!=$form['dat_vudachi'] && trim($form['dat_vudachi'])!='') ? ' dat_vudachi="'. date_for_sql_format($form['dat_vudachi']).'",' :'';
            $sql = 'select * from bs_reg_new_users where phone="'.$this->userinfo['phone'].'"'.$dop_phone.' limit 1';
            s($sql);
            $aUser = db_row($sql);
           // s('surname='.$form['surname']);
            // tесли челоек зарегистрировался то 2 раз не добавляем
            if (empty($aUser) && !empty($form['surname'])){
                $date_birtday= ('00.00.0000'!=$form['birthday'] && trim($form['birthday'])!='') ? ' birthday="'. ($form['birthday']).'",' :'';

                $phone_ =  'phone="'.$new_phone.'",';
                // //   cer="'.$form['cer'].'", type_doc="'.$form['type_doc'].'",number="'.$form['number'].'
                $set = ' surname="'.$form['surname'].'", 
         name="'.$form['name'].'",
         acc=0,
         cer="'.$form['cer'].'", type_doc="'.$form['type_doc'].'",number="'.$form['number'].'",
         lastname="'.$form['lastname'].'",
         sex="'.$form['sex'].'",
         club="'.$form['club'].'",
         lead_source="'.$leadSource.'",
         
         '.$date_birtday.$phone_.'
        
       ';
                $sql = 'insert into bs_reg_new_users set 
           '.$set.'    date_reg=now()
         '  ;
                s($sql);
                db_query($sql);

            }
            $sql='select ip_club from spr_clubs where id='.$form['club'];
            //    s($sql);
            $ip_club = db_field($sql,'ip_club');
            //  s('$ip_club='.$ip_club);
            ActionModule::$ip_club=$ip_club;
        //    s('tyt111');
            if (!empty($form['surname']))
            {
             //   s('tytt222');
              $status =   ActionModule::addNewUsersToB52(ActionModule::$UserInfo);
              if ($status == 'OK')  {
                  if (empty($this->userinfo['is_reg'])){
                      $sql = 'update spr_users set is_reg=now(),club="'.$form['club'].'" where id='.$this->userinfo['id'];
                      db_query($sql);
                      ActionModule::$UserInfo['is_reg']='1';



                  }
                  actionmodule::sendMessText('Ви успішно зареєстровані в нашому клубі! Чекаємо Вас на  візит');
                   $st = 'ok';
                   $mess='';
              }else {
                  $st = 'error';
                  $mess = 'Виникла помилка при додавані на сервер Ваших даних. Спробуйте пізніше!';
                  actionmodule::sendMessText($mess);

              }
                header('Content-Type: application/json');

                // если всё успешно:
                echo json_encode([
                    "status" => $st,
                    "mess" => $mess
                ]);
                exit;
            }
           else {
            //   s('tytt3333');
            //   s('new_reg_is');
                $StatusReadAcc = ActionModule::readAccounts();
               // s('$StatusReadAcc='.$StatusReadAcc);
                if ($StatusReadAcc=='NO_RESULT') {
                   $sql = 'update spr_users set is_reg=null  where id='.$this->userinfo['id'];
                    db_query($sql);
                    ActionModule::$UserInfo['is_reg']='0';
                    actionmodule::sendMessText('Вибачте ваш телефон не знайдено в базі даних клубу, який ви вибрали! Щоб вирішити Ваше питання зверніться до адміністратора клубу');
                   // s('NO_RESULT__new_reg');
                    header('Content-Type: application/json');

// если всё успешно:
                    echo json_encode([
                        "status" => "error",
                            "mess" =>"Вибачте ваш телефон не знайдено в базі даних клубу, який ви вибрали! Щоб вирішити Ваше питання зверніться до адміністратора клубу",
                    ]);
                    exit;
                }

                elseif ($StatusReadAcc=='OK'){
                    actionmodule::sendMessText('Ви успішно зареєстровані в нашому клубі! Чекаємо Вас на  візит');
                    header('Content-Type: application/json');

// если всё успешно:
                    $sql = 'update spr_users set is_reg=now(),club="'.$form['club'].'" where id='.$this->userinfo['id'];
                    db_query($sql);
                    ActionModule::$UserInfo['is_reg']='1';
                    echo json_encode([
                        "status" => "ok"
                    ]);
                    exit;
                }else
                {
                    echo json_encode([
                        "status" => "error",
                        "mess" =>"Вибачте! Сталася помилка при реєстрації, можливо недоступний сервер клубу",
                    ]);
                    exit;
                }


            }
                  } else{


            actionmodule::sendMessText('Щось пішло не так. Спробуйте пізніше!');
            header('Content-Type: application/json');

// если всё успешно:
            echo json_encode([
                "status" => "error",
                "mess" => 'Щось пішло не так. Спробуйте пізніше!'
            ]);
            exit;

        }



    }

}
