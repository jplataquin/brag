@extends('layouts.app')

@section('title', 'Battle Room #' . $battle->id)

@section('content')
<div class="battle-room-container py-3">
    <livewire:battle-room :battle="$battle" />
</div>

<style>
    .battle-room-container {
        max-width: 1400px;
        margin: 0 auto;
    }
</style>
@endsection
