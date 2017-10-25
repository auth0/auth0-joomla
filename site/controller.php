<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport( 'joomla.application.module.helper' );

class auth0Controller extends JControllerLegacy
{
    function display($cachable = false, $urlparams = false)
    {
        switch (JRequest::getVar('task')) {
            case 'auth':
                $this->AuthJUser();
                break;
            case 'coverify':
                $this->COVerify();
                break;
            default:
                break;
        }
        switch (JRequest::getVar('view')) {
            default:
                JRequest::setVar('view', 'auth0');
        }
        parent::display();
    }

    private function getReturn($key, $type) {
        $return    = base64_decode($this->app->input->get->get($key, '', $type));
        if (!JUri::isInternal($return))
        {
            $return = '';
        }
        return $return;
    }

    function COVerify() {
      $com_params = JComponentHelper::getParams('com_auth0');
      $clientid = $com_params->get('clientid');
      $domain = 'https://' . $com_params->get('domain');
      $redirect_uri = JRoute::_('index.php?option=com_auth0&task=auth', true, -1);

      echo '
        <!DOCTYPE html>
        <html>
        <head>
        <script src="http://cdn.auth0.com/js/auth0/9.0.0-beta.2/auth0.min.js"></script>
        <script type="text/javascript">
          var auth0 = new auth0.WebAuth({
          clientID: "'.$clientid.'",
          domain: "'.$domain.'",
          redirectUri: "'.$redirect_uri.'"
          });
          auth0.crossOriginAuthenticationCallback();
        </script>
        </head>
        <body></body>
        </html>';
      exit;
    }

    #################### Auth User #######################
    function AuthJUser()
    {

        if (!class_exists('Auth0Connect')) {
            require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'auth0_connect.php');
        }
        if (!class_exists('Auth0ValidationException')) {
            require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'auth0_validation_exception.php');
        }

        $this->app = JFactory::getApplication('site');

        $com_params = JComponentHelper::getParams('com_auth0');
        $module = JModuleHelper::getModule('mod_auth0');

        $mod_params = new JRegistry();
        $mod_params->loadString($module->params);

        $return = $this->getReturn('state', 'RAW');

        if (empty($return))
        {
            $return = $this->getReturn('return', 'BASE64');
        }

        if (empty($return))
        {
            $return = 'index.php?option=com_users&view=profile';
        }

        // Set the return URL in the user state to allow modification by plugins
        $this->app->setUserState('users.login.form.return', $return);

        $clientid = $com_params->get('clientid');
        $clientsecret = $com_params->get('clientsecret');
        $domain = 'https://' . $com_params->get('domain');

        $code = $this->app->input->getVar('code');

        $auth0 = new Auth0Connect($domain, $clientid, $clientsecret, JRoute::_('index.php?option=com_auth0&task=auth', true, -1));

        try {
            $accessToken = $auth0->getAccessToken($code);
            $userInfo = $auth0->getUserInfo($accessToken);

            if (($jUser = $this->doesUserExists($userInfo)) !== null) {
                $this->loginAuth0User($jUser);
            } else if (($jUser = $this->doesUserExistsUnlinked($userInfo)) !== null) {
                $this->linkAuth0User($jUser, $userInfo);
            } else {
                $this->createAuth0User($userInfo);
            }

            $this->app->setUserState('users.login.form.data', array());
            $this->app->redirect(JRoute::_($this->app->getUserState('users.login.form.return'), false));

        } catch (Auth0ValidationException $e) {
            $this->app->enqueueMessage($e->getMessage(), 'warning');
            $this->app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
        } catch (Exception $e) {
            $this->app->enqueueMessage(JText::sprintf('COM_AUTH0_GENERIC_ERROR'), 'warning');
            $this->app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
        }
    }

    protected function doesUserExists($userInfo)
    {
        $db = JFactory::getDBO();

        $nickname = $userInfo->nickname;
        $auth0Uid = $userInfo->user_id;

        $db->setQuery("SELECT #__users.* FROM #__users
                      INNER JOIN #__auth0_joomla_connect ON #__auth0_joomla_connect.joomla_userid = #__users.id
                      WHERE #__users.username='$nickname' AND #__auth0_joomla_connect.auth0_userid = '$auth0Uid'");

        $userDetails = $db->loadObjectList();

        if (count($userDetails) == 0) return null;

        return $userDetails[0];
    }

    protected function doesUserExistsUnlinked($userInfo)
    {
        $db = JFactory::getDBO();

        $nickname = $userInfo->nickname;
        $auth0Uid = $userInfo->user_id;

        $db->setQuery("SELECT #__users.* FROM #__users
                      LEFT JOIN #__auth0_joomla_connect ON #__auth0_joomla_connect.joomla_userid = #__users.id
                      WHERE #__users.username='$nickname' AND #__auth0_joomla_connect.auth0_userid IS NULL");

        $userDetails = $db->loadObjectList();

        if (count($userDetails) == 0) return null;

        return $userDetails[0];
    }

    protected function loginAuth0User($jUser)
    {
        jimport('joomla.user.helper');
        JPluginHelper::importPlugin('user');

        $mainframe = JFactory::getApplication();

        $options = array();
        $options['action'] = 'core.login.site';

        $response = new stdClass();
        $response->username = $jUser->username;

        $result = $mainframe->triggerEvent('onUserLogin', array((array)$response, $options));

    }

    protected function linkAuth0User($jUser, $userInfo)
    {
        jimport('joomla.application.application');
        jimport('joomla.user.helper');
        jimport('joomla.utilities.utility');
        JPluginHelper::importPlugin('user');
        jimport('joomla.environment.request');

        foreach ($userInfo->identities as $identity) {
            if ($identity->provider == "auth0") {
                if ( isset($identity->profileData) && isset($identity->profileData->email_verified) && !$identity->profileData->email_verified) {

                    throw new Auth0ValidationException(JText::sprintf('COM_AUTH0_CANT_LINK_UNVERIFIED_USERS'));

                }
            }
        }

        $session = JFactory::getSession();
        $db = JFactory::getDBO();
        $usersConfig = JComponentHelper::getParams('com_users');

        $mainframe = JFactory::getApplication();

        $jomuserid = $jUser->id;

        $intdatetime = time();

        if ($this->doesAuth0UserExists($userInfo->user_id)) {

            throw new Auth0ValidationException(JText::sprintf('COM_AUTH0_ALREADY_LINKED'));

        } else {
            $updateUserQuery = "INSERT INTO #__auth0_joomla_connect(joomla_userid,auth0_userid,joined_date,linked) VALUES ($jomuserid,'$userInfo->user_id',$intdatetime,1)";
        }
        $db->setQuery($updateUserQuery);
        $result = $db->query();

        if ($result) {
            $this->loginAuth0User($jUser);
        }


    }

    protected function createAuth0User($userInfo)
    {

        jimport('joomla.application.application');
        jimport('joomla.user.helper');
        jimport('joomla.utilities.utility');
        JPluginHelper::importPlugin('user');
        jimport('joomla.environment.request');

        $session = JFactory::getSession();
        $db = JFactory::getDBO();
        $user = clone(JFactory::getUser());
        $usersConfig = JComponentHelper::getParams('com_users');

        $mainframe = JFactory::getApplication();

        if (!$user->get('guest')) {
            throw new Auth0ValidationException(JText::sprintf('COM_AUTH0_ALREADY_LOGGED_IN'));
        }

        $newUsertype = $usersConfig->get('new_usertype', 2);

        if (!$newUsertype) {
            $newUsertype = 'Registered';
        }

        $randomepass = JUserHelper::genRandomPassword(5);
        $intdatetime = time();

        //email data
        $emailData = array();
        $emailData['name'] = $userInfo->name;
        $emailData['username'] = $userInfo->nickname;
        $emailData['email'] = $userInfo->email;
        $emailData['temp_pass'] = $randomepass;
        $emailData['auth0id'] = $userInfo->user_id;

        // binding process
        $userData = array();
        $userData['name'] = $userInfo->name;
        $userData['username'] = $userInfo->nickname;
        $userData['email'] = $userInfo->email;
        $userData['password'] = $randomepass;
        $userData['password2'] = $randomepass;
        $userData['sendEmail'] = 0;

        if (!$user->bind($userData, 'usertype')) {
            throw new Auth0ValidationException(JText::sprintf('COM_AUTH0_CANT_CREATE_USER'));
        }

        $user->set('groups', array($newUsertype));

        $user->set('id', 0);
        $date = JFactory::getDate();   //j3 change
        $user->set('registerDate', $date->toSql()); //j3 change

        if ($user->save()) {
            $jomuserid = $user->get('id');

            if ($this->doesAuth0UserExists($userInfo->user_id)) {
                $updateUserQuery = "UPDATE #__auth0_joomla_connect SET joomla_userid=$jomuserid,joined_date=$intdatetime WHERE auth0_userid='$userInfo->user_id'";
            } else {
                $updateUserQuery = "INSERT INTO #__auth0_joomla_connect(joomla_userid,auth0_userid,joined_date,linked) VALUES ($jomuserid,'$userInfo->user_id',$intdatetime,1)";
            }
            $db->setQuery($updateUserQuery);
            $result = $db->query();

            if ($result) {
                $options = array();
                $options['action'] = 'core.login.site';

                $response = new stdClass();
                $response->username = $userInfo->nickname;
                $response->password = $randomepass;

                $result = $mainframe->triggerEvent('onUserLogin', array((array)$response, $options));
            }

        }

    }

    public static function doesAuth0UserExists($auth0UserId)
    {
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM #__auth0_joomla_connect WHERE auth0_userid='" . $auth0UserId . "'";

        $db->setQuery($query);
        $count = $db->loadResult();

        return ($count > 0);
    }


}
