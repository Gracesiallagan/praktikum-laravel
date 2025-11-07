<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <form wire:submit.prevent="login" class="w-100" style="max-width: 400px;">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="/logo.png" alt="Logo" style="width:80px;">
                    <h3 class="mt-2">Masuk</h3>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" wire:model.defer="email">
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label>Kata Sandi</label>
                    <input type="password" class="form-control" wire:model.defer="password">
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>

                <div class="text-center mt-3">
                    Belum punya akun? <a href="{{ route('auth.register') }}">Daftar</a>
                </div>
            </div>
        </div>
    </form>
</div>
