<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $userPassword;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder){
        $this->userPassword=$userPasswordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
        $admin  ->setEmail("hermia44@gmail.com")
                ->setRoles(['ROLE_ADMIN'])
                // ->setPassword( this->userPassword->hashPassword(
                //     $admin,
                //     "AZE123"
                // ))

                ->setPassword( $this->userPassword->encodePassword(
                    $admin,
                    "AZE123"
                ))

                ->setIsVerified(true)
                ->setNom('Hermia');
        
            $manager->persist($admin);
        $manager->flush();
    }
}
