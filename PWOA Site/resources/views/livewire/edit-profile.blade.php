<x-slot name="header">
    <div class="d-flex align-items-center">
        <h2 class="h4 mb-0 text-dark fw-bold">
            Account Settings
        </h2>
    </div>
</x-slot>

<div class="row justify-content-center">
    <div class="col-xl-8">
        @if(session('status'))
            <div class="alert alert-success border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-3 fs-3"></i>
                <div class="fw-bold">{{ session('status') }}</div>
            </div>
        @endif

        <div class="card border-0 glass-card">
            <div class="card-body p-4 p-md-5">
                <div class="section-title mb-5">
                    <h5 class="fw-bold mb-1 text-dark">Personal Information</h5>
                    <p class="text-muted small">Update your official contact and location details.</p>
                    <hr class="mt-2 opacity-10">
                </div>

                <form wire:submit.prevent="updateProfile">
                    <div class="mb-4">
                        <label for="name" class="form-label-premium">Full Name</label>
                        <input type="text" id="name" wire:model="name" class="form-control-premium @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="phone" class="form-label-premium">Phone Number</label>
                            <input type="text" id="phone" wire:model="phone" class="form-control-premium @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label-premium">Date of Birth</label>
                            <input type="date" id="date_of_birth" wire:model="date_of_birth" class="form-control-premium @error('date_of_birth') is-invalid @enderror">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label-premium">Residential Address</label>
                        <input type="text" id="address" wire:model="address" class="form-control-premium @error('address') is-invalid @enderror" placeholder="123 Luxury Ave">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="country_id" class="form-label-premium">Country</label>
                            <div wire:ignore>
                                <select id="country_id" x-data="{
                                        init() {
                                            let el = $(this.$el);
                                            el.select2({
                                                width: '100%',
                                                placeholder: 'Select Country'
                                            }).on('change', (e) => {
                                                $wire.set('country_id', e.target.value);
                                            });
                                            this.$watch('$wire.country_id', val => {
                                                if(el.val() != val) el.val(val).trigger('change.select2');
                                            });
                                            if($wire.country_id) el.val($wire.country_id).trigger('change.select2');
                                        }
                                    }"
                                    class="form-select-premium">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ $country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('country_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="zip" class="form-label-premium">ZIP Code</label>
                            <input type="text" id="zip" wire:model="zip" class="form-control-premium @error('zip') is-invalid @enderror">
                            @error('zip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label for="state_id" class="form-label-premium">State</label>
                            <div wire:ignore>
                                <select id="state_id" x-data="{
                                        init() {
                                            let el = $(this.$el);
                                            el.select2({
                                                width: '100%',
                                                placeholder: 'Select State'
                                            }).on('change', (e) => {
                                                $wire.set('state_id', e.target.value);
                                            });
                                            this.$watch('$wire.state_id', val => {
                                                if(el.val() != val) el.val(val).trigger('change.select2');
                                            });
                                            this.$watch('$wire.states', options => {
                                                const opts = Array.isArray(options) ? options : Object.values(options);
                                                el.prop('disabled', opts.length === 0);
                                                let html = '<option value=\'\'>Select State</option>';
                                                opts.forEach(opt => {
                                                    html += '<option value=\'' + opt.id + '\'>' + opt.name + '</option>';
                                                });
                                                el.html(html).trigger('change.select2');
                                            });
                                            if($wire.state_id) el.val($wire.state_id).trigger('change.select2');
                                        }
                                    }"
                                    class="form-select-premium">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state['id'] }}" {{ $state_id == $state['id'] ? 'selected' : '' }}>{{ $state['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('state_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="city_id" class="form-label-premium">City</label>
                            <div wire:ignore>
                                <select id="city_id" x-data="{
                                        init() {
                                            let el = $(this.$el);
                                            el.select2({
                                                width: '100%',
                                                placeholder: 'Select City'
                                            }).on('change', (e) => {
                                                $wire.set('city_id', e.target.value);
                                            });
                                            this.$watch('$wire.city_id', val => {
                                                if(el.val() != val) el.val(val).trigger('change.select2');
                                            });
                                            this.$watch('$wire.cities', options => {
                                                const opts = Array.isArray(options) ? options : Object.values(options);
                                                el.prop('disabled', opts.length === 0);
                                                let html = '<option value=\'\'>Select City</option>';
                                                opts.forEach(opt => {
                                                    html += '<option value=\'' + opt.id + '\'>' + opt.name + '</option>';
                                                });
                                                el.html(html).trigger('change.select2');
                                            });
                                            if($wire.city_id) el.val($wire.city_id).trigger('change.select2');
                                        }
                                    }"
                                    class="form-select-premium">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city['id'] }}" {{ $city_id == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('city_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-top d-flex justify-content-end align-items-center gap-3">
                        <span wire:loading wire:target="updateProfile" class="text-muted small">
                            <span class="spinner-border spinner-border-sm me-2"></span> Updating...
                        </span>
                        <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

