@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="card" style="max-width:400px;margin:60px auto;">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', 'demo@bank.test') }}" required>
        <label>Password</label>
        <input type="password" name="password" value="password" required>
        <button class="btn" type="submit">Sign in</button>
    </form>
    <p style="color:#6b7280;font-size:.85rem">Demo: demo@bank.test / password</p>
</div>
@endsection
