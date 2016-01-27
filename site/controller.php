<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class auth0Controller extends JControllerLegacy
{
    function display($cachable = false, $urlparams = false)
    {
        switch (JRequest::getVar('task')) {
            case 'auth':
                $this->AuthJUser();
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


    #################### Auth User #######################
    function AuthJUser()
    {

        if (!class_exists('Auth0Connect')) {
            require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'auth0_connect.php');
        }

        $params = JComponentHelper::getParams('com_auth0');

        $clientid = $params->get('clientid');
        $clientsecret = $params->get('clientsecret');
        $domain = 'https://' . $params->get('domain');

        $this->app = JFactory::getApplication('site');
        $code = $this->app->input->getVar('code');

        $auth0 = new Auth0Connect($domain, $clientid, $clientsecret, JRoute::_('index.php?option=com_auth0&task=auth', true, -1));

        $app = JFactory::getApplication();
        try {
            $accessToken = $auth0->getAccessToken($code);
            $userInfo = $auth0->getUserInfo($accessToken);

            if (($jUser = $this->doesUserExists($userInfo)) !== null) {
                $this->loginAuth0User($jUser);
            } else {
                $this->createAuth0User($userInfo);
            }

            $app->setUserState('users.login.form.data', array());
            $app->redirect(JRoute::_($app->getUserState('users.login.form.return'), false));

        } catch (Exception $e) {
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
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
            die($user->name . JText::_('COM_AUTH0_ALREADY_LOGGED_IN'));
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

            die('user bind error');
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
