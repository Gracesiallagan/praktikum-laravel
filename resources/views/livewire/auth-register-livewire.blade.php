<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="text-center mb-3">Daftar Akun</h4>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="register" novalidate>
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" wire:model.defer="name" class="form-control">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" wire:model.defer="email" class="form-control">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" wire:model.defer="password" class="form-control">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" wire:model.defer="password_confirmation" class="form-control">
                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>

        <p class="text-center mt-3">
            Sudah punya akun? <a href="{{ route('auth.login') }}">Login</a>
        </p>
    </div>
</div>
