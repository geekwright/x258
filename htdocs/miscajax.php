<?php

// code marker
use Xmf\Jwt\TokenFactory;
use Xmf\Jwt\TokenReader;
use Xmf\Module\Helper;
use Xmf\Request;

require_once __DIR__ . '/mainfile.php';

$GLOBALS['xoopsLogger']->activated = false;
error_reporting(E_ALL);

// claims we want to assert (verify)
$uid = (is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->uid() : 0;
$assertClaims = array('aud' => basename(__FILE__), 'uid' => $uid);

// handle ajax requests
if (0 === strcasecmp(Request::getHeader('X-Requested-With', ''), 'XMLHttpRequest')) {
    xoops_loadLanguage('misc');
    xoops_loadLanguage('user');

    $token = TokenReader::fromRequest('miscajax', 'Authorization', $assertClaims);
    if (false){ // === $token) {
        header("HTTP/1.0 401 Not Authorized", true, 401);
        //http_response_code(401);
        exit;
    }
    // The real work can happen here!
    $payload = getPayload();
    //http_response_code(200);
    header('Content-Type: application/json', true, 200);
    $jsonFlags = JSON_NUMERIC_CHECK;
    if (PHP_VERSION_ID >= 50400) {
        $jsonFlags |= JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    }
    echo json_encode($payload, $jsonFlags);
    exit;
}

function getPayload() {
    $type = Request::getCmd('type', '');
    switch ($type) {
        case 'online':
            $payload = getOnline();
            break;
        default:
            header("HTTP/1.0 404 Not Found", true, 404);
            //http_response_code(404);
            exit;
            break;
    }
    return $payload;
}

function getOnline()
{
    global $xoopsConfig, $xoopsUser;
    $start = Request::getInt('start', 0);
    $limit = Request::getInt('limit', 20);

    $isadmin = false;
    $timezone = $xoopsConfig['default_TZ'];
    if (is_object($xoopsUser)) {
        $isadmin = $xoopsUser->isAdmin();
        $timezone = $xoopsUser->timezone();
    }

    $variables['isadmin'] = $isadmin;
    $variables['lang_whoisonline'] = htmlentities(_WHOSONLINE);
    $variables['lang_close'] = htmlentities(_CLOSE);
    $variables['lang_avatar'] = htmlentities(_US_AVATAR);
    $variables['anonymous'] = $xoopsConfig['anonymous'];
    $variables['xoops_url'] = XOOPS_URL . '/';
    $variables['upload_url'] = XOOPS_UPLOAD_URL . '/';

    /* @var XoopsModuleHandler $module_handler */
    $module_handler = xoops_getHandler('module');
    $modules = $module_handler->getObjects(new Criteria('isactive', 1), true);

    /* @var XoopsOnlineHandler $onlineHandler */
    $onlineHandler = xoops_getHandler('online');
    $onlineTotal = $onlineHandler->getCount();
    $criteria = new CriteriaCompo();
    $criteria->setStart($start);
    $criteria->setLimit($limit);
    $onlines = $onlineHandler->getAll($criteria);

    $onlineUserInfo = array();
    foreach ($onlines as $online) {
        $info = array();
        $info['uid'] = $online['online_uid'];
        if (0 == $online['online_uid']) {
            $info['uname'] = $xoopsConfig['anonymous'];;
            $info['name'] = '';
            $info['avatar'] = 'avatars/blank.gif';
            $info['anon'] = 1;
        } else {
            /** @var XoopsUser $onlineUser */
            $onlineUser = new XoopsUser($online['online_uid']);
            $info['uname'] = $online['online_uname'];
            $info['name'] = $onlineUser->name();
            $info['avatar'] = $onlineUser->user_avatar();
            $info['anon'] = 0;
        }
        if ($isadmin) {
            $info['updated'] = formatTimestamp($online['online_updated'], 'm', $timezone);
            $info['ip'] = $online['online_ip'];
        }
        $info['mid'] = $online['online_module'];
        if (0 === $online['online_module'] || !isset($modules[$online['online_module']])) {
            $info['module_name'] = '';
            $info['dirname'] = '';
        } else {
            /** @var \XoopsModule $mod */
            $mod = $modules[$online['online_module']];
            $info['module_name'] = $mod->name();
            $info['dirname'] = $mod->dirname();
        }
        $onlineUserInfo[] = $info;
    }
    $variables['onlineUserInfo'] = $onlineUserInfo;

    // pagination info
    $variables['start'] = $start;
    $variables['limit'] = $limit;
    $variables['onlineTotal'] = $onlineTotal;

    return $variables;
}

/* notes
const inputjson = JSON.parse('{"isadmin":true,"lang_whoisonline":"Who\s Online","lang_close":"Close","lang_avatar":"Avatar","anonymous":"Anonymous","upload_url":"http:\/\/localhost\/x258i\/uploads\/","onlineUserInfo":[{"uid":"1","uname":"richard","name":"","avatar":"avatars\/savt5ec87d1fe0211.png","updated":"03\/18\/2021 23:02","ip":"::1","mid":"13","module_name":"Forum","dirname":"newbb"},{"uid":"0","uname":"Anonymous","name":"","avatar":"avatars\/blank.gif","updated":"03\/13\/2021 21:21","ip":"::1","mid":"32","module_name":"Xmfdemo","dirname":"xmfdemo"}],"start":0,"limit":20,"onlineTotal":"2"}');
const array1 = inputjson.onlineUserInfo;

array1.forEach(element => console.log(element));
*/