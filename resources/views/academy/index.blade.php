@extends('layouts.app')

@section('title', 'Academy')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase mb-2" style="color: var(--neon-cyan); text-shadow: 0 0 15px rgba(0, 240, 255, 0.6); font-family: 'Orbitron', sans-serif;">
            <i class="bi bi-mortarboard-fill"></i> Brag Academy
        </h1>
        <p class="text-secondary lead">Master the mechanics of forging, battling, and dominating the leaderboard.</p>
        <div class="mx-auto bg-info" style="height: 3px; width: 80px; box-shadow: 0 0 10px var(--neon-cyan);"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="accordion neon-accordion" id="academyAccordion">
                
                <!-- Section 1: The Forge -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForge">
                            <i class="bi bi-hammer me-3 text-info"></i> THE FORGE: TEMPLATES & CARDS
                        </button>
                    </h2>
                    <div id="collapseForge" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h5 class="text-white mb-3">Understand the Foundation</h5>
                                    <p>Everything in Brag starts at <strong>The Forge</strong>. There are two core items you need to know:</p>
                                    <ul>
                                        <li><strong class="text-info">Templates:</strong> These are the "blueprints" or designs. You can create up to 3 templates per Game Title. You can use AI to generate art for them!</li>
                                        <li><strong class="text-info">Digital Cards:</strong> These are the physical trophies forged from your templates. These are what you actually stake in battles.</li>
                                    </ul>
                                    <p>Forging requires <strong class="text-warning">Diamonds</strong>. Once a card is forged, it belongs to you until you lose it in a battle.</p>
                                </div>
                                <div class="col-md-5 text-center">
                                    <i class="bi bi-layers-fill text-info opacity-25" style="font-size: 8rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: The Arena -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArena">
                            <i class="bi bi-crosshair me-3 text-danger"></i> THE ARENA: Pitting Your Cards
                        </button>
                    </h2>
                    <div id="collapseArena" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">High Stakes PvP</h5>
                            <p>The <strong>Arena</strong> is where you challenge other players. Battles are the heart of Brag.</p>
                            <div class="card bg-black bg-opacity-50 border-danger border-opacity-25 p-3 mb-3">
                                <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> THE GOLDEN RULE</h6>
                                <p class="mb-0 small">When you join a battle, you must stake one of your Digital Cards. <strong>The winner of the battle takes the loser's card as a trophy.</strong></p>
                            </div>
                            <p>Battles can be 1v1 or team-based. You can create your own battle room or join an existing one using a QR code or link.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Marshalls -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMarshall">
                            <i class="bi bi-shield-shaded me-3 text-warning"></i> THE MARSHALL: Fair Play
                        </button>
                    </h2>
                    <div id="collapseMarshall" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">The Third Party</h5>
                            <p>To ensure fairness in high-stakes matches, you can invite a <strong>Marshall</strong> to your battle room. A Marshall is a neutral user who does not participate in the fight but has the power to declare the official winner based on the results of your game.</p>
                            <p>Marshalls are essential for professional matches and resolving disputes.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Card Stats & Leveling -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStats">
                            <i class="bi bi-bar-chart-fill me-3 text-success"></i> CARD STATS: The Path to Elite
                        </button>
                    </h2>
                    <div id="collapseStats" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">Understanding Your Card's Worth</h5>
                            <p>Every Digital Card tracks its history. These stats determine its level and its value on the platform:</p>
                            <ul>
                                <li><strong class="text-info">Win Rate %:</strong> Calculated as <code>(Wins / Total Battles) * 100</code>. Consistency is key!</li>
                                <li><strong class="text-info">Integrity Stat:</strong> This is a unique anti-farming metric. It is calculated as <code>(Unique Opponents / Total Battles) * 100</code>. Fighting the same person over and over will lower this score. To keep high integrity, you must challenge a diverse range of opponents.</li>
                                <li><strong class="text-info">Life Points (LP):</strong> Every card starts with <strong>3 Life Points</strong>. Each time a card loses a battle, it loses 1 LP. At 0 LP, the card is surrendered to the winner as a Trophy. (Note: Surrendered cards reset to 3 LP for their new owner).</li>
                            </ul>
                            
                            <h5 class="text-white mt-4 mb-3">Level Up Eligibility</h5>
                            <p>Cards do not level up by wins alone. To reach the next level, a card must satisfy three conditions <strong>simultaneously</strong>:</p>
                            <ol>
                                <li>Minimum <strong>Total Wins</strong> reached.</li>
                                <li>Minimum <strong>Win Rate %</strong> maintained.</li>
                                <li>Minimum <strong>Integrity Stat %</strong> maintained.</li>
                            </ol>
                            <div class="p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25">
                                <small class="text-info fw-bold"><i class="bi bi-info-circle-fill me-1"></i> PRO TIP:</small>
                                <small class="text-secondary d-block">When a card levels up, its Life Points are fully restored to 3!</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Progression -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProgression">
                            <i class="bi bi-graph-up-arrow me-3 text-success"></i> PROGRESSION: Levels & Trophies
                        </button>
                    </h2>
                    <div id="collapseProgression" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">Build Your Legacy</h5>
                            <p>Winning battles does more than just earn you cards. It levels up your inventory:</p>
                            <ul>
                                <li><strong class="text-success">Leveling Up:</strong> As a card wins more matches, its level increases. Higher-level cards are rarer and more prestigious.</li>
                                <li><strong class="text-success">Trophy Collection:</strong> Cards won from others are stored in your <strong>Trophy Room</strong>. These cards display the original creator's name, serving as a permanent record of your dominance.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 5: The Burn -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBurn">
                            <i class="bi bi-fire me-3" style="color: #ff6600;"></i> THE BURN: Earning Diamonds
                        </button>
                    </h2>
                    <div id="collapseBurn" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">Recycle Your Collection</h5>
                            <p>Burning is the process of permanently destroying a digital card in exchange for immediate resources.</p>
                            <ul>
                                <li><strong style="color: #ff6600;">Burning Forged Cards:</strong> If you burn a card that you created yourself, you will receive a portion of Diamonds back into your wallet.</li>
                                <li><strong style="color: #ff6600;">Burning Trophies:</strong> You can also burn trophies you have won from other players to claim Diamonds. This is a great way to clear out your inventory while funding your next forge.</li>
                            </ul>
                            <p class="mb-0 text-muted fst-italic">Warning: Burning is permanent. Once a card is burned, it is removed from the platform forever.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Economy -->
                <div class="accordion-item bg-dark bg-opacity-50 border-info border-opacity-25 mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent text-white fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEconomy">
                            <i class="bi bi-gem me-3 text-info"></i> THE ECONOMY: Diamond Regeneration
                        </button>
                    </h2>
                    <div id="collapseEconomy" class="accordion-collapse collapse" data-bs-parent="#academyAccordion">
                        <div class="accordion-body text-secondary lh-lg p-4 pt-0">
                            <h5 class="text-white mb-3">Funding Your Legacy</h5>
                            <p><strong>Diamonds</strong> are required to forge new templates and cards. While you can always purchase Diamonds in your Wallet, the platform also provides scheduled support:</p>
                            <ul>
                                <li><strong class="text-info">Scheduled Regeneration:</strong> Twice a month—on the **15th** and the **last day of the month**—the platform checks all active users.</li>
                                <li><strong class="text-success">The Grant:</strong> If your Diamond balance is below the minimum threshold (5 Diamonds), the system will automatically top your balance back up to 10 Diamonds for free.</li>
                            </ul>
                            <p class="mb-0">This ensures that every player has the opportunity to get back into the forge and rejoin the battle twice every month.</p>
                        </div>
                    </div>
                </div>

            </div>

            @auth
            <div class="mt-5 text-center p-5 rounded-4 bg-dark bg-opacity-50 border border-info border-opacity-10">
                <h4 class="text-white mb-3 orbitron">Need a hands-on guide?</h4>
                <p class="text-secondary mb-4">Restart the interactive tour to walk through the dashboard again.</p>
                <form action="{{ route('academy.restart_tour') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-neon-cyan px-5 py-3 fw-bold">
                        <i class="bi bi-play-circle-fill me-2"></i> RESTART INTERACTIVE TOUR
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>
</div>

<style>
.neon-accordion .accordion-button:not(.collapsed) {
    color: var(--neon-cyan);
    box-shadow: none;
}
.neon-accordion .accordion-button::after {
    filter: invert(1) sepia(1) saturate(5) hue-rotate(175deg);
}
.neon-accordion .accordion-item {
    backdrop-filter: blur(10px);
}
</style>
@endsection
