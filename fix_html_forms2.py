import re

with open('resources/views/battles/room.blade.php', 'r') as f:
    html = f.read()

# 7. Rename Team Modal A
html = html.replace('<div class="mb-4">\n                    <input type="text"  class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name">\n                </div>\n                <div class="d-flex gap-3">\n                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>\n                    <button type="button" class="btn btn-neon w-50 orbitron" data-bs-dismiss="modal">SAVE</button>\n                </div>',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">\n@csrf\n<input type="hidden" name="team" value="A">\n<div class="mb-4">\n<input type="text" name="name" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>\n</div>\n<div class="d-flex gap-3">\n<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>\n<button type="submit" class="btn btn-neon w-50 orbitron">SAVE</button>\n</div>\n</form>')

# 8. Rename Team Modal B
html = html.replace('<div class="mb-4">\n                    <input type="text"  class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name">\n                </div>\n                <div class="d-flex gap-3">\n                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>\n                    <button type="button" class="btn btn-neon-magenta w-50 orbitron" data-bs-dismiss="modal">SAVE</button>\n                </div>',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">\n@csrf\n<input type="hidden" name="team" value="B">\n<div class="mb-4">\n<input type="text" name="name" class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name" required>\n</div>\n<div class="d-flex gap-3">\n<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>\n<button type="submit" class="btn btn-neon-magenta w-50 orbitron">SAVE</button>\n</div>\n</form>')

# 9. Elect Marshall
html = html.replace('<div class="mb-3 position-relative">\n                        <label class="form-label">MARSHALL USERNAME</label>',
    '<form action="{{ route(\'battles.action.elect_marshall\', $battle) }}" method="POST">\n@csrf\n<input type="hidden" name="marshall_id" id="marshall_id" value="">\n<div class="mb-3 position-relative">\n<label class="form-label">MARSHALL USERNAME</label>')
html = html.replace('<div class="modal-footer border-0 pt-0">\n                    <button type="button" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;" data-bs-dismiss="modal">ELECT USER</button>\n                </div>',
    '<div class="modal-footer border-0 pt-0">\n<button type="submit" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;">ELECT USER</button>\n</div>\n</form>')

# 10. Invite Players
html = html.replace('<div class="mb-3 position-relative">\n                        <label class="form-label">PLAYER USERNAME</label>',
    '<form action="{{ route(\'battles.action.invite\', $battle) }}" method="POST">\n@csrf\n<input type="hidden" name="user_id" id="invite_id" value="">\n<div class="mb-3 position-relative">\n<label class="form-label">PLAYER USERNAME</label>')
html = html.replace('<div class="modal-footer border-0 pt-0">\n                    <button type="button" class="btn btn-neon w-100" data-bs-dismiss="modal" @if(!$inviteNomineeId) disabled @endif>SEND INVITE</button>\n                </div>',
    '<div class="modal-footer border-0 pt-0">\n<button type="submit" class="btn btn-neon w-100">SEND INVITE</button>\n</div>\n</form>')

# 11. Cancellation Request Modal
html = html.replace('<div class="d-flex gap-3">\n                        <button type="button" class="btn btn-neon-magenta w-100" >\n                            <i class="bi bi-check-lg"></i> AGREE & CANCEL\n                        </button>\n                        <button type="button" class="btn btn-outline-secondary w-100" style="border-color: #555;" >\n                            <i class="bi bi-x-lg"></i> REJECT\n                        </button>\n                    </div>',
    '<div class="d-flex gap-3">\n<form action="{{ route(\'battles.action.respond_cancel\', $battle) }}" method="POST" class="w-100">\n@csrf\n<input type="hidden" name="agreed" value="1">\n<button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>\n</form>\n<form action="{{ route(\'battles.action.respond_cancel\', $battle) }}" method="POST" class="w-100">\n@csrf\n<input type="hidden" name="agreed" value="0">\n<button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button>\n</form>\n</div>')

# 12. Declare Win
html = html.replace('<button type="button" class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) document.getElementById(\'form-win-a\').submit() })" >TEAM A WON</button>',
    '<form id="form-win-a" action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">\n@csrf\n<input type="hidden" name="team" value="A">\n<button type="button" class="btn btn-neon btn-sm" onclick="if(confirm(\'Declare TEAM A as the winner?\')) document.getElementById(\'form-win-a\').submit()">TEAM A WON</button>\n</form>')

html = html.replace('<button type="button" class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) document.getElementById(\'form-win-b\').submit() })" >TEAM B WON</button>',
    '<form id="form-win-b" action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">\n@csrf\n<input type="hidden" name="team" value="B">\n<button type="button" class="btn btn-neon-magenta btn-sm" onclick="if(confirm(\'Declare TEAM B as the winner?\')) document.getElementById(\'form-win-b\').submit()">TEAM B WON</button>\n</form>')

html = html.replace('<button type="button" class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm(\'Are you sure you want to CANCEL this match? No cards will be transferred.\').then(c => { if(c) document.getElementById(\'form-cancel\').submit() })" >CANCEL MATCH</button>',
    '<form id="form-cancel" action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">\n@csrf\n<button type="button" class="btn btn-neon-danger btn-sm" onclick="if(confirm(\'CANCEL this match?\')) document.getElementById(\'form-cancel\').submit()">CANCEL MATCH</button>\n</form>')

with open('resources/views/battles/room.blade.php', 'w') as f:
    f.write(html)
