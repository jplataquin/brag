@props([
    'id' => 'card_canvas_' . uniqid(),
    'mode' => 'default', // 'default', 'template', 'thumbnail', 'display'
    'fullscreen' => false,
    'rarity' => 'common',
    'detailUrl' => '',
    'width' => 350,
    'height' => 490,
    'title' => 'CARD TITLE',
    'game' => 'GAME',
    'creator' => 'Creator',
    'isCreatorVerified' => false,
    'isCreatorUntrustworthy' => false,
    'quote' => 'Card quote goes here...',
    'image' => '',
    'imagePositionX' => 50,
    'imagePositionY' => 50,
    'imageScale' => 1.0,
    'imageStretchY' => 1.0,
    'backgroundColor' => '#0a0a1a',
    'borderColor' => '#00f0ff',
    'sectionColor' => '#111122',
    'primaryTextColor' => '#ffffff',
    'secondaryTextColor' => '#dddddd',
    'wins' => 0,
    'losses' => 0,
    'integrityStat' => 0,
    'eloScore' => 1000,
    'lifePoints' => 3,
    'status' => 'Maintained',
    'rankLevel' => 1,
    'serialNumber' => null,
    'year' => null,
    'asThumbnail' => false,
    'linkUrl' => null,
    'burned' => false,
    'cardId' => null,
    'ownerId' => null,
    'isCensored' => false
])

@php
$asThumbnail = filter_var($asThumbnail, FILTER_VALIDATE_BOOLEAN);
$burned = filter_var($burned, FILTER_VALIDATE_BOOLEAN);
$isCensored = filter_var($isCensored, FILTER_VALIDATE_BOOLEAN);
if (!$year) {
    $year = date('Y');
}
if ($mode === 'template') {
    $backgroundColor = '#1a1800';
    $borderColor = '#ffdd00';
    $sectionColor = '#2b2400';
    $primaryTextColor = '#ffdd00';
    $secondaryTextColor = '#cca800';
}
$hasFullscreen = $mode === 'thumbnail' || filter_var($fullscreen, FILTER_VALIDATE_BOOLEAN);
if ($linkUrl) $hasFullscreen = false;
$badgeVersion = file_exists(public_path("img/badge/lv{$rankLevel}.webp")) ? filemtime(public_path("img/badge/lv{$rankLevel}.webp")) : time();

// Rarity Color & Icon
$rarityColors = [
    'ultra-rare' => '#ff0000',
    'rare' => '#ff00ff',
    'common' => '#39ff14',
    'template' => '#ffdd00',
];
$rarityIcons = [
    'ultra-rare' => '🐦‍🔥',
    'rare' => '🦄',
    'common' => '🪵',
    'template' => '📜',
];
$computedRarityColor = $rarityColors[$mode === 'template' ? 'template' : $rarity] ?? '#ffffff';
$computedRarityIcon = $rarityIcons[$mode === 'template' ? 'template' : $rarity] ?? '🪵';
$winRate = ($wins + $losses > 0) ? round(($wins / ($wins + $losses)) * 100) : 0;

$nextLevelProgress = 100;
$showProgressBar = false;
if ($mode !== 'template' && $rankLevel < 5) {
    $showProgressBar = true;
    $levelConditions = config('leveling.conditions', []);
    $nextConditions = $levelConditions[$rankLevel + 1] ?? null;
    if ($nextConditions) {
        $minWins = $nextConditions['min_wins'];
        $minWinRate = $nextConditions['min_win_rate'];
        $minIntegrity = $nextConditions['min_integrity'] ?? 0;

        $winsProgress = $minWins > 0 ? min(100, ($wins / $minWins) * 100) : 100;
        $winRateProgress = $minWinRate > 0 ? min(100, ($winRate / $minWinRate) * 100) : 100;
        $integrityProgress = $minIntegrity > 0 ? min(100, ($integrityStat / $minIntegrity) * 100) : 100;

        $nextLevelProgress = round(($winsProgress + $winRateProgress + $integrityProgress) / 3);
    }
}

$cardOptionsJson = json_encode([
    'mode' => $mode,
    'title' => $title,
    'game' => $game,
    'creator' => $creator,
    'quote' => $quote,
    'image' => $image,
    'imagePositionX' => $imagePositionX ?? 50,
    'imagePositionY' => $imagePositionY ?? 50,
    'imageScale' => $imageScale ?? 1.0,
    'imageStretchY' => $imageStretchY ?? 1.0,
    'isCreatorVerified' => $isCreatorVerified,
    'isCreatorUntrustworthy' => $isCreatorUntrustworthy,
    'backgroundColor' => $backgroundColor,
    'borderColor' => $borderColor,
    'sectionColor' => $sectionColor,
    'primaryTextColor' => $primaryTextColor,
    'secondaryTextColor' => $secondaryTextColor,
    'wins' => $wins,
    'losses' => $losses,
    'winRate' => $winRate,
    'integrityStat' => $integrityStat,
    'eloScore' => $eloScore,
    'lifePoints' => $lifePoints,
    'status' => $status,
    'rarityColor' => $computedRarityColor,
    'rarityIcon' => $computedRarityIcon,
    'rankLevel' => $rankLevel,
    'badgeVersion' => $badgeVersion,
    'badgeBaseUrl' => asset('img/badge'),
    'serialNumber' => $serialNumber !== null ? $serialNumber : null,
    'year' => $year,
    'asThumbnail' => $asThumbnail ? true : false,
    'burned' => $burned,
    'isCensored' => $isCensored
]);
@endphp

@php
$placeholderSvg = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}'%3E%3Crect width='100%25' height='100%25' fill='" . urlencode($backgroundColor) . "' rx='10' ry='10' /%3E%3Ctext x='50%25' y='50%25' font-family='sans-serif' font-size='20' fill='" . urlencode($secondaryTextColor) . "' text-anchor='middle' dominant-baseline='middle'%3ELOADING...%3C/text%3E%3C/svg%3E";
@endphp

@if($mode === 'thumbnail' || $asThumbnail)
    @if($linkUrl)
    <a href="{{ $linkUrl }}" id="wrapper_{{ $id }}" style="cursor: pointer; transition: transform 0.2s; display: block; text-decoration: none;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <img id="img_{{ $id }}" src="{{ $placeholderSvg }}" alt="{{ $title }}" style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 0 15px {{ $borderColor }}40; display: block;" />
        <canvas id="{{ $id }}" width="{{ $width }}" height="{{ $height }}" style="display: none;" data-card-options="{{ $cardOptionsJson }}"></canvas>
    </a>
    @else
    <div id="wrapper_{{ $id }}" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#modal_{{ $id }}">
        <img id="img_{{ $id }}" src="{{ $placeholderSvg }}" alt="{{ $title }}" style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 0 15px {{ $borderColor }}40; display: block;" />
        <canvas id="{{ $id }}" width="{{ $width }}" height="{{ $height }}" style="display: none;" data-card-options="{{ $cardOptionsJson }}"></canvas>
    </div>
    @endif
@elseif($mode === 'display')
<div class="d-inline-flex flex-column align-items-center">
    <div id="wrapper_{{ $id }}" class="digital-card rarity-{{ $rarity }}" style="padding: 4px; border-radius: 16px; display: inline-block; max-width: 100%; {{ $hasFullscreen ? 'cursor: pointer; transition: transform 0.2s;' : '' }}" 
        @if($hasFullscreen) 
            onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#modal_{{ $id }}"
        @endif>
        <canvas id="{{ $id }}" width="{{ $width }}" height="{{ $height }}" class="digital-card-canvas" style="border-radius: 10px; box-shadow: 0 0 15px {{ $borderColor }}40; max-width: 100%; height: auto;" data-card-options="{{ $cardOptionsJson }}"></canvas>
    </div>
    @if($showProgressBar)
    <div class="card-level-progress-wrapper w-100 mt-2 px-1" style="max-width: {{ $width }}px;">
        <div class="d-flex justify-content-between align-items-center mb-1 small" style="font-family: 'Orbitron', sans-serif; font-size: 0.75rem; color: {{ $computedRarityColor }};">
            <span>NEXT LEVEL PROGRESS</span>
            <span>{{ $nextLevelProgress }}%</span>
        </div>
        <div class="progress" style="height: 6px; background-color: #111122; border: 1px solid rgba(0, 240, 255, 0.15); border-radius: 4px; overflow: hidden; box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);">
            <div class="progress-bar" role="progressbar" style="width: {{ $nextLevelProgress }}%; background-color: {{ $computedRarityColor }}; box-shadow: 0 0 8px {{ $computedRarityColor }}; transition: width 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);" aria-valuenow="{{ $nextLevelProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
    @endif
</div>
@else
<div class="d-inline-flex flex-column align-items-center">
    <div id="wrapper_{{ $id }}" @if($hasFullscreen) style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#modal_{{ $id }}" @endif>
        <canvas id="{{ $id }}" width="{{ $width }}" height="{{ $height }}" class="digital-card-canvas" style="border-radius: 10px; box-shadow: 0 0 15px {{ $borderColor }}40; max-width: 100%; height: auto;" data-card-options="{{ $cardOptionsJson }}"></canvas>
    </div>
    @if($showProgressBar)
    <div class="card-level-progress-wrapper w-100 mt-2 px-1" style="max-width: {{ $width }}px;">
        <div class="d-flex justify-content-between align-items-center mb-1 small" style="font-family: 'Orbitron', sans-serif; font-size: 0.75rem; color: {{ $computedRarityColor }};">
            <span>NEXT LEVEL PROGRESS</span>
            <span>{{ $nextLevelProgress }}%</span>
        </div>
        <div class="progress" style="height: 6px; background-color: #111122; border: 1px solid rgba(0, 240, 255, 0.15); border-radius: 4px; overflow: hidden; box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);">
            <div class="progress-bar" role="progressbar" style="width: {{ $nextLevelProgress }}%; background-color: {{ $computedRarityColor }}; box-shadow: 0 0 8px {{ $computedRarityColor }}; transition: width 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);" aria-valuenow="{{ $nextLevelProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Full Screen Modal -->
@if($hasFullscreen)
@push('modals')
<div class="modal fade" wire:ignore.self id="modal_{{ $id }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered modal-lg d-flex justify-content-center">
        <div class="modal-content" style="background: transparent; border: none; align-items: center; box-shadow: none;">
            <div class="digital-card rarity-{{ $rarity }} d-flex flex-column align-items-center" style="padding: 4px; border-radius: 16px; width: 100%; max-width: 500px; margin: 0 auto;">
                <canvas id="fullscreen_{{ $id }}" width="500" height="700" class="digital-card-canvas" style="border-radius: 12px; display: block; position: relative; z-index: 1; max-width: 100%; height: auto;" data-card-options="{{ $cardOptionsJson }}"></canvas>
                @if($showProgressBar)
                <div class="card-level-progress-wrapper w-100 mt-3 px-3 pb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1 small" style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: {{ $computedRarityColor }};">
                        <span>NEXT LEVEL PROGRESS</span>
                        <span>{{ $nextLevelProgress }}%</span>
                    </div>
                    <div class="progress" style="height: 8px; background-color: #111122; border: 1px solid rgba(0, 240, 255, 0.15); border-radius: 4px; overflow: hidden; box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);">
                        <div class="progress-bar" role="progressbar" style="width: {{ $nextLevelProgress }}%; background-color: {{ $computedRarityColor }}; box-shadow: 0 0 10px {{ $computedRarityColor }}; transition: width 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);" aria-valuenow="{{ $nextLevelProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                @endif
            </div>
            <div class="mt-4 text-center d-flex gap-2 justify-content-center flex-wrap">
                @if($detailUrl)
                <a href="{{ $detailUrl }}" class="btn btn-neon">
                    <i class="bi bi-info-circle"></i> DETAILS
                </a>
                @endif
                
                @if($cardId && Auth::check() && Auth::id() != $ownerId)
                <button type="button" class="btn btn-outline-danger" onclick="window.openReportModal({{ $cardId }})">
                    <i class="bi bi-flag-fill"></i> REPORT
                </button>
                @endif

                <button type="button" class="btn btn-neon-magenta" onclick="bootstrap.Modal.getOrCreateInstance(document.getElementById('modal_{{ $id }}')).hide();">
                    <i class="bi bi-x-lg"></i> CLOSE
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
@endif

<script>
    (function() {
        const initCard = function(attempts = 0) {
            if (typeof DigitalCardRenderer === 'undefined') {
                // If DigitalCardRenderer is not yet defined, retry in 100ms (max 5 seconds)
                if (attempts < 50) setTimeout(() => initCard(attempts + 1), 100);
                return;
            }
            const canvasId = '{{ $id }}';
            const canvasEl = document.getElementById(canvasId);
            if (!canvasEl) {
                // If the element is not in the DOM yet (e.g. Livewire is patching), retry
                if (attempts < 50) setTimeout(() => initCard(attempts + 1), 100);
                return;
            }
            
            if (!window.digitalCardRenderers) window.digitalCardRenderers = {};
            window.digitalCardRenderers[canvasId] = new DigitalCardRenderer(canvasId);
            
            const rawOptions = canvasEl.getAttribute('data-card-options');
            if (!rawOptions) return;
            
            const initialOptions_{{ $id }} = JSON.parse(rawOptions);
            window.digitalCardRenderers[canvasId].draw(initialOptions_{{ $id }});

            @if($hasFullscreen)
            const fsCanvasId = 'fullscreen_{{ $id }}';
            window.digitalCardRenderers[fsCanvasId] = new DigitalCardRenderer(fsCanvasId);
            
            const modalEl = document.getElementById('modal_{{ $id }}');
            if (modalEl) {
                // Ensure the canvas is re-drawn when modal opens
                modalEl.addEventListener('shown.bs.modal', function () {
                    const freshCanvasEl = document.getElementById(canvasId);
                    if (freshCanvasEl) {
                        const freshOptions = JSON.parse(freshCanvasEl.getAttribute('data-card-options'));
                        const fullscreenOptions = { ...freshOptions, isFullScreenRender: true };
                        window.digitalCardRenderers[fsCanvasId].draw(fullscreenOptions);
                    }
                });
            }
            @endif

            window['updateDigitalCard_' + canvasId] = function(newOptions) {
                Object.assign(initialOptions_{{ $id }}, newOptions);
                window.digitalCardRenderers[canvasId].draw(initialOptions_{{ $id }});
                
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    canvas.setAttribute('data-card-options', JSON.stringify(initialOptions_{{ $id }}));
                    if (newOptions.borderColor) {
                        canvas.style.boxShadow = `0 0 15px ${newOptions.borderColor}40`;
                    }
                }
            };
        };

        // Expose to global for Livewire to call
        window['initCard_{{ $id }}'] = initCard;

        if (document.readyState === 'complete') {
            initCard();
        } else {
            document.addEventListener('DOMContentLoaded', initCard);
        }
    })();
</script>
