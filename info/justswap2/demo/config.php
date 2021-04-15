<?php
require('../vendor/autoload.php');

//部署后得到addresses.json
$addresses = json_decode(file_get_contents('./addresses.json'), true);

define('FACTORY_ADDRESS', $addresses['JustswapFactory']);
define('HUB_ADDRESS', $addresses['HubToken']);
define('WIZ_ADDRESS', $addresses['WizToken']);

//替换为自己的私钥
define('ALICE_PRIVKEY', '1234567890123456789012345678901234567890123456789012345678901234');
