@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="guest-card">
        <header class="guest-card-head"><div class="guest-brand"><i class="fas fa-futbol"></i></div><h1 class="h4 fw-bold mb-2">{{ config('app.name','Futsal Booking') }}</h1><p class="text-muted mb-0">Masuk untuk mengelola operasional dan transaksi.</p></header>
        <div class="card-body">
            @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('login') }}">@csrf
                <div class="mb-3"><label for="email" class="form-label">Alamat email</label><div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="far fa-envelope text-muted"></i></span><input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@contoh.com">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="mb-3"><label for="password" class="form-label">Kata sandi</label><div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span><input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi"><button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Tampilkan kata sandi"><i class="far fa-eye"></i></button>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="d-flex justify-content-between align-items-center mb-4"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')?'checked':'' }}><span class="form-check-label small" for="remember">Ingat saya</span></label><a href="{{ route('password.request') }}" class="small text-success text-decoration-none fw-semibold">Lupa kata sandi?</a></div>
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-right-to-bracket me-2"></i>Masuk ke dashboard</button>
            </form>
        </div><footer class="text-center text-muted small pb-4">Akses khusus staf dan administrator</footer>
    </section>
@endsection

@push('scripts')
<script>document.getElementById('togglePassword')?.addEventListener('click',function(){const input=document.getElementById('password'),icon=this.querySelector('i'),show=input.type==='password';input.type=show?'text':'password';icon.classList.toggle('fa-eye',!show);icon.classList.toggle('fa-eye-slash',show);this.setAttribute('aria-label',show?'Sembunyikan kata sandi':'Tampilkan kata sandi')});</script>
@endpush
