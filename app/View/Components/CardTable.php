<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardTable extends Component
{
    public $project;
    public $title;

    // Params pass to table component
    public $query;
    public $columns;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($project, $title)
    {
        $this->project = $project;
        $this->title =  $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.card-table');
    }
}
