<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

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

$html = str_replace('</script>
</div>', "</script>\n" . $js . "\n</div>", $html);

file_put_contents('resources/views/battles/room.blade.php', $html);
