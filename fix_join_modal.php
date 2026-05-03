<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$bad_html = <<<'HTML'
                    @endif
                    <?php if(isset($errors) && $errors->has("selectedCardId")): ?><div class="text-danger small mt-2 text-center">{{ $errors->first("selectedCardId") }}</div><?php endif; ?>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button></form>
                </div>
            </div>
        </div>
HTML;

$good_html = <<<'HTML'
                    @endif
                    <?php if(isset($errors) && $errors->has("selectedCardId")): ?><div class="text-danger small mt-2 text-center">{{ $errors->first("selectedCardId") }}</div><?php endif; ?>

                <div class="d-flex gap-3 mt-4">
                    <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button>
                </div>
                </form>
            </div>
        </div>
        </div>
HTML;

$content = str_replace($bad_html, $good_html, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
