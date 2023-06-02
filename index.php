<?php

abstract class Personnage {
    protected $nom;
    protected $pointsDeVie;
    protected $attaqueMin;
    protected $attaqueMax;
    protected $defense;

    public function __construct($nom, $pointsDeVie, $attaqueMin, $attaqueMax, $defense) {
        $this->nom = $nom;
        $this->pointsDeVie = $pointsDeVie;
        $this->attaqueMin = $attaqueMin;
        $this->attaqueMax = $attaqueMax;
        $this->defense = $defense;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPointsDeVie() {
        return $this->pointsDeVie;
    }

    public function attaquer(Personnage $adversaire) {
        $attaque = rand($this->attaqueMin, $this->attaqueMax);
        $adversaire->subirDegats($attaque);
    }

    protected function subirDegats($degats) {
        $degats -= $this->defense;

        if ($degats > 0) {
            $this->pointsDeVie -= $degats;
        }

        if ($this->pointsDeVie < 0) {
            $this->pointsDeVie = 0;
        }
    }

    abstract protected function verifierVictoire($adversaire);

    abstract public function afficherInfos();
}

class Guerrier extends Personnage {
    public function __construct($nom) {
        parent::__construct($nom, 100, 20, 40, rand(10, 19));
    }

    protected function verifierVictoire($adversaire) {
        echo "Vous avez gagné !";
        session_unset();
    }

    public function afficherInfos() {
        echo "Guerrier: " . $this->nom . "<br>";
        echo "Points de vie: " . $this->pointsDeVie . "<br>";
        echo "Attaque: " . $this->attaqueMin . " - " . $this->attaqueMax . "<br>";
        echo "Défense: " . $this->defense . "<br>";
    }
}

class Magicien extends Personnage {
    private $peutEndormir;
    private $cooldown;
    private $endormi;

    public function __construct($nom) {
        parent::__construct($nom, 100, 5, 10, 0);
        $this->peutEndormir = true;
        $this->cooldown = 0;
        $this->endormi = false;
    }

    protected function verifierVictoire($adversaire) {
        echo "Vous avez gagné !";
        session_unset();
    }

    public function afficherInfos() {
        echo "Magicien: " . $this->nom . "<br>";
        echo "Points de vie: " . $this->pointsDeVie . "<br>";
        echo "Attaque: " . $this->attaqueMin . " - " . $this->attaqueMax . "<br>";
        echo "Défense: " . $this->defense . "<br>";
        echo "Peut endormir: " . ($this->peutEndormir ? "Oui" : "Non") . "<br>";
        echo "Cooldown: " . $this->cooldown . "<br>";
        echo "Endormi: " . ($this->endormi ? "Oui" : "Non") . "<br>";
    }

    public function attaquer(Personnage $adversaire) {
        parent::attaquer($adversaire);
        $this->verifierVictoire($adversaire);
    }

    public function endormir(Personnage $adversaire) {
        if ($this->peutEndormir && !$this->endormi && $this->cooldown <= 0) {
            $adversaire->etreEndormi();
            $this->cooldown = 120; // 2 minutes en secondes
        }
    }

    protected function subirDegats($degats) {
        if (!$this->endormi) {
            parent::subirDegats($degats);
        }
    }

    private function etreEndormi() {
        $this->endormi = true;
        $this->cooldown = 15;
    }

    public function updateCooldown() {
        if ($this->cooldown > 0) {
            $this->cooldown--;
        }

        if ($this->endormi && $this->cooldown <= 0) {
            $this->endormi = false;
        }
    }
}

// Code du jeu
session_start();

if (isset($_POST['nom']) && isset($_POST['type'])) {
    $nom = $_POST['nom'];
    $type = $_POST['type'];

    if ($type === 'guerrier') {
        $_SESSION['personnage'] = new Guerrier($nom);
        $_SESSION['adversaire'] = new Magicien('Ennemi');
    } else {
        $_SESSION['personnage'] = new Magicien($nom);
        $_SESSION['adversaire'] = new Guerrier('Ennemi');
    }
}

if (isset($_SESSION['personnage']) && isset($_SESSION['adversaire'])) {
    $personnage = $_SESSION['personnage'];
    $adversaire = $_SESSION['adversaire'];

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'attaquer') {
            $personnage->attaquer($adversaire);
            $adversaire->attaquer($personnage);
        }
    }

    if ($personnage->getPointsDeVie() <= 0) {
        echo "Vous avez perdu !";
        session_unset();
    } elseif ($adversaire->getPointsDeVie() <= 0) {
        echo "Vous avez gagné !";
        session_unset();
    } else {
        echo "<h1>Combat</h1>";
        $personnage->afficherInfos();
        echo "<br>";
        $adversaire->afficherInfos();

        echo "<form method='post'>";
        echo "<input type='submit' name='action' value='attaquer'>";
        echo "</form>";
    }
} else {
    echo "<h1>Création du personnage</h1>";
    echo "<form method='post'>";
    echo "Nom: <input type='text' name='nom'><br>";
    echo "Type: <select name='type'>";
    echo "<option value='guerrier'>Guerrier</option>";
    echo "<option value='magicien'>Magicien</option>";
    echo "</select><br>";
    echo "<input type='submit' value='Commencer'>";
    echo "</form>";
}

?>




























