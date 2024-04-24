<?php
namespace App\Tests\Repository;

use App\Entity\Visite;
use App\Repository\VisiteRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of VisiteRepositoryTest
 *
 * @author emds
 */
class FormationRepositoryTest extends KernelTestCase {
    
    /**
     * Récupère le repository de Visite
     * @return VisiteRepository
     */
    public function recupRepository(): VisiteRepository{
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }
    
    /**
     * Création d'une instance de Visite avec ville, pays et dateCreation
     * @return Visite
     */
    public function newFormation(): Formation{
        $formation = (new Formation())
                ->setDatecreation(new DateTime("now"));
        return $formation;
    }
    
    public function testNbFormations(){
        $repository = $this->recupRepository();
        $nbFormations = $repository->count([]);
        $this->assertEquals(2, $nbFormations);
    }
    
    public function testAddFormation(){
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $nbFormation = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    public function testRemoveFormation(){
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $nbFormation = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "erreur lors de la suppression");        
    }
    
    public function testFindByEqualValue(){
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findByEqualValue();
        $nbVisites = count($formations);
        $this->assertEquals(1, $nbFormations);
        $this->assertEquals("New York", $formations[0]->getCategories());
    }
}
