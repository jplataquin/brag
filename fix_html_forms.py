import re

with open('resources/views/battles/room.blade.php', 'r') as f:
    html = f.read()

def wrap_form(html, btn_text, action_route, extra_inputs=''):
    # Finds a button by its exact text and wraps it in a form. 
    # Example: btn_text = "JOIN"
    # action_route = "{{ route('battles.action.join', $battle) }}"
    pass

# We can just manually replace the exact buttons.
# 1. Start Match
html = html.replace('<button type="button" class="btn btn-neon-lime"  style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">\n                                            <i class="bi bi-play-fill"></i> START MATCH\n                                        </button>', 
    f'<form action="{{{{ route(\'battles.action.start\', $battle) }}}}" method="POST" class="d-inline">\n@csrf\n<button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-play-fill"></i> START MATCH</button>\n</form>')

# 2. Cancel Battle
html = html.replace('<button type="button" class="btn btn-neon-danger" >\n                                        <i class="bi bi-x-circle"></i> CANCEL BATTLE\n                                    </button>',
    f'<form action="{{{{ route(\'battles.action.cancel\', $battle) }}}}" method="POST" class="d-inline">\n@csrf\n<button type="submit" class="btn btn-neon-danger"><i class="bi bi-x-circle"></i> CANCEL BATTLE</button>\n</form>')

html = html.replace('<button type="button" class="btn btn-outline-danger btn-sm" >\n                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL\n                                        </button>',
    f'<form action="{{{{ route(\'battles.action.cancel\', $battle) }}}}" method="POST" class="d-inline">\n@csrf\n<button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> REQUEST CANCEL</button>\n</form>')

# 3. Team B Ready
html = html.replace('<button type="button" class="btn btn-neon-lime"  style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">\n                                            <i class="bi bi-check2-all"></i> READY\n                                        </button>',
    f'<form action="{{{{ route(\'battles.action.ready\', $battle) }}}}" method="POST" class="d-inline">\n@csrf\n<button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button>\n</form>')

# 4. Stand Up
html = html.replace('<button type="button" class="btn btn-outline-warning" >\n                                    <i class="bi bi-box-arrow-right"></i> STAND UP\n                                </button>',
    f'<form action="{{{{ route(\'battles.action.standup\', $battle) }}}}" method="POST" class="d-inline">\n@csrf\n<button type="submit" class="btn btn-outline-warning"><i class="bi bi-box-arrow-right"></i> STAND UP</button>\n</form>')

# 5. Join
html = html.replace('<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>',
    f'<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'A\'; document.getElementById(\'pairingSlot\').value=\'{{\\$i}}\';">JOIN</button>')
html = html.replace('<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>',
    f'<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'B\'; document.getElementById(\'pairingSlot\').value=\'{{\\$i}}\';">JOIN</button>')

# 6. Join Modal Submit
html = html.replace('<div class="mb-4">\n                    <label class="form-label small text-center w-100 mb-3"',
    f'<form action="{{{{ route(\'battles.action.join\', $battle) }}}}" method="POST">\n@csrf\n<input type="hidden" name="joiningTeam" id="joiningTeam" value="">\n<input type="hidden" name="pairingSlot" id="pairingSlot" value="">\n<input type="hidden" name="selectedCardId" id="selectedCardId" value="">\n<div class="mb-4">\n<label class="form-label small text-center w-100 mb-3"')
html = html.replace('<div class="d-flex gap-3 mt-4">\n                    <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>\n                    <button type="button" class="btn btn-neon w-50 py-2 orbitron" data-bs-dismiss="modal" >\n                        <span >CONFIRM JOIN</span>\n                        <span ><i class="bi bi-hourglass-split"></i> JOINING...</span>\n                    </button>\n                </div>',
    '<div class="d-flex gap-3 mt-4">\n<button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>\n<button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button>\n</div>\n</form>')

# Add JS to select card
html = html.replace('class="selectable-card {{ (int)$selectedCardId === (int)$card->id ? \'selected\' : \'\' }}" \n                                                         \n                                                         style="cursor: pointer;"',
    'class="selectable-card" onclick="document.querySelectorAll(\'.selectable-card\').forEach(e=>e.classList.remove(\'selected\')); this.classList.add(\'selected\'); document.getElementById(\'selectedCardId\').value=\'{{$card->id}}\';" style="cursor: pointer;"')

# Remove Livewire card checks
html = html.replace('@if($selectedCardId == $card->id)', '<!--')
html = html.replace('</div>\n                                                            @endif', '-->')

with open('resources/views/battles/room.blade.php', 'w') as f:
    f.write(html)

