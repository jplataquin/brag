<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                            <!-- Share QR (Mobile Only) -->
                            <button type="button" class="btn btn-neon d-md-none" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                                <i class="bi bi-qr-code"></i> SHARE QR
                            </button>
                        </div>
                    </div>
HTML;

$new = <<<'HTML'
                        </div>
                    </div>
                    
                    <!-- Share QR (Mobile Only) - Moved outside actions-container so it survives AJAX replacements -->
                    <div class="mt-3 text-center d-md-none">
                        <button type="button" class="btn btn-neon" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                            <i class="bi bi-qr-code"></i> SHARE QR
                        </button>
                    </div>
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
