<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Validations;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Description of FormationsValidationsTest
 *
 * @author Test1
 */
class FormationsValidationsTest extends KernelTestCase {
    
    public function getFormations(): Formations{
        return (new Formations())
                ->setFormaions("Java");
        
    }
     public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message=""){
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
    
    public function testValidNoteFormation(){
        $this->assertErrors($this->getFormation()->setNote(10), 0, "10 devrait réussir");
        $this->assertErrors($this->getFormation()->setNote(0), 0, "0 devrait réussir");
        $this->assertErrors($this->getFormation()->setNote(20), 0, "20 devrait réussir");
    }
    
    public function testNonValidNoteVisite(){
        $this->assertErrors($this->getFormation()->setNote(21), 1, "21 devrait échouer");
        $this->assertErrors($this->getFormation()->setNote(-1), 1, "-1 devrait échouer");
        $this->assertErrors($this->getFormation()->setNote(-5), 1, "-5 devrait échouer");
        $this->assertErrors($this->getFormation()->setNote(25), 1, "25 devrait échouer");
    }

    public function testValidTempmaxVisite(){    
        $this->assertErrors($this->getFormation()->setTempmin(18)->setTempmax(20), 0, "min=18, max=20 devrait réussir");
        $this->assertErrors($this->getFormation()->setTempmin(19)->setTempmax(20), 0, "min=19, max=20 devrait réussir");
    }
    
    public function testNonValidTempmaxFormation(){
        $this->assertErrors($this->getFormation()->setTempmin(20)->setTempmax(18), 1, "min=20, max=18 devrait échouer");
        $this->assertErrors($this->getFormation()->setTempmin(20)->setTempmax(20), 1, "min=20, max=20 devrait échouer");
    }
    
    public function testValidDatecreationFormation(){ 
        $aujourdhui = new \DateTime();
        $this->assertErrors($this->getFormation()->setDatecreation($aujourdhui), 0, "aujourd'hui devrait réussir");
        $plustot = (new \DateTime())->sub(new \DateInterval("P5D"));
        $this->assertErrors($this->getFormation()->setDatecreation($plustot), 0, "plus tôt devrait réussir");
    }
    
    public function testNonValidDatecreationFormation(){ 
        $demain = (new \DateTime())->add(new \DateInterval("P1D"));
        $this->assertErrors($this->getFormation()->setDatecreation($demain), 1, "demain devrait échouer");
        $plustard = (new \DateTime())->add(new \DateInterval("P5D"));
        $this->assertErrors($this->getFormation()->setDatecreation($plustard), 1, "plus tard devrait échouer");
    }
    

}
