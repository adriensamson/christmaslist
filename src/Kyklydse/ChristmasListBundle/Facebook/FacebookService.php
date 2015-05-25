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
    private $homeRoute;
    private $profileRoute;

    public function __construct($clientId, $clientSecret, RouterInterface $router, $callbackRoute, $homeRoute, $profileRoute)
    {
        $this->appId = $clientId;
        $this->appSecret = $clientSecret;
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
        $this->router = $router;
        $this->callbackRoute = $callbackRoute;
        $this->homeRoute = $homeRoute;
        $this->profileRoute = $profileRoute;
    }

    public function getLoginUrl()
    {
        return $this->getRedirectLoginHelper()->getLoginUrl([
            'email',
            'user_friends',
        ]);
    }

    public function getMessageUrl()
    {
        return 'http://www.facebook.com/dialog/send?' . http_build_query([
            'app_id' => $this->appId,
            'link' => $this->router->generate($this->homeRoute,  [], true),
            'redirect_uri' => $this->router->generate($this->profileRoute, [], true),
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

        return array_map(function ($arr) { return $arr->id; }, $data);
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
