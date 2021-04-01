<?php
/**
 * XOOPS jwt Smarty compiler plug-in
 *
 * @copyright   2021 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Richard Griffith <richard@geekwright.com>
 */

/**
 * Insert a XOOPS JWT
 *
 * The key to use to sign the token must be specified as xmf-key.
 * All other parameters specified will be used as claims.
 * If uid=fill i given aa a parameter the uid claim will be switched
 * to the current session user.
 *
 * Example: <{jwt xmf-key=online aud=miscajax.php uid=fill}>
 *
 * @param $params
 * @param $smarty
 *
 * @return null
 */
function smarty_function_jwt($params, &$smarty)
{
    $keyIndex = 'xmf_key';
    if (isset($params[$keyIndex])) {
        $key = $params[$keyIndex];
        unset($params[$keyIndex]);
        if (isset($params['uid']) && 'fill' === $params['uid']) {
            $params['uid'] = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->uid() : 0;
        }
        $expirationOffset = (isset($params['expirationOffset'])) ? $params['expirationOffset'] : 60*60;
        unset($params['expirationOffset']);
        $jwt = \Xmf\Jwt\TokenFactory::build($key, $params, $expirationOffset);
        echo $jwt;
        return;
    }
    trigger_error('xmf-key parameter required');
}
