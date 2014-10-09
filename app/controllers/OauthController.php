<?php

namespace Controllers;

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
            // todo
            return $this->response->redirect('');
        }

        if (empty($user->id)) {
            // todo
            return $this->response->redirect('');
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

        return $this->response->redirect('');
    }
}

