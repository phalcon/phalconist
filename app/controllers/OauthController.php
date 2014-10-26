<?php

namespace Controllers;

use Models\LogAction;

class OauthController extends ControllerBase
{

    public function githubAction()
    {
        $code = $this->request->get('code');

        /** @var \SocialConnect\Github\Provider $provider */
        $provider = $this->di->get('authProvider');
        $accessToken = $provider->getAccessToken($code);

        /** @var \SocialConnect\Common\Entity\User $user */
        if (!$user = $provider->getUser($accessToken)) {
            $this->flash->warning('Sorry, we cant get GitHub user right now');
            return $this->response->redirect('/');
        }

        if (empty($user->id)) {
            $this->flash->warning('Sorry, no GitHub user ID found');
            return $this->response->redirect('/');
        }

        \Models\User::add((array)$user);

        $this->session->set(
            'identity',
            [
                'id'       => $user->id,
                'username' => $user->name,
                'avatar'   => $user->avatar_url,
            ]
        );

        LogAction::log(LogAction::ACTION_LOGIN, $user->id);

        return $this->response->redirect('');
    }
}
