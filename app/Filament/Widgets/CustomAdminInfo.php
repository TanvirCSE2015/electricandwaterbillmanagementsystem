<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CustomAdminInfo extends Widget
{
    protected static bool $isLazy=false;
    protected static ?int $sort=-1;
    protected string $view = 'filament.widgets.custom-admin-info';
}
