<?php

namespace Controllers;

class UserController extends ControllerBase
{

    public function logoutAction()
    {
        $this->session->remove('identity');
        $this->response->redirect('');
    }
}