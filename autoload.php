<?php

use Bitrix\Main\Loader;

//Автозагрузка наших классов
Loader::registerAutoLoadClasses(null, [
    'lib\usertype\CUserTypePropertyIblock' => APP_CLASS_FOLDER . 'usertype/CUserTypePropertyIblock.php',
]);


