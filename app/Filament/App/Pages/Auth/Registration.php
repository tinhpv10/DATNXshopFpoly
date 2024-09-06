<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Registration extends Register
{
    protected ?string $maxWidth = '2xl';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Tài khoản')
                        ->schema([
                            $this->getNameFormComponent()->label('Tên cửa hàng'),
                            $this->getEmailFormComponent(),
                        ]),
                    Wizard\Step::make('Thông tin')
                        ->schema([
                            $this->getPhoneFormComponent(),
                            $this->getTwitterFormComponent(),
                        ]),
                    Wizard\Step::make('Mật khẩu')
                        ->schema([
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                        wire:submit="register"
                    >
                        Hoàn Thành
                    </x-filament::button>
                    BLADE
                ))),
            ]);
    }


    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->prefix('+84')
            ->label(__('Số điện thoại'))
            ->unique('users', 'phone')
            ->validationMessages([
                'unique' => 'Số điện thoại này đã được tạo.',
            ])
            ->minLength(10)
            ->maxLength(10);
    }

    protected function getTwitterFormComponent(): Component
    {
        return Textarea::make('description')
            ->label(__('Mô tả ngành hàng'))
            ->maxLength(255);
    }
}
