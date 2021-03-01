<?php

namespace App\DataFixtures;

use App\Entity\SmsAuto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SmsAutoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // First step
        $step1 = new SmsAuto();
        $step1->setStep(1);
        $step1->setContent("Contenu de la première étape");
        $manager->persist($step1);
        // Step 2
        $step2 = new SmsAuto();
        $step2->setStep(2);
        $step2->setContent("Contenu de la seconde étape");
        $manager->persist($step2);
        // Step 3
        $step3 = new SmsAuto();
        $step3->setStep(3);
        $step3->setContent("Contenu de la troisième étape");
        $manager->persist($step3);
        // Step 4
        $step4 = new SmsAuto();
        $step4->setStep(4);
        $step4->setContent("Contenu de la quatrième étape");
        $manager->persist($step4);
        $manager->flush();
    }
}
