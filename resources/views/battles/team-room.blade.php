@extends('layouts.app')

@section('title', 'Team Battle Room')

@section('content')
<livewire:team-battle-room :teamBattle="$teamBattle" />
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrcodeContainer = document.getElementById('qrcode');
        if (qrcodeContainer) {
            new QRCode(qrcodeContainer, {
                text: "{{ route('team-battles.room', $teamBattle) }}",
                width: 200,
                height: 200,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
    });

    function copyBattleUrl() {
        const copyText = document.getElementById("battle-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "COPIED!";
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    }
</script>
@endsection
