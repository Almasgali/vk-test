<?php

include "util.php";

/**
 * Player class to store information about players.
 */
class Player {
    private string $name;
    private string $current_page;
    private string $start_page;
    private string $end_page;

    /**
     * Initialize new player.
     * 
     * New player get his random start and end random Wikipedia pages.
     * They are always different.
     * 
     * @param string $name Name of the player.
     */
    public function __construct(string $name) {
        $this->name = $name;
        $this->current_page = Util::get_random_link();
        $this->start_page = $this->current_page;
        $this->end_page = Util::get_random_link();
        while ($this->end_page === $this->current_page) {
            $this->end_page = Util::get_random_link();
        }
    }

    public function get_current_page() {
        return $this->current_page;
    }

    public function set_current_page(string $page) {
        $this->current_page = $page;
    }

    public function get_start_page() {
        return $this->start_page;
    }

    public function get_end_page() {
        return $this->end_page;
    }

    public function get_name() {
        return $this->name;
    }
}