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
        $lockOptions['auth'] = array();;
        $lockOptions['auth']['redirectUrl'] = $callbackURL;
        $lockOptions['auth']['responseType'] = 'code';
        $lockOptions['auth']['params'] = array('scope' => 'openid email profile');
        $lockOptions['socialButtonStyle'] = $bigSocialButtons ? 'big' : 'small';

        if (!$gravatar) {
          $lockOptions['gravatar'] = null;
        }

        $lockOptions['usernameStyle'] = $usernameStyle;
        $lockOptions['rememberLastLogin'] = $rememberLastLogin;

        if (!empty($formTitle) && empty($dict)) {
            $lockOptions['languageDictionary'] = array(
              'title' => $formTitle
            );
        } elseif (!empty($dict)) {
            $lockOptions['languageDictionary'] = $dict;
        }

        if (!empty($redirectUrl)) {
            $lockOptions['auth']['params']['state'] = base64_encode($redirectUrl);
        }

        if (!empty($iconURL)) {
            $lockOptions['theme'] = array();
            $lockOptions['theme']['logo'] = $iconURL;
        }

        if (!$showAsModal) {
            $lockOptions['container'] = 'auth0-login-form';
        }


        $lockOptionsJson = json_encode($lockOptions);

        $javascript = "
        var lock;
        jQuery( document ).ready(function(){
            lock = new Auth0Lock('$clientid', '$domain', $lockOptionsJson);
        });

        function a0ShowLock() {
            lock.show();
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
