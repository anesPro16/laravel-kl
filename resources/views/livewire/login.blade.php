<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new 
#[Layout('components.layouts.empty')]       // <-- Here is the `empty` layout
#[Title('Login')]
class extends Component {
  //#[Rule('required|email')]
  #[Rule('required')]
    public string $email = '';
 
    #[Rule('required')]
    public string $password = '';
 
    public function mount()
    {
        // It is logged in
        if (auth()->user()) {
            return redirect('/');
        }
    }
 
    public function login()
    {
        $credentials = $this->validate();
 
        if (auth()->attempt($credentials)) {
            request()->session()->regenerate();
 
            //return redirect()->intended('/');
            //$this->redirect('/', navigate: true);
            //$this->redirect('/cashier', navigate: true);
            $this->redirect('/faktur', navigate: true);
        }
 
        $this->addError('email', 'email tidak benar.');
    }
}; ?>

<div class="md:w-96 mx-auto mt-20">
	<x-theme-toggle class="btn btn-circle btn-ghost" />
    {{-- <div class="mb-10">Cool image here</div> --}}
 
    <x-form wire:submit="login">
        <x-input label="E-mail" wire:model="email" icon="o-envelope" inline />
        <x-input label="Password" wire:model="password" type="password" icon="o-key" inline />
 
        <x-slot:actions>
            <x-button label="Back" class="btn-ghost" link="/" />
            <x-button label="Create an account" class="btn-ghost" link="/register" />
            <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
