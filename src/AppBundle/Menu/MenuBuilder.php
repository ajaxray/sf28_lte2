<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    /** @var FactoryInterface  */
    private $factory;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'sidebar-menu')
        ;

        $menu->addChild('Dashboard', ['route' => 'dashboard'])
            ->setAttribute('icon', 'fa fa-dashboard')
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Users', ['route' => 'app_users'])
                ->setAttribute('icon', 'fa fa-user')
            ;
        }

// Example of adding child menu items with dropdown
//
//        $menu->addChild('Tasks')
//            ->setAttribute('dropdown', true)
//            ->setAttribute('icon', 'fa fa-tasks')
//            ->setLinkAttribute('data-hover', 'dropdown')
//            ->setLinkAttribute('data-close-others', 'true')
//        ;
//
//        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
//            $menu['Tasks']->addChild('All Tasks', ['route' => 'task_index'])
//                ->setAttribute('icon', 'fa fa-suitcase')
//            ;
//        }

        return $menu;
    }
}