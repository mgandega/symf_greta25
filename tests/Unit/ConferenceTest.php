<?php

namespace App\Tests\Unit;

use DateTimeInterface;
use App\Entity\Conference;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConferenceTest extends KernelTestCase
{
    public function getEntity(){
        $conference = new Conference();
        $conference->setTitre('Test test');
        $conference->setDescription('sdfghjkl dfghjklm dfghjkl dfghjk');
        $conference->setLieu('Test sdfghjk');
        $conference->setPrix(123);
        $conference->setDate(new \DateTime('now'));
        return $conference;
    }
    public function testIfConferenceIsValid(): void
    {
        $kernel = self::bootKernel();
        $conference = $this->getEntity();
        $this->assertSame('Test test', $conference->getTitre());
        $this->assertSame('sdfghjkl dfghjklm dfghjkl dfghjk', $conference->getDescription());
        $this->assertSame('Test sdfghjk', $conference->getLieu());
        $this->assertSame(123, intVal($conference->getPrix()));
        $this->assertInstanceOf(\DateTimeInterface::class, $conference->getDate());
        // ajouter le service validator
        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($conference);
        $this->assertEquals(0, count($errors));

    }
    public function testIfConferenceIsInvalid(){
        $kernel = self::bootKernel();
        $conference = $this->getEntity();
        $conference->setTitre('');
        $conference->setDescription('');
        $conference->setLieu('');
        $conference->setPrix(-123);
        $conference->setDate(new \DateTimeImmutable('now'));
        // ajouter le service validator
         $this->assertNotSame('Test test', $conference->getTitre());
        $this->assertNotSame('sdfghjkl dfghjklm dfghjkl dfghjk', $conference->getDescription());
        $this->assertNotSame('Test sdfghjk', $conference->getLieu());
        $this->assertNotSame(123, intVal($conference->getPrix()));
        $this->assertNotInstanceOf(\DateTime::class, $conference->getDate());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($conference);
        $this->assertEquals(2, count($errors));
    }
}
