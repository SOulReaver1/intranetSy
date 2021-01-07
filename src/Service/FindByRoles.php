<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;

class FindByRoles {

    private $roleHierarchy;
    private $users;
    private $security;

    public function __construct(RoleHierarchyInterface $roleHierarchy, UserRepository $userRepository, Security $security){
        $this->roleHierarchy = $roleHierarchy;
        $this->users = $userRepository->findAll();
        $this->security = $security;
    }

    public function findByRole(string $role, ?User $user = null, ?bool $me = true){

        if($user){
            $rolesH = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
            if(in_array($role, $rolesH)){
                return true;
            }
        }
        $users = [];
        for ($i=0; $i < count($this->users) ; $i++) {
            if($this->users[$i] !== $this->security->getUser() || $me){
                $rolesH = $this->roleHierarchy->getReachableRoleNames($this->users[$i]->getRoles());
                if(in_array($role, $rolesH)){
                    $users[] = $this->users[$i];
                }
            }
        }

        return $users ? $users : [null];
    }

    public function findByRoles(array $roles, ?User $user = null, ?bool $me = true){
        
        if($user){
            $rolesH = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
            if(array_intersect($roles, $rolesH) == $roles){
                return true;
            }
        }

        $users = [];
          for ($i=0; $i < count($this->users) ; $i++) { 
            if($this->users[$i] !== $this->security->getUser() || $me){
                $rolesH = $this->roleHierarchy->getReachableRoleNames($this->users[$i]->getRoles());
                if(array_intersect($roles, $rolesH) == $roles){
                    $users[] = $this->users[$i];
                }
            }
        }

        return $users ? $users : [null];
    }

}