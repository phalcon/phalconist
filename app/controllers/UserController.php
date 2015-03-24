<?php

namespace Controllers;

use Models\LogAction;

class UserController extends ControllerBase
{

    public function loginAction()
    {
        $this->view->login_url = $this->di->get('authProvider')->makeAuthUrl();
    }

    public function logoutAction()
    {
        $this->session->remove('identity');
        LogAction::log(LogAction::ACTION_LOGOUT, $this->user->get('id'));
        $this->response->redirect('');
    }
}
