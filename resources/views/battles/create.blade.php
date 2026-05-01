@extends('layouts.app')

@section('title', 'Create Battle')

@section('content')
<div class="container py-4">
    <livewire:create-battle-form :gameTitleId="$preselectedGameId" :selectedCardId="$preselectedCardId" />
</div>
@endsection
