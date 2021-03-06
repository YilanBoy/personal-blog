@extends('layouts.app')

@section('title', '驗證信箱')

@section('content')
    <div class="container mx-auto max-w-7xl py-6">
        <div class="flex justify-center items-center px-4 xl:px-0">

            <div class="w-full flex flex-col justify-center items-center">

                {{-- Title --}}
                <div class="fill-current text-gray-700 text-2xl dark:text-white">
                    <i class="bi bi-person-check-fill"></i><span class="ml-4">驗證 Email</span>
                </div>

                <x-card class="w-full sm:max-w-md mt-4 overflow-hidden">
                    <div class="mb-4 text-gray-600 dark:text-white">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-green-600">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf

                            <div>
                                <x-button>
                                    {{ __('Resend Verification Email') }}
                                </x-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </x-card>

            </div>
        </div>
    </div>
@endsection
