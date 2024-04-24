<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;


/**
 * Description of FormationTest
 *
 * @author Test1
 */
/**
 * Tests the functionality of the Formations class.
 * 
 * @coversDefaultClass \App\Entity\Formation
 */
class FormationTest extends TestCase
{
    /**
     * Tests the getDatecreationString method to ensure it returns the date in 'd/m/Y' format.
     * 
     * @covers ::getDatecreationString
     */
    public function testGetDateCreationString()
    {
        $formation = new Formation();
        $formation->setDatecreation(new \DateTime("2024-04-22"));

        // Ensure that the date is formatted correctly
        $this->assertEquals("22/04/2024", $formation->getDatecreationString());
    }
}