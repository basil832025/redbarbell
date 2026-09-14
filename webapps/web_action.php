<?php

class WebActionModule
{

    protected $action = '';



    function __construct()
    {
        $this->action=get('action') ? get('action') : poste('action');
    }

    function init()
    {
          //  echo WEBAPPS_ACTION;
         //   echo '<br>';
         //   echo $this->action;
        //   s(WEBAPPS_ACTION  );
        // s(WEBAPPS_ACTION  .strtolower($this->action) .'.php');
        if (file_exists(WEBAPPS_ACTION  .strtolower($this->action) .'.php')) {
            $class_action = $this->action;
            $obAct = new $class_action;
            //   s('$param_class='.$param_class);
            $obAct->init();

        }elseif (file_exists(WEBAPPS_REPORTS  .strtolower($this->action) .'.php')) {
            $class_action = $this->action;
            $obAct = new $class_action;
            //   s('$param_class='.$param_class);
            $obAct->init();

        }else


            echo 'Шлях до файла не коректний11! '.WEBAPPS_ACTION.strtolower($this->action) .'.php';
    }

}