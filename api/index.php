<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<?php

class player {
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}

class faction {
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}

class picker {
    private $factions = [];
    private $players = [];

    public function __construct($picks = [])
    {
        if (!empty($picks)) {
            foreach ($picks as $key => $pick) {
                if ('firstPlayer' !== $key) {
                    $this->players[] = new player($pick['player']['name']);
                }
            }
        }
    }

    public function getImage($faction)
    {
        return '<img src="/factions/' . str_replace(' ', '_', $faction) . '.png" width="100" height="100" title="' . $faction . '"/><br/>';
    }

    public function doPick()
    {
        $picks = [];
        foreach ($this->players as $player) {
            $pick = [
                'player' => $player,
                'faction' => [],
            ];
            for ($i = 0; $i < 3; $i++) {
                $pick['faction'][] = $this->pickRandomFaction();
            }
            $picks[] = $pick;
        }
        $picks['firstPlayer'] = $this->pickFirstPlayer();

        return $picks;
    }

    private function pickFirstPlayer()
    {
        return $this->players[array_rand($this->players, 1)]->name;
    }

    public function displayResults($picks)
    {
        $firstPlayer = $picks['firstPlayer'];
        $url = 'https://smash-up.vercel.app/?seed=' . base64_encode(json_encode($picks));

        echo '<br/>
<br/>
<br/>
<div class="container">
    <div class="jumbotron">

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Player</th>
                <th colspan="3"  style="text-align: center;">Factions</th>
            </tr>
            </thead>
            <tbody>';
        foreach ($picks as $key => $pick) {
            if ('firstPlayer' !== $key) {
                echo '<tr><td class="align-middle" style="' . (($firstPlayer === $pick['player']['name']) ? "color: red;" : "") . '">' . $pick['player']['name'] . '</td>';
                foreach ($pick['faction'] as $faction) {
                    echo '<td>' . $this->getImage($faction['name']) . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</tbody>
        </table>
        <br/>
        <center><span><a href="' . $url . '">Share seed.</a></span></center>
    </div>
</div>';
    }

    private function pickRandomFaction()
    {
        $factionIndex = array_rand($this->factions, 1);
        $faction = $this->factions[$factionIndex];
        unset($this->factions[$factionIndex]);
        return $faction;
    }

    /**
     * @return array
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    /**
     * @param array $factions
     */
    public function setFactions(array $factions): void
    {
        $this->factions = $factions;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }
}

$players = [];
if (isset($_GET['seed'])) {
    $seed = $_GET['seed'];
    $picks = json_decode(base64_decode($seed), true);
    $picker = new picker($picks);
} else {
    if (isset($_GET['players'])) {
        $players = $_GET['players'];
        $players = explode(',', $players);
    } elseif (!isset($_GET['numberOfPlayers']) || ((int)$counter = $_GET['numberOfPlayers'])) {
        if (!isset($_GET['numberOfPlayers'])) {
            $counter = 4;
        }
        $counter = abs($counter);
        for ($i=0; $i < $counter; $i++) {
            $players[] = 'Player ' . ($i + 1);
        }
    }

    foreach ($players as $player) {
        $instancedPlayers[] = new player($player);
    }

    $picker = new picker();
    $picker->setFactions([
        new faction('Pirates'),
        new faction('Ninjas'),
        new faction('Zombies'),
        new faction('Robots'),
        new faction('Dinosaurs'),
        new faction('Wizards'),
        new faction('Tricksters'),
        new faction('Aliens'),
        new faction('Killer Plants'),
        new faction('Ghosts'),
        new faction('Steampunks'),
        new faction('Bear Cavalry'),
        new faction('Minions of Cthulhu'),
        new faction('Elder Things'),
        new faction('Innsmouth'),
        new faction('Miskatonic University'),
        new faction('Time Travelers'),
        new faction('Cyborg Apes'),
        new faction('Super Spies'),
        new faction('Shapeshifters'),
        new faction('Geeks'),
        new faction('Vampires'),
        new faction('Mad Scientists'),
        new faction('Giant Ants'),
        new faction('Werewolves'),
        new faction('Fairies'),
        new faction('Mythic Horses'),
        new faction('Kitty Cats'),
        new faction('Princesses'),
        new faction('Sharks'),
        new faction('Superheroes'),
        new faction('Mythic Greeks'),
        new faction('Dragons'),
        new faction('Sheeps'),
        new faction('Star Roamers'),
        new faction('Astro Knights'),
        new faction('Changerbots'),
        new faction('Ignobles'),
        new faction('Teddy Bears'),
        new faction('Grandmas'),
        new faction('Rock Stars'),
        new faction('Explorers'),
        new faction('Truckers'),
        new faction('Disco Dancers'),
        new faction('Vigilantes'),
        new faction('Kung Fu Fighters'),
        new faction('Itty Critters'),
        new faction('Kaiju'),
        new faction('Magical Girls'),
        new faction('Mega Troopers'),
    ]);

    $picker->setPlayers($instancedPlayers);

    $picks = $picker->doPick();
}

$picker->displayResults(json_decode(json_encode($picks), true));
