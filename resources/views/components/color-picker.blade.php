@props(['id', 'name', 'label', 'value' => '#0a0a1a'])

<div class="color-picker-container mb-3">
    @if($label)
        <label for="{{ $id }}_hex" class="form-label">{{ $label }}</label>
    @endif
    <div class="d-flex align-items-center gap-2">
        <div class="color-preview-btn" 
             id="{{ $id }}_preview" 
             style="background-color: {{ old($name, $value) }}; width: 50px; height: 45px; border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 4px; cursor: pointer; box-shadow: 0 0 5px rgba(0,240,255,0.2);"
             data-bs-toggle="modal" 
             data-bs-target="#{{ $id }}_modal"
             title="Click to pick a color">
        </div>
        <input type="text" 
               class="form-control hex-input @error($name) is-invalid @enderror" 
               id="{{ $id }}_hex" 
               name="{{ $name }}" 
               value="{{ old($name, $value) }}" 
               maxlength="7"
               style="width: 120px; font-family: monospace; text-transform: uppercase;">
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <!-- Color Picker Modal -->
    <div class="modal fade" id="{{ $id }}_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content" style="background-color: #0a0a1a; border: 1px solid rgba(0, 240, 255, 0.3); box-shadow: 0 0 20px rgba(0, 240, 255, 0.2);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(0, 240, 255, 0.1);">
                    <h5 class="modal-title" style="color: #00f0ff; font-family: 'Orbitron', sans-serif;">{{ $label ? 'Pick ' . $label : 'Pick Color' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center p-4">
                    <div id="{{ $id }}_picker"></div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(0, 240, 255, 0.1);">
                    <button type="button" class="btn btn-neon w-100" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Move modal to body to prevent z-index issues with backdrop
        const modalEl = document.getElementById('{{ $id }}_modal');
        if (modalEl) {
            document.body.appendChild(modalEl);
        }

        if (typeof iro !== 'undefined') {
            var colorPicker = new iro.ColorPicker("#{{ $id }}_picker", {
                width: 220,
                color: "{{ old($name, $value) }}",
                borderWidth: 1,
                borderColor: "rgba(0, 240, 255, 0.3)",
                layout: [
                    { component: iro.ui.Wheel },
                    { component: iro.ui.Slider, options: { sliderType: 'value', sliderSize: 20 } }
                ]
            });

            const previewBtn = document.getElementById('{{ $id }}_preview');
            const hexInput = document.getElementById('{{ $id }}_hex');

            // Update on picker change
            colorPicker.on('color:change', function(color) {
                previewBtn.style.backgroundColor = color.hexString;
                previewBtn.style.boxShadow = `0 0 10px ${color.hexString}`;
                hexInput.value = color.hexString.toUpperCase();
                hexInput.dispatchEvent(new Event('input', { bubbles: true }));
            });

            // Update on manual text input
            hexInput.addEventListener('input', function() {
                let val = this.value;
                if (!val.startsWith('#')) val = '#' + val;
                if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(val)) {
                    colorPicker.color.hexString = val;
                    previewBtn.style.backgroundColor = val;
                    previewBtn.style.boxShadow = `0 0 10px ${val}`;
                }
            });
        }
    });
</script>