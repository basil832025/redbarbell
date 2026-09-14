<?php



class actionCardNumber extends ActionModule
{
    function init()
    {
        slog('command=MyCard');

        $StatusReadAcc = actionmodule::readAccounts();

        if ($StatusReadAcc == 'OK' || $StatusReadAcc == 'OFFLINE') {
            if (SystemClass::$cntAccounts > 1 && SystemClass::$activeAccount == 0) {
                $this->setActiveAcc(0);
            } else {
                $dop_sql = '';
                if (actionmodule::$UserInfo['active_account'] > 0) {
                    $dop_sql = ' and acc=' . actionmodule::$UserInfo['active_account'];
                }

                $sql = 'select card from spr_acc where phone="' . actionmodule::$UserInfo['phone'] . '" ' . $dop_sql . ' limit 1';
                $card_num = db_field($sql, 'card');
              //  s($sql);
                $cnlen = strlen($card_num);
                $card_num = ($card_num == 'SITE_REG' || $card_num == '-' || $cnlen < 6)
                    ? actionmodule::$UserInfo['phone']
                    : $card_num;

                $this->returnCodeShtrih($card_num);
            }
        }

        $this->setLastOper('MyCard', $StatusReadAcc);
    }

    function returnCodeShtrih($code)
    {
        // папка
        $dir = ROOT_A . 'barcodes/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // итоговый файл (готовый к TG)
        $filePath1 = $dir . $code . '_1.png';
//s('$code='.$code.' '.$filePath1);
        // если был глюк и файл нулевой — удаляем
        if (file_exists($filePath1) && filesize($filePath1) == 0) {
            @unlink($filePath1);
        }

        // генерим, если нет
        if (!file_exists($filePath1)) {
       //     s('net card');
            // 1) Генерим "сырую" картинку штрихкода
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

            // ширина модуля и высота — ключевые параметры читаемости
            $moduleWidth = 3; // 2-4 обычно норм, начнём с 3
            $barHeight   = 90;

            $barcodePngBinary = $generator->getBarcode(
                (string)$code,
                $generator::TYPE_CODE_128,
                $moduleWidth,
                $barHeight
            );

            // 2) Добавляем quiet zone (отступы) белым полем
            //    Это важно для сканеров и чтобы TG не "съедал" границы
            $padX = 40; // слева/справа
            $padY = 25; // сверху/снизу

            $img = @imagecreatefromstring($barcodePngBinary);
            if ($img === false) {
                // если GD нет или что-то пошло не так — хотя бы сохраним как есть
                file_put_contents($filePath1, $barcodePngBinary);
            } else {
                $w = imagesx($img);
                $h = imagesy($img);

                $out = imagecreatetruecolor($w + $padX * 2, $h + $padY * 2);
                $white = imagecolorallocate($out, 255, 255, 255);
                imagefilledrectangle($out, 0, 0, $w + $padX * 2, $h + $padY * 2, $white);

                imagecopy($out, $img, $padX, $padY, 0, 0, $w, $h);

                // сохраняем без компрессии (0 = max quality)
                imagepng($out, $filePath1, 0);

                imagedestroy($img);
                imagedestroy($out);
            }
        }

        $ObjBut = new ButtonModule();
        SystemClass::$Button = $ObjBut->buttonAvtoriz();

        // Оставляю как у тебя — отправка фото.
        // Но знай: для 100% читаемости лучше "document" (без сжатия).
        SystemClass::$typeReturn = 'photo';

        SystemClass::$textMessage_bot = 'Твоя картка номер <b>"' . $code . '"</b>';

        SystemClass::$arrayQuery = array(
            'chat_id'      => SystemClass::getChatId(),
            'caption'      => SystemClass::$textMessage_bot,
            'photo'        => new CURLFile($filePath1),
            'parse_mode'   => "html",
            'reply_markup' => SystemClass::$Button,
        );

         SystemClass::TG_sendPhoto(SystemClass::$arrayQuery);
    }
}
