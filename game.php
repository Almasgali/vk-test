<?php

include "player.php";

/**
 * Main class with all game logic.
 */
class Game {

    private $players_number;
    private $players = [];
    private $results = [];
    private $completed = [];
    private $number_of_completed;
    private $need_find_min;

    /**
     * Initialize the game.
     * 
     * Reading number of players and their names from console.
     * Finding minimal distances between start and end pages of every player
     * may be very long, so it's possible to disable this feature.
     * 
     * @param bool $need_find_min Minimal distance between
     * start and end pages of every player will be found
     * if and only this variable is true.
     */
    public function __construct(bool $need_find_min) {
        $this->need_find_min = $need_find_min;
        $this->number_of_completed = 0;
        while (true) {
            $this->players_number = readline("Enter the number of players: ");
            if (is_numeric($this->players_number)) {
                break;
            }
            print "You should enter a correct number." . PHP_EOL;
        }
        for ($i = 1; $i <= $this->players_number; ++$i) {
            $this->players[] = new Player(readline("Enter your name, Player №$i: "));
            $this->completed[] = false;
            $this->results[] = 0;
        }
    }

    /**
     * Starts the game.
     * 
     * Game will be over when all players will reach their destination web page,
     * or it can be interrupted if somebody will type 'END' in the console.
     * At the end of the game you can see result of every player
     * (and minimal amount of steps he could make to reach his goal,
     * if this feature is enabled).
     */
    public function play() {
        while ($this->number_of_completed < $this->players_number) {
            for ($i = 0; $i < $this->players_number; ++$i) {
                if ($this->completed[$i] === true) {
                    continue;
                }
                $current_player_page = $this->players[$i]->get_current_page();
                $current_player_destination = $this->players[$i]->get_end_page();
                $current_player_name = $this->players[$i]->get_name();
                print "$current_player_name's turn." . PHP_EOL . 
                "Your current page: $current_player_page." . PHP_EOL .
                "Your destination page: $current_player_destination." . PHP_EOL;
                print "Choose the page you are going to or type 'END' if you want to end the game." . PHP_EOL . PHP_EOL;
                $links = Util::extract_links($current_player_page);
                $links_count = count($links);
                for ($j = 1; $j <= $links_count; ++$j) {
                    print "$j " . $links[$j - 1] . PHP_EOL;
                }
                while (true) {
                    $input = readline("Your choice: ");
                    if ($input === "END") {
                        print "Game was interrupted by $current_player_name." . PHP_EOL;
                        return;
                    }
                    if (is_numeric($input)) {
                        $num = intval($input);
                        if ($num >= 1 && $num <= $links_count) {
                            $to = $links[$num - 1];
                            ++$this->results[$i];
                            if ($to === $current_player_destination) {
                                ++$this->number_of_completed;
                                $this->completed[$i] = true;
                            } else {
                                $this->players[$i]->set_current_page($to);
                            }
                            break;
                        }
                    }
                    print "You should enter a number between 1 and $links_count or END." . PHP_EOL;
                }
            }
        }
        $this->show_result();
    }

    private function show_result() {
        for ($i = 0; $i < $this->players_number; ++$i) {
            $current_player_name = $this->players[$i]->get_name();
            print "$current_player_name has finished in " . 
            $this->results[$i] . " moves.";
            if ($this->need_find_min) {
                print("The minimum amount of moves was " .
                Util::calculate_min_distance(
                    $this->players[$i]->get_start_page(), 
                    $this->players[$i]->get_end_page()));
            }
            print PHP_EOL;
        }
    }
}