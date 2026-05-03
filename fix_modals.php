<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

$invite = <<<HTML
<form action="{{ route('battles.action.invite', \$battle) }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" id="invite_nominee_id">
    <div class="mb-3 position-relative">
        <label class="form-label">PLAYER USERNAME</label>
        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px; position: relative;">
            <span class="badge d-flex align-items-center gap-2 p-2 d-none" id="invite_selected_badge" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                <i class="bi bi-person-fill"></i> 
                <span id="invite_selected_username"></span>
                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearInvite()"></i>
            </span>
            <input type="text" id="invite_search_input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;" oninput="searchUsers(this.value, 'invite')">
        </div>
        <div class="position-absolute w-100 mt-1 d-none" id="invite_search_results" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>
    </div>
    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
</div>
<div class="modal-footer border-0 pt-0">
    <button type="submit" class="btn btn-neon w-100" id="invite_submit_btn" disabled>SEND INVITE</button>
</div>
</form>
HTML;

$html = preg_replace('/<div class="mb-3 position-relative">\s*<label class="form-label">PLAYER USERNAME<\/label>.*?<button type="button" class="btn btn-neon w-100" data-bs-dismiss="modal"[^>]*>SEND INVITE<\/button>\s*<\/div>/s', $invite, $html);

$marshall = <<<HTML
<form action="{{ route('battles.action.elect_marshall', \$battle) }}" method="POST">
    @csrf
    <input type="hidden" name="marshall_id" id="marshall_nominee_id">
    <div class="mb-3 position-relative">
        <label class="form-label">MARSHALL USERNAME</label>
        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px; position: relative;">
            <span class="badge d-flex align-items-center gap-2 p-2 d-none" id="marshall_selected_badge" style="background: rgba(255,221,0,0.2); border: 1px solid #ffdd00; color: #ffdd00; font-size: 0.9rem;">
                <i class="bi bi-person-fill"></i> 
                <span id="marshall_selected_username"></span>
                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearMarshall()"></i>
            </span>
            <input type="text" id="marshall_search_input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;" oninput="searchUsers(this.value, 'marshall')">
        </div>
        <div class="position-absolute w-100 mt-1 d-none" id="marshall_search_results" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>
    </div>
    <p class="text-muted small">Both team leaders must elect the same user for them to be designated as the marshall.</p>
</div>
<div class="modal-footer border-0 pt-0">
    <button type="submit" class="btn btn-neon w-100" id="marshall_submit_btn" disabled style="border-color: #ffdd00; color: #ffdd00;">ELECT USER</button>
</div>
</form>
HTML;

$html = preg_replace('/<div class="mb-3 position-relative">\s*<label class="form-label">MARSHALL USERNAME<\/label>.*?<button type="button" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;" data-bs-dismiss="modal">ELECT USER<\/button>\s*<\/div>/s', $marshall, $html);

$js = <<<JS
<script>
let searchTimeout = null;
function searchUsers(query, type) {
    clearTimeout(searchTimeout);
    let resultsDiv = document.getElementById(type + '_search_results');
    if(query.length < 2) {
        resultsDiv.classList.add('d-none');
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch('/battles/' + {{ \$battle->id }} + '/search?q=' + query)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if(data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                } else {
                    data.forEach(user => {
                        let div = document.createElement('div');
                        div.className = 'p-2 d-flex align-items-center gap-2';
                        div.style.cssText = 'cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);';
                        div.onclick = () => selectUser(user.id, user.username, type);
                        div.innerHTML = '<img src="' + (user.avatar_url || '') + '" style="width: 24px; height: 24px; border-radius: 50%;"> <span class="text-white">@' + user.username + '</span>';
                        resultsDiv.appendChild(div);
                    });
                }
                resultsDiv.classList.remove('d-none');
            });
    }, 300);
}

function selectUser(id, username, type) {
    document.getElementById(type + '_nominee_id').value = id;
    document.getElementById(type + '_search_input').classList.add('d-none');
    document.getElementById(type + '_search_results').classList.add('d-none');
    let badge = document.getElementById(type + '_selected_badge');
    badge.classList.remove('d-none');
    document.getElementById(type + '_selected_username').innerText = username;
    document.getElementById(type + '_submit_btn').disabled = false;
}

function clearInvite() {
    document.getElementById('invite_nominee_id').value = '';
    document.getElementById('invite_search_input').classList.remove('d-none');
    document.getElementById('invite_search_input').value = '';
    document.getElementById('invite_selected_badge').classList.add('d-none');
    document.getElementById('invite_submit_btn').disabled = true;
}

function clearMarshall() {
    document.getElementById('marshall_nominee_id').value = '';
    document.getElementById('marshall_search_input').classList.remove('d-none');
    document.getElementById('marshall_search_input').value = '';
    document.getElementById('marshall_selected_badge').classList.add('d-none');
    document.getElementById('marshall_submit_btn').disabled = true;
}
</script>
JS;

$html = str_replace("</script>\n<script>\n    document.addEventListener('DOMContentLoaded', () => {", "</script>\n" . $js . "\n<script>\n    document.addEventListener('DOMContentLoaded', () => {", $html);

file_put_contents('resources/views/battles/room.blade.php', $html);
