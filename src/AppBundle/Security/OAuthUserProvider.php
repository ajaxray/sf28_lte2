<?php

namespace AppBundle\Security;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;

/**
 * Class OAuthUserProvider
 */
class OAuthUserProvider extends BaseClass
{
    /** @var SessionInterface */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function __construct(UserManagerInterface $userManager, SessionInterface $session, array $properties)
    {
        parent::__construct($userManager, $properties);
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $socialId    = $response->getUsername();
        $user        = $this->userManager->findUserBy([$this->getProperty($response) => $socialId]);
        $email       = $response->getEmail();

        $this->session->set('oauth_token', $response->getOAuthToken());

        // Check if the user already has the corresponding social account
        if (null === $user) {

            // Check if the user has a normal account
            $user = $this->userManager->findUserByEmail($email);

            if (null === $user || !$user instanceof UserInterface) {
                // If the user does not have a normal account, set it up
                $user = $this->userManager->createUser();
                $user->setUsername($response->getUsername());
                $user->setFirstName($response->getFirstName());
                $user->setLastName($response->getLastName());
                $user->setEmail($email);
                $user->setPlainPassword(md5(uniqid()));
                $user->setRoles(['ROLE_CONTRIBUTOR']);
                $user->setEnabled(true);
            }

            $user->setGoogleId($socialId);
            $user->setProfilePicture($response->getProfilePicture());
            $user->setAccessToken($response->getAccessToken());

            $this->userManager->updateUser($user);

        } else {

            $user->setProfilePicture($response->getProfilePicture());
            $user->setAccessToken($response->getAccessToken());
            $this->userManager->updateUser($user);

            // Login the user
            $checker = new UserChecker();
            $checker->checkPreAuth($user);

        }

        return $user;
    }
}