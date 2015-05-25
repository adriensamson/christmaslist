<?php

namespace Kyklydse\ChristmasListBundle\Facebook;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Symfony\Component\Routing\RouterInterface;

class FacebookService
{
    private $appId;
    private $appSecret;
    private $router;
    private $callbackRoute;


    public function __construct($clientId, $clientSecret, RouterInterface $router, $callbackRoute)
    {
        $this->appId = $clientId;
        $this->appSecret = $clientSecret;
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
        $this->router = $router;
        $this->callbackRoute = $callbackRoute;
    }

    public function getLoginUrl()
    {
        return $this->getRedirectLoginHelper()->getLoginUrl([
            'email',
            'user_friends',
        ]);
    }

    public function getSessionFromRedirect()
    {
        return $this->getRedirectLoginHelper()->getSessionFromRedirect();
    }

    public function getMe(FacebookSession $session)
    {
        return (new FacebookRequest(
            $session, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className());
    }

    public function getFriendIds(FacebookSession $session)
    {
        $data = (new FacebookRequest(
            $session, 'GET', '/me/friends'
        ))->execute()->getGraphObject()->getProperty('data')->asArray();

        return array_map(function ($arr) { return $arr['id']; }, $data);
    }

    private function getRedirectLoginHelper()
    {
        return new FacebookRedirectLoginHelper(
            $this->router->generate($this->callbackRoute, [], true),
            $this->appId,
            $this->appSecret
        );
    }
}
