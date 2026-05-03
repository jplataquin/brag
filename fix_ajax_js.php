<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$ajaxJs = <<<'JS'
<script>
    // Universal AJAX Form Handler for Modals
    function setupAjaxForm(formId, modalId, successCallback = null) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHTML = submitBtn.innerHTML;
            const errorDiv = form.querySelector('.form-error-display');
            const modalEl = document.getElementById(modalId);
            
            // Loading State
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> PROCESSING...';
            if (errorDiv) errorDiv.classList.add('d-none');
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Something went wrong. Please try again.');
                }
                return data;
            })
            .then(data => {
                // Success
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.hide();
                
                if (typeof window.neonAlert === 'function') {
                    window.neonAlert(data.message, "SUCCESS");
                }
                
                if (successCallback) successCallback(data);
                form.reset();
            })
            .catch(error => {
                // Error
                if (errorDiv) {
                    errorDiv.innerText = error.message;
                    errorDiv.classList.remove('d-none');
                } else {
                    alert(error.message);
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHTML;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Setup Rename Form
        setupAjaxForm('renameTeamForm', 'renameTeamModal', (data) => {
            // Local update for immediate feedback
            const teamId = data.team === 'A' ? 'nakedA' : 'nakedB';
            const els = document.querySelectorAll('[x-ref="' + teamId + '"]');
            els.forEach(el => el.innerText = data.newName);
        });

        // Setup Elect Marshall Form
        setupAjaxForm('electMarshallForm', 'electMarshallModal', (data) => {
            clearMarshall(); // Reset the search state
        });

        // Setup Invite Player Form
        setupAjaxForm('invitePlayerForm', 'invitePlayerModal', (data) => {
            clearInvite(); // Reset the search state
        });
    });
</script>
JS;

$content = preg_replace('/<\/script>\s*@endsection/s', "</script>\n" . $ajaxJs . "\n@endsection", $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
