<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $sAdmin = new User();
        $sAdmin->setUsername('Ilan JOURNO');
        $sAdmin->setEmail('admin@admin.com');
        $sAdmin->setRoles(['ROLE_SUPERADMIN']);
        $password = $this->encoder->encodePassword($sAdmin, 'secret');
        $sAdmin->setPassword($password);
        $manager->persist($sAdmin);
        $manager->flush();
    }
}
