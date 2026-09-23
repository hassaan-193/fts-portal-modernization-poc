<?php
// app/View/Components/CardLetters.php

namespace App\View\Components;

use Illuminate\View\Component;

class CardLetters extends Component
{
    public $profile;
    public $type;

    public function __construct($profile, $type)
    {
        $this->profile = $profile;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.card-letters');
    }
}
