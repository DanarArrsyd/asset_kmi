<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * @param  string|null  $title  Page title. Defaults to the login screen's,
     *                              since every other guest page is that one.
     * @param  bool  $split  Brand rail beside the form. On for the auth screens;
     *                       off for the QR landing page, which is a card someone
     *                       reads on a phone and has no sign-in to frame.
     */
    public function __construct(
        public ?string $title = null,
        public bool $split = true,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
