<?php

namespace App\View\Components;

use Illuminate\View\Component;

class staf_expiry_row extends Component
{
    public $id,$name,$type,$date,$number;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id,$name,$type,$date,$number)
    {
        $this->id=$id;
        $this->name=$name;
        $this->type=$type;
        $this->date=$date;
        $this->number=$number;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.staf_expiry_row');
    }
}
