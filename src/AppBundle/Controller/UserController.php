<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Avatar;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/admin/users/new", name="admin_user_create")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createUserAction(Request $request)
    {
        $data = [];

        if('POST' == $request->getMethod()) {
            $data = $request->request->all();

            $errors = $this->_validateUserData($data);
            if(count($errors) > 0) {
                $data['errors'] = $errors;
            } else {
                $manager = $this->get('fos_user.user_manager');
                $helper = $this->get('fos_user.util.user_manipulator');

                $pass = substr(md5(uniqid()), 0, 8);
                $user = $helper->create($data['username'], $pass, $data['email'], true, false);

                if(isset($data['firstName'])) $user->setFirstName($data['firstName']);
                if(isset($data['lastName'])) $user->setLastName($data['lastName']);
                if(isset($data['designation'])) $user->setDesignation($data['designation']);

                $user->addRole('ROLE_'. strtoupper($data['user_type']));
                $manager->updateUser($user, true);
                $this->getDoctrine()->getManager()->flush();

                $this->_welcomeResetRequest($user, $data['user_type']);
                $this->addFlash('success','User created successfully.');

                return $this->redirect($this->generateUrl('app_users'));
            }
        }

        return $this->render('AppBundle:User:create.html.twig', $data);
    }

    /**
     * @Route("/admin/users/{id}/update", name="admin_user_update")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateUserAction(User $user, Request $request)
    {
        $data = ['targetUser' => $user];

        if('POST' == $request->getMethod()) {
            $data = array_merge($data, $request->request->all());
            $errors = $this->_validateUserData($data, $user);
            if(count($errors) > 0) {
                $data['errors'] = $errors;
            } else {

                if(isset($data['firstName'])) $user->setFirstName($data['firstName']);
                if(isset($data['lastName'])) $user->setLastName($data['lastName']);
                if(isset($data['designation'])) $user->setDesignation($data['designation']);

                $role = 'ROLE_'. strtoupper($data['user_type']);
                if(!in_array($role, $user->getRoles())) {
                    $roles = in_array('ROLE_SUPER_ADMIN', $user->getRoles()) ? ['ROLE_SUPER_ADMIN'] : [];
                    $roles[] = 'ROLE_'. strtoupper($data['user_type']);
                    $user->setRoles($roles);
                }

                $this->get('fos_user.user_manager')->updateUser($user, true);
                $this->addFlash('success', 'User has been updated!');
                return $this->redirect($this->generateUrl('app_user_show', ['id' => $user->getId()]));
            }
        }

        return $this->render('AppBundle:User:create.html.twig', $data);
    }

    /**
     * @Route("admin/users/{id}/toggle", name="user_profile_toggle", methods="POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function toggleAction(User $user, Request $request)
    {
        $message = 'Invalid action, not applied';
        switch ($request->request->get('action')) {
            case "disable":
                $user->setEnabled(false);
                $message = 'User has been disabled!';
                break;
            case "enable":
                $user->setEnabled(true);
                $message = 'User has been enabled!';
                break;
        }

        $this->get('fos_user.user_manager')->updateUser($user, true);
        $this->addFlash('success', $message);

        return $this->redirect($this->generateUrl('app_user_show', ['id' => $user->getId()]));
    }

    /**
     * @Route("/profile/{id}/change-avatar", name="user_change_avatar", methods="POST")
     */
    public function changeAvatarAction(User $user, Request $request)
    {
        $file = $request->files->get('avatar');
        if($file instanceof UploadedFile && $file->isValid()) {

            if($file->getSize() > 20000) {
                $this->addFlash('error',"File size is too large ({$file->getSize()} bytes)! try smaller photo.");
            } else {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $avatar          = new Avatar();

                $uploadableManager->markEntityToUpload($avatar, $file);

                if($user->getAvatar()) {
                    @unlink($user->getAvatar()->getFilePath());
                    $this->getDoctrine()->getManager()->remove($user->getAvatar());
                }
                $this->getDoctrine()->getManager()->persist($avatar);
                $user->setAvatar($avatar);

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success','Avatar changed!');
            }

        } else {
            $this->addFlash('error',"Something went wrong! Did you select an image?");
        }

        return $this->redirect($this->generateUrl('app_user_show', ['id' => $user->getId()]));
    }

    private function _validateUserData($data, User $user = null)
    {
        $userTmp = is_null($user) ? new User() : $user;
        $userTmp->setUsername($data['username']);
        $userTmp->setEmail($data['email']);

        return $this->get('validator')->validate($userTmp);
    }

    private function _welcomeResetRequest(User $user, $role)
    {
        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $url = $this->get('router')->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $message = \Swift_Message::newInstance()
                                 ->setSubject('Welcome to IBBL Audit System')
                                 ->setFrom([$this->container->getParameter('mailer_sender_email') => $this->container->getParameter('mailer_sender')])
                                 ->setTo($user->getEmail())
                                 ->setBody(
                                     $this->renderView(
                                         'AppBundle:Email:registration.html.twig',
                                         array('username' => $user->getUsername(), 'role' => ucfirst($role), 'resetUrl' => $url)
                                     ),
                                     'text/html'
                                 );
        $this->get('mailer')->send($message);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);
    }


}
