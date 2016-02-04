<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
defined('_JEXEC') or die('Restricted access');

if ($type == 'login') {

    $db = JFactory::getDBO();
    $document = JFactory::getDocument();

    if ($params->get('show-login-form') == 1) {

        $redirectUrl = $params->get('redirect-url');
        $showAsModal = ($params->get('show-as-modal') == 1);
        $bigSocialButtons = ($params->get('big-social-button') == 1);
        $lockURL = $params->get('lock-url');
        $formTitle = $params->get('form-title');
        $dict = $params->get('dict');
        $customCSS = $params->get('custom-css');
        $iconURL = $params->get('icon-url');
        $usernameStyle = $params->get('username-style');
        $gravatar = ($params->get('gravatar') == 1);
        $rememberLastLogin = ($params->get('remember-last-login') == 1);

        $comParams = JComponentHelper::getParams('com_auth0');

        $clientid = $comParams->get('clientid');
        $domain = $comParams->get('domain');

        /*
         * TODO: how to get de domain??
         * */
        $callbackURL = JRoute::_('index.php?option=com_auth0&task=auth', true, -1);

        $lockOptions = array();
        $lockOptions['callbackURL'] = $callbackURL;
        $lockOptions['responseType'] = 'code';
        $lockOptions['authParams'] = array('scope' => 'openid name email picture');
        $lockOptions['socialBigButtons'] = $bigSocialButtons;
        $lockOptions['gravatar'] = $gravatar;
        $lockOptions['usernameStyle'] = $usernameStyle;
        $lockOptions['rememberLastLogin'] = $rememberLastLogin;

        if (!empty($formTitle) && empty($dict)) {
            $lockOptions['dict'] = array(
                'signin' => array(
                    'title' => $formTitle
                )
            );
        } elseif (!empty($dict)) {
            $lockOptions['dict'] = $dict;
        }

        if (!empty($redirectUrl)) {
            $lockOptions['authParams']['state'] = base64_encode($redirectUrl);
        }

        if (!empty($iconURL)) {
            $lockOptions['icon'] = $iconURL;
        }

        if (!$showAsModal) {
            $lockOptions['container'] = 'auth0-login-form';
        }


        $lockOptionsJson = json_encode($lockOptions);

        $javascript = "
        var lock;
        jQuery( document ).ready(function(){
            lock = new Auth0Lock('$clientid', '$domain');
        });

        function a0ShowLock() {
            lock.show($lockOptionsJson);
        }

    ";

        $auth0js = '<script src="' . $lockURL . '"></script>';


        if ($showAsModal) {
            echo '<button onclick="a0ShowLock()">Login</button>';
        } else {
            $javascript .= "

        jQuery( document ).ready(function(){
            a0ShowLock();
        });

        ";
            echo '<div id="auth0-login-form"></div>';
        }

        if (!empty($customCSS)) {

            $document->addCustomTag('<style type="text/css">' . $customCSS . '</style>');

        }


        $document->addCustomTag($auth0js);
        $document->addScriptDeclaration($javascript);
    }

}
