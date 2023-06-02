<?php 

class Personnage {
    protected $nom;
    protected $typePersonnage;
    protected $pointsDeVie;
    protected $attaqueMin;
    protected $attaqueMax;
    protected $defenseMin;
    protected $defenseMax;
    protected $endormi;
    protected $tpsEndormissement;

    public function __construct($nom, $typePersonnage) {
        $this->nom = $nom;
        $this->typePersonnage = $typePersonnage;
        $this->pointsDeVie = 100;

        if ($typePersonnage == "guerrier") {
            $this->attaqueMin = 20;
            $this->attaqueMax = 40;
            $this->defenseMin = 10;
            $this->defenseMax = 19;
        } elseif ($typePersonnage == "magicien") {
            $this->attaqueMin = 5;
            $this->attaqueMax = 10;
            $this->defenseMin = 0;
            $this->defenseMax = 0;
            $this->endormi = false;
            $this->tpsEndormissement = time() - 120;
        }
    }
}