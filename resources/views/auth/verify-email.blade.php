<x-guest-layout title="Verifikasi Email">
    <header class="auth-head">
        <h1>Verifikasi Email</h1>
        <p>Klik tautan yang kami kirim ke email Anda untuk mengaktifkan akun. Belum menerimanya? Kirim ulang di bawah.</p>
    </header>

    @if (session('status') == 'verification-link-sent')
        <div class="form-status form-status--success">
            Tautan verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-primary-button class="btn--block">Kirim Ulang Email Verifikasi</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="auth-foot">
        @csrf
        <button type="submit" class="auth-link">Keluar</button>
    </form>
</x-guest-layout>
