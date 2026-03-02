<?php

namespace App\Http\Controllers\Filament;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use MarcoGermani87\FilamentCaptcha\Forms\Components\CaptchaField;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                CaptchaField::make('captcha')
                    ->required()
                    ->validationAttribute('CAPTCHA'),
            ])
            ->statePath('data');
    }
    
}
