<?php

namespace Gigablah\Silex\OAuth\EventListener;

use Gigablah\Silex\OAuth\OAuthEvents;
use Gigablah\Silex\OAuth\Event\GetUserForTokenEvent;
use Gigablah\Silex\OAuth\Security\User\Provider\OAuthUserProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener to match OAuth user with the local user provider.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class UserProviderListener implements EventSubscriberInterface
{
    protected $externalUser;
    protected $app;
    /**
     * Populate the security token with a user from the local database.
     *
     * @param GetUserForTokenEvent $event
     */
    public function onGetUser(GetUserForTokenEvent $event)
    {
        $userProvider = $event->getUserProvider();

        if (!$userProvider instanceof OAuthUserProviderInterface) {
            return;
        }

        $token = $event->getToken();

        if ($user = $userProvider->loadUserByOAuthCredentials($token, $this->externalUser, $this->app)) {
            $token->setUser($user);
        }
    }

    public function setExternalUser($externalUser)
    {
        $this->externalUser = $externalUser;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            OAuthEvents::USER => 'onGetUser'
        );
    }
}
