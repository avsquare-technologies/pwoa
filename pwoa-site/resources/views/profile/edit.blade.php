<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold text-dark mb-0">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container d-flex flex-column gap-4">
            <div class="p-4 p-md-5 bg-white shadow-sm rounded-3">
                <div class="col-md-8 col-lg-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 p-md-5 bg-white shadow-sm rounded-3">
                <div class="col-md-8 col-lg-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 p-md-5 bg-white shadow-sm rounded-3">
                <div class="col-md-8 col-lg-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
