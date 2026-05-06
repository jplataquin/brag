document.addEventListener('DOMContentLoaded', () => {
    const STUDIO_VERSION = "1.0";
    // Set version in UI
    const versionTag = document.getElementById('studio-version-tag');
    if (versionTag) versionTag.textContent = `v${STUDIO_VERSION}`;

    const renderer = new StudioRenderer('studio-canvas');
    const layerList = document.getElementById('layer-list');
    const settingsPanel = document.getElementById('layer-settings');
    const canvasOverlay = document.getElementById('canvas-overlay');
    
    function getDefaultState() {
        return {
            version: STUDIO_VERSION,
            customFonts: [], // { name: 'FontName', data: 'data:font/woff2;base64,...' }
            currentLevel: "1",
            levels: {
                "1": { layers: createDefaultLayers() },
                "2": { layers: createDefaultLayers() },
                "3": { layers: createDefaultLayers() },
                "4": { layers: createDefaultLayers() },
                "5": { layers: createDefaultLayers() }
            },
            activeLayerIndex: 0,
            activeElementKey: null, // Track which text element is selected
            isDragging: false,
            draggedElement: null, // { layerIndex, elementKey } for text, or just { layerIndex } for photo
            dragOffset: { x: 0, y: 0 },
            showGrid: false
        };
    }

    // Application State
    let state = getDefaultState();

    let undoStack = [];
    let redoStack = [];
    const MAX_HISTORY = 50;
    const AUTOSAVE_KEY = 'brag_studio_autosave';
    const DB_NAME = 'BragStudioDB';
    const STORE_NAME = 'autosave';

    function clearDB() {
        try {
            localStorage.removeItem(AUTOSAVE_KEY);
        } catch (e) {}
        const request = indexedDB.open(DB_NAME, 1);
        request.onsuccess = (e) => {
            const db = e.target.result;
            if (db.objectStoreNames.contains(STORE_NAME)) {
                const tx = db.transaction(STORE_NAME, 'readwrite');
                tx.objectStore(STORE_NAME).delete('latest_state');
            }
        };
    }

    function saveToDB(stateObj) {
        const request = indexedDB.open(DB_NAME, 1);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME);
            }
        };
        request.onsuccess = (e) => {
            const db = e.target.result;
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).put(JSON.stringify(stateObj), 'latest_state');
        };
    }

    function loadFromDB(callback) {
        const request = indexedDB.open(DB_NAME, 1);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME);
            }
        };
        request.onsuccess = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                 callback(null);
                 return;
            }
            const tx = db.transaction(STORE_NAME, 'readonly');
            const req = tx.objectStore(STORE_NAME).get('latest_state');
            req.onsuccess = () => {
                if (req.result) {
                    try {
                        callback(JSON.parse(req.result));
                    } catch(err) {
                        callback(null);
                    }
                } else {
                    callback(null);
                }
            };
            req.onerror = () => callback(null);
        };
        request.onerror = () => callback(null);
    }

    // Auto-Save Loop (Runs every 3 seconds)
    setInterval(() => {
        saveToDB(state);
    }, 3000);

    function saveState() {
        undoStack.push(JSON.stringify(state));
        if (undoStack.length > MAX_HISTORY) undoStack.shift();
        redoStack = [];
    }

    function undo() {
        if (undoStack.length > 0) {
            redoStack.push(JSON.stringify(state));
            state = JSON.parse(undoStack.pop());
            if (state.customFonts) {
                state.customFonts.forEach(font => injectFont(font.name, font.data));
            }
            renderCustomFontsList();
            refreshUI();
        }
    }

    function redo() {
        if (redoStack.length > 0) {
            undoStack.push(JSON.stringify(state));
            state = JSON.parse(redoStack.pop());
            if (state.customFonts) {
                state.customFonts.forEach(font => injectFont(font.name, font.data));
            }
            renderCustomFontsList();
            refreshUI();
        }
    }

    function createDefaultLayers() {
        return [
            { type: 'text', name: 'Text Layer', elements: createDefaultTextElements(), visible: true },
            { type: 'image', name: 'Image Layer 3 (Overlay)', data: null, x: 0, y: 0, width: 500, height: 700, visible: true, lockAspectRatio: true, ratio: 5/7 },
            { type: 'image', name: 'Image Layer 2 (Middle)', data: null, x: 0, y: 0, width: 500, height: 700, visible: true, lockAspectRatio: true, ratio: 5/7 },
            { type: 'photo', name: 'User Photo Layer', x: 50, y: 100, width: 300, height: 300, shape: 'square', size: 300, visible: true },
            { type: 'image', name: 'Image Layer 1 (BG)', data: null, x: 0, y: 0, width: 500, height: 700, visible: true, lockAspectRatio: true, ratio: 5/7 }
        ];
    }

    function createDefaultTextElements() {
        return {
            card_title: { x: 250, y: 50, font: 'Orbitron', size: 28, color: '#00f0ff', align: 'center', weight: '900', visible: true, content: '{title}' },
            game_title: { x: 250, y: 80, font: 'Montserrat', size: 14, color: '#bbbbd0', align: 'center', weight: '600', visible: true, content: '{game}' },
            serial: { x: 480, y: 680, font: 'Montserrat', size: 12, color: '#ffffff', align: 'right', weight: 'normal', visible: true, content: '{serial}' },
            wins: { x: 20, y: 650, font: 'Orbitron', size: 18, color: '#39ff14', align: 'left', weight: 'normal', visible: true, content: 'W: {wins}' },
            losses: { x: 20, y: 680, font: 'Orbitron', size: 18, color: '#ff00ff', align: 'left', weight: 'normal', visible: true, content: 'L: {losses}' },
            rate: { x: 250, y: 680, font: 'Orbitron', size: 18, color: '#ffdd00', align: 'center', weight: 'normal', visible: true, content: 'R: {rate}' },
            integrity: { x: 480, y: 620, font: 'Orbitron', size: 18, color: '#00f0ff', align: 'right', weight: 'normal', visible: true, content: 'I: {integrity}%' },
            health: { x: 480, y: 650, font: 'Orbitron', size: 18, color: '#ff0000', align: 'right', weight: 'normal', visible: true, content: '{health}' },
            quote: { x: 250, y: 550, font: 'Montserrat', size: 14, color: '#ffffff', align: 'center', weight: 'normal', visible: true, content: '"{quote}"' }
        };
    }

    const TEXT_DEFAULTS = {
        card_title: '{title}',
        game_title: '{game}',
        serial: '{serial}',
        wins: 'W: {wins}',
        losses: 'L: {losses}',
        rate: 'R: {rate}',
        integrity: 'I: {integrity}%',
        health: '{health}',
        quote: '"{quote}"'
    };

    // --- Custom Fonts Logic ---
    function injectFont(name, dataUri) {
        const styleId = `font-style-${name.replace(/[^a-zA-Z0-9]/g, '')}`;
        if (document.getElementById(styleId)) return;
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            @font-face {
                font-family: '${name}';
                src: url('${dataUri}') format('woff2');
            }
        `;
        document.head.appendChild(style);
    }

    function renderCustomFontsList() {
        const list = document.getElementById('custom-fonts-list');
        list.innerHTML = '';
        state.customFonts.forEach(font => {
            const li = document.createElement('li');
            li.className = 'list-group-item bg-dark border-secondary text-light d-flex justify-content-between align-items-center py-1';
            li.innerHTML = `
                <span style="font-family: '${font.name}';">${font.name}</span>
                <i class="bi bi-check-circle text-neon-lime"></i>
            `;
            list.appendChild(li);
        });
    }

    document.getElementById('font-upload').onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            if (!file.name.toLowerCase().endsWith('.woff2')) {
                alert('Only .woff2 fonts are supported.');
                return;
            }
            // Use filename (minus extension) as the font family name
            let fontName = file.name.replace(/\.woff2$/i, '').replace(/[^a-zA-Z0-9_-]/g, '');
            if (!fontName) fontName = 'CustomFont' + state.customFonts.length;

            // Check if exists
            if (state.customFonts.find(f => f.name === fontName)) {
                alert('A font with this name is already uploaded.');
                return;
            }

            const reader = new FileReader();
            reader.onload = async (event) => {
                const dataUri = event.target.result;
                state.customFonts.push({ name: fontName, data: dataUri });
                injectFont(fontName, dataUri);
                
                try {
                    await document.fonts.load(`16px '${fontName}'`);
                } catch (err) {
                    console.warn('Font load check failed', err);
                }
                
                renderCustomFontsList();
                refreshUI();
                e.target.value = ''; // Reset input
            };
            reader.readAsDataURL(file);
        }
    };


    // --- UI Rendering Functions ---

    function refreshUI() {
        renderLayerList();
        renderSettings();
        renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex).then(() => {
            if (state.showGrid) renderer.drawGrid();
        });
    }

    function renderLayerList() {
        layerList.innerHTML = '';
        const layers = state.levels[state.currentLevel].layers;
        
        layers.forEach((layer, index) => {
            const item = document.createElement('div');
            item.className = `list-group-item d-flex justify-content-between align-items-center ${state.activeLayerIndex === index ? 'active' : ''}`;
            item.innerHTML = `
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm text-light p-0 me-2 btn-toggle-visibility" data-index="${index}">
                        <i class="bi ${layer.visible !== false ? 'bi-eye-fill' : 'bi-eye-slash text-secondary'}"></i>
                    </button>
                    <span><i class="bi ${getLayerIcon(layer.type)} me-2"></i> ${layer.name}</span>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-dark btn-move-up" data-index="${index}" ${index === 0 ? 'disabled' : ''}><i class="bi bi-chevron-up"></i></button>
                    <button class="btn btn-dark btn-move-down" data-index="${index}" ${index === layers.length - 1 ? 'disabled' : ''}><i class="bi bi-chevron-down"></i></button>
                </div>
            `;
            item.onclick = (e) => {
                if (e.target.closest('.btn-group') || e.target.closest('.btn-toggle-visibility')) return;
                state.activeLayerIndex = index;
                refreshUI();
            };
            layerList.appendChild(item);
        });

        // Add visibility toggle listeners
        document.querySelectorAll('.btn-toggle-visibility').forEach(btn => {
            btn.onclick = (e) => {
                saveState();
                const idx = parseInt(btn.dataset.index);
                layers[idx].visible = layers[idx].visible === false ? true : false;
                refreshUI();
            };
        });

        // Add reorder button listeners
        document.querySelectorAll('.btn-move-up').forEach(btn => {
            btn.onclick = (e) => {
                const idx = parseInt(btn.dataset.index);
                swapLayers(idx, idx - 1);
            };
        });
        document.querySelectorAll('.btn-move-down').forEach(btn => {
            btn.onclick = (e) => {
                const idx = parseInt(btn.dataset.index);
                swapLayers(idx, idx + 1);
            };
        });
    }

    function getLayerIcon(type) {
        if (type === 'image') return 'bi-image';
        if (type === 'photo') return 'bi-person-bounding-box';
        return 'bi-fonts';
    }

    function swapLayers(idx1, idx2) {
        saveState();
        const layers = state.levels[state.currentLevel].layers;
        [layers[idx1], layers[idx2]] = [layers[idx2], layers[idx1]];
        state.activeLayerIndex = idx2;
        refreshUI();
    }

    function renderSettings() {
        const layer = state.levels[state.currentLevel].layers[state.activeLayerIndex];
        if (!layer) return;

        let html = `<div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-neon-cyan mb-0" style="font-family: 'Orbitron', sans-serif;">${layer.name} SETTINGS</h6>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="layer-visible-toggle" ${layer.visible !== false ? 'checked' : ''}>
                <label class="form-check-label small text-secondary" for="layer-visible-toggle">Visible</label>
            </div>
        </div>`;

        if (layer.type === 'image') {
            html += `
                <div class="row g-2 mb-3 align-items-end">
                    <div class="col-5">
                        <label class="form-label small">X POS</label>
                        <input type="number" class="form-control form-control-sm img-coord-input" data-prop="x" value="${layer.x || 0}">
                    </div>
                    <div class="col-5">
                        <label class="form-label small">Y POS</label>
                        <input type="number" class="form-control form-control-sm img-coord-input" data-prop="y" value="${layer.y || 0}">
                    </div>
                    <div class="col-2 text-center pb-1">
                        <button class="btn btn-sm p-0 text-neon-cyan btn-center-layer" title="Center on Card">
                            <i class="bi bi-crosshair"></i>
                        </button>
                    </div>

                    <div class="col-5">
                        <label class="form-label small">WIDTH</label>
                        <input type="number" class="form-control form-control-sm img-dim-input" id="img-width" data-prop="width" value="${layer.width || 500}">
                    </div>
                    <div class="col-2 text-center pb-2">
                        <button class="btn btn-sm p-0 text-neon-cyan" id="btn-lock-aspect" title="Lock Aspect Ratio">
                            <i class="bi ${layer.lockAspectRatio ? 'bi-link-45deg' : 'bi-link-45deg opacity-25'}"></i>
                        </button>
                    </div>
                    <div class="col-5">
                        <label class="form-label small">HEIGHT</label>
                        <input type="number" class="form-control form-control-sm img-dim-input" id="img-height" data-prop="height" value="${layer.height || 700}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">UPLOAD ASSET (PNG/WEBP)</label>
                    <input type="file" class="form-control form-control-sm" id="asset-upload" accept="image/*">
                    <div class="mt-2 text-center border border-secondary p-2" style="background: #000; height: 100px; display: flex; align-items: center; justify-content: center;">
                        ${layer.data ? `<img src="${layer.data}" style="max-width: 100%; max-height: 100%;">` : '<span class="text-secondary small">No image</span>'}
                    </div>
                </div>
            `;
        } else if (layer.type === 'photo') {
            html += `
                <div class="mb-3">
                    <label class="form-label small">PHOTO SHAPE</label>
                    <select class="form-select form-select-sm" id="photo-shape-select">
                        <option value="square" ${layer.shape === 'square' ? 'selected' : ''}>Square</option>
                        <option value="rectangle_portrait" ${layer.shape === 'rectangle_portrait' ? 'selected' : ''}>Rectangle (Portrait)</option>
                        <option value="circle" ${layer.shape === 'circle' ? 'selected' : ''}>Circle</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">SIZE</label>
                    <input type="number" class="form-control form-control-sm" id="photo-size-input" value="${layer.size || 300}">
                    <small class="text-secondary" style="font-size: 0.65rem;">Adjusts width and height proportionally.</small>
                </div>
                <div class="row g-2 mb-3 align-items-end">
                    <div class="col-5">
                        <label class="form-label small">X POS</label>
                        <input type="number" class="form-control form-control-sm coord-input" data-prop="x" value="${layer.x}">
                    </div>
                    <div class="col-5">
                        <label class="form-label small">Y POS</label>
                        <input type="number" class="form-control form-control-sm coord-input" data-prop="y" value="${layer.y}">
                    </div>
                    <div class="col-2 text-center pb-1">
                        <button class="btn btn-sm p-0 text-neon-cyan btn-center-layer" title="Center on Card">
                            <i class="bi bi-crosshair"></i>
                        </button>
                    </div>
                </div>
            `;
        } else if (layer.type === 'text') {
            html += '<div class="accordion accordion-flush bg-black" id="textElementsAccordion">';
            Object.entries(layer.elements).forEach(([key, el], idx) => {
                
                // Build Font Options including Custom Fonts
                let fontOptions = `
                    <option value="Orbitron" ${el.font === 'Orbitron' ? 'selected' : ''}>Orbitron</option>
                    <option value="Montserrat" ${el.font === 'Montserrat' ? 'selected' : ''}>Montserrat</option>
                    <option value="Arial" ${el.font === 'Arial' ? 'selected' : ''}>Arial</option>
                `;
                state.customFonts.forEach(cf => {
                    fontOptions += `<option value="${cf.name}" ${el.font === cf.name ? 'selected' : ''}>${cf.name} (Custom)</option>`;
                });

                const isExpanded = state.activeElementKey === key;

                html += `
                    <div class="accordion-item bg-black text-light border-secondary">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${isExpanded ? '' : 'collapsed'} bg-black text-light small py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${key}">
                                ${key.replace('_', ' ').toUpperCase()}
                            </button>
                        </h2>
                        <div id="collapse${key}" class="accordion-collapse collapse ${isExpanded ? 'show' : ''}" data-bs-parent="#textElementsAccordion">
                            <div class="accordion-body p-2 border-top border-secondary">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input text-toggle" type="checkbox" data-key="${key}" ${el.visible ? 'checked' : ''}>
                                    <label class="form-check-label small">Visible</label>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label x-small mb-0">Content Template</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control text-input" data-key="${key}" data-prop="content" id="input-content-${key}" value="${el.content || ''}" placeholder="e.g. WINS: {wins}">
                                        <button class="btn btn-outline-secondary btn-reset-content" data-key="${key}" title="Reset to default">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </div>
                                    <div class="x-small text-secondary mt-1" style="font-size: 0.6rem;">Use {title}, {game}, {serial}, {wins}, {losses}, {rate}, {integrity}, {health}, {quote}</div>
                                </div>
                                <div class="row g-2 mb-2 align-items-end">
                                    <div class="col-5">
                                        <label class="form-label x-small mb-0">X</label>
                                        <input type="number" class="form-control form-control-sm text-input" data-key="${key}" data-prop="x" value="${el.x}">
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label x-small mb-0">Y</label>
                                        <input type="number" class="form-control form-control-sm text-input" data-key="${key}" data-prop="y" value="${el.y}">
                                    </div>
                                    <div class="col-2 text-center pb-1">
                                        <button class="btn btn-sm p-0 text-neon-cyan btn-center-text" data-key="${key}" title="Center on Card">
                                            <i class="bi bi-crosshair"></i>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label x-small mb-0">Size</label>
                                        <input type="number" class="form-control form-control-sm text-input" data-key="${key}" data-prop="size" value="${el.size}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label x-small mb-0">Color</label>
                                        <input type="color" class="form-control form-control-sm text-input" data-key="${key}" data-prop="color" value="${el.color}">
                                    </div>
                                </div>
                                <label class="form-label x-small mb-0">Font</label>
                                <select class="form-select form-select-sm text-input mb-2" data-key="${key}" data-prop="font">
                                    ${fontOptions}
                                </select>

                                <label class="form-label x-small mb-0">Style</label>
                                <select class="form-select form-select-sm text-input mb-2" data-key="${key}" data-prop="weight">
                                    <option value="normal" ${el.weight === 'normal' || !el.weight ? 'selected' : ''}>Normal</option>
                                    <option value="bold" ${el.weight === 'bold' || el.weight === '900' || el.weight === '600' ? 'selected' : ''}>Bold</option>
                                    <option value="italic" ${el.weight === 'italic' ? 'selected' : ''}>Italic</option>
                                    <option value="bold italic" ${el.weight === 'bold italic' ? 'selected' : ''}>Bold Italic</option>
                                </select>

                                <label class="form-label x-small mb-0">Align</label>
                                <select class="form-select form-select-sm text-input" data-key="${key}" data-prop="align">
                                    <option value="left" ${el.align === 'left' ? 'selected' : ''}>Left</option>
                                    <option value="center" ${el.align === 'center' ? 'selected' : ''}>Center</option>
                                    <option value="right" ${el.align === 'right' ? 'selected' : ''}>Right</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        settingsPanel.innerHTML = html;
        attachSettingsEventListeners();
    }

    function attachSettingsEventListeners() {
        const layer = state.levels[state.currentLevel].layers[state.activeLayerIndex];

        document.getElementById('layer-visible-toggle').onchange = (e) => {
            saveState();
            layer.visible = e.target.checked;
            refreshUI();
        };

        if (layer.type === 'image') {
            document.getElementById('btn-lock-aspect').onclick = () => {
                saveState();
                layer.lockAspectRatio = !layer.lockAspectRatio;
                if (layer.lockAspectRatio) {
                    layer.ratio = layer.width / layer.height;
                }
                renderSettings();
            };

            document.querySelectorAll('.btn-center-layer').forEach(btn => {
                btn.onclick = () => {
                    saveState();
                    layer.x = Math.round((500 - (layer.width || 500)) / 2);
                    // layer.y remains unchanged
                    refreshUI();
                };
            });

            document.querySelectorAll('.img-coord-input').forEach(input => {
                input.onfocus = () => saveState();
                input.oninput = (e) => {
                    layer[input.dataset.prop] = parseInt(input.value) || 0;
                    renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                };
            });

            document.querySelectorAll('.img-dim-input').forEach(input => {
                input.onfocus = () => saveState();
                input.oninput = (e) => {
                    const val = parseInt(input.value) || 0;
                    layer[input.dataset.prop] = val;

                    if (layer.lockAspectRatio && layer.ratio) {
                        if (input.dataset.prop === 'width') {
                            layer.height = Math.round(val / layer.ratio);
                            document.getElementById('img-height').value = layer.height;
                        } else {
                            layer.width = Math.round(val * layer.ratio);
                            document.getElementById('img-width').value = layer.width;
                        }
                    } else {
                        // Update ratio if not locked so locking later uses current ratio
                        layer.ratio = layer.width / layer.height;
                    }

                    renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                };
            });

            document.getElementById('asset-upload').onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        saveState();
                        layer.data = event.target.result;
                        
                        // Try to get natural dimensions if not set
                        const img = new Image();
                        img.onload = () => {
                            layer.width = img.width;
                            layer.height = img.height;
                            layer.ratio = img.width / img.height;
                            refreshUI();
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            };
        } else if (layer.type === 'photo') {
            const shapeSelect = document.getElementById('photo-shape-select');
            const sizeInput = document.getElementById('photo-size-input');

            document.querySelectorAll('.btn-center-layer').forEach(btn => {
                btn.onclick = () => {
                    saveState();
                    layer.x = Math.round((500 - layer.width) / 2);
                    // layer.y remains unchanged
                    refreshUI();
                };
            });

            const updateDimensions = () => {
                saveState();
                const size = parseInt(sizeInput.value) || 0;
                layer.size = size;
                layer.shape = shapeSelect.value;
                
                if (layer.shape === 'rectangle_portrait') {
                    layer.width = size;
                    layer.height = Math.round(size * 1.4); // 5:7 ratio (7/5 = 1.4)
                } else {
                    layer.width = size;
                    layer.height = size;
                }
                renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
            };

            shapeSelect.onchange = updateDimensions;
            sizeInput.onchange = updateDimensions;

            document.querySelectorAll('.coord-input').forEach(input => {
                input.onfocus = () => saveState();
                input.oninput = (e) => {
                    layer[input.dataset.prop] = parseInt(input.value) || 0;
                    renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                };
            });
        } else if (layer.type === 'text') {
            document.querySelectorAll('.text-toggle').forEach(chk => {
                chk.onchange = () => {
                    saveState();
                    layer.elements[chk.dataset.key].visible = chk.checked;
                    renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                };
            });
            document.querySelectorAll('.text-input').forEach(input => {
                input.onfocus = () => saveState();
                input.oninput = () => {
                    const val = (input.type === 'number') ? parseInt(input.value) : input.value;
                    layer.elements[input.dataset.key][input.dataset.prop] = val;
                    renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                };
            });

            document.querySelectorAll('.btn-reset-content').forEach(btn => {
                btn.onclick = () => {
                    saveState();
                    const key = btn.dataset.key;
                    const defaultValue = TEXT_DEFAULTS[key];
                    if (defaultValue) {
                        layer.elements[key].content = defaultValue;
                        document.getElementById(`input-content-${key}`).value = defaultValue;
                        renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex);
                    }
                };
            });

            document.querySelectorAll('.btn-center-text').forEach(btn => {
                btn.onclick = () => {
                    saveState();
                    const key = btn.dataset.key;
                    layer.elements[key].x = 250; // Canvas center X
                    // layer.elements[key].y remains unchanged
                    layer.elements[key].align = 'center'; // Force text alignment to center
                    refreshUI();
                };
            });
        }
    }

    // --- Interaction Logic ---

    function getMousePos(canvas, evt) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: (evt.clientX - rect.left) / (rect.right - rect.left) * canvas.width,
            y: (evt.clientY - rect.top) / (rect.bottom - rect.top) * canvas.height
        };
    }

    renderer.canvas.onmousedown = (e) => {
        const pos = getMousePos(renderer.canvas, e);
        const currentLayers = state.levels[state.currentLevel].layers;
        
        let hit = false;
        // Check text elements first (top-down)
        for (let i = 0; i < currentLayers.length; i++) {
            const layer = currentLayers[i];
            if (layer.visible === false) continue;

            if (layer.type === 'text') {
                for (const [key, el] of Object.entries(layer.elements)) {
                    if (!el.visible) continue;
                    // Simple hit box for text
                    const size = el.size || 16;
                    if (pos.x >= el.x - 50 && pos.x <= el.x + 50 && pos.y >= el.y - size && pos.y <= el.y) {
                        saveState();
                        state.isDragging = true;
                        state.draggedElement = { layerIndex: i, elementKey: key };
                        state.dragOffset = { x: pos.x - el.x, y: pos.y - el.y };
                        state.activeLayerIndex = i;
                        state.activeElementKey = key; // Set active element
                        refreshUI();
                        hit = true;
                        return;
                    }
                }
            } else if (layer.type === 'photo') {
                if (pos.x >= layer.x && pos.x <= layer.x + layer.width && pos.y >= layer.y && pos.y <= layer.y + layer.height) {
                    saveState();
                    state.isDragging = true;
                    state.draggedElement = { layerIndex: i };
                    state.dragOffset = { x: pos.x - layer.x, y: pos.y - layer.y };
                    state.activeLayerIndex = i;
                    state.activeElementKey = null;
                    refreshUI();
                    hit = true;
                    return;
                }
            } else if (layer.type === 'image' && layer.data) {
                // Approximate image hitbox (using stored width/height if available, otherwise assume full canvas)
                const w = layer.width || renderer.width;
                const h = layer.height || renderer.height;
                const lx = layer.x || 0;
                const ly = layer.y || 0;
                if (pos.x >= lx && pos.x <= lx + w && pos.y >= ly && pos.y <= ly + h) {
                    saveState();
                    state.isDragging = true;
                    state.draggedElement = { layerIndex: i };
                    state.dragOffset = { x: pos.x - lx, y: pos.y - ly };
                    state.activeLayerIndex = i;
                    state.activeElementKey = null;
                    refreshUI();
                    hit = true;
                    return;
                }
            }
        }

        if (!hit) {
            state.activeLayerIndex = -1;
            state.activeElementKey = null;
            refreshUI();
        }
    };

    window.onkeydown = (e) => {
        // Undo / Redo
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
            e.preventDefault();
            if (e.shiftKey) {
                redo();
            } else {
                undo();
            }
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
            e.preventDefault();
            redo();
            return;
        }

        if (state.activeLayerIndex === -1) return;
        
        // Prevent scrolling with arrows
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
        } else {
            return;
        }

        const layer = state.levels[state.currentLevel].layers[state.activeLayerIndex];
        const step = e.shiftKey ? 10 : 1;
        let target = layer;

        if (layer.type === 'text' && state.activeElementKey) {
            target = layer.elements[state.activeElementKey];
        }

        saveState(); // Save before nudge

        switch (e.key) {
            case 'ArrowUp': target.y -= step; break;
            case 'ArrowDown': target.y += step; break;
            case 'ArrowLeft': target.x -= step; break;
            case 'ArrowRight': target.x += step; break;
        }

        refreshUI();
    };

    window.onmousemove = (e) => {
        if (!state.isDragging || !state.draggedElement) return;
        const pos = getMousePos(renderer.canvas, e);
        const layer = state.levels[state.currentLevel].layers[state.draggedElement.layerIndex];

        if (state.draggedElement.elementKey) {
            const el = layer.elements[state.draggedElement.elementKey];
            el.x = Math.round(pos.x - state.dragOffset.x);
            el.y = Math.round(pos.y - state.dragOffset.y);
        } else {
            layer.x = Math.round(pos.x - state.dragOffset.x);
            layer.y = Math.round(pos.y - state.dragOffset.y);
        }
        
        // Direct draw for smoothness
        renderer.draw(state.levels[state.currentLevel], state.activeLayerIndex).then(() => {
            if (state.showGrid) renderer.drawGrid();
            
            // Draw alignment guides for the dragged element
            // (check if still dragging, as onmouseup might have fired before promise resolves)
            if (state.draggedElement) {
                if (state.draggedElement.elementKey) {
                    const el = layer.elements[state.draggedElement.elementKey];
                    renderer.drawGuides(el.x, el.y);
                } else {
                    renderer.drawGuides(layer.x, layer.y);
                }
            }
        });
    };

    window.onmouseup = () => {
        if (state.isDragging) {
            state.isDragging = false;
            state.draggedElement = null;
            refreshUI(); // Sync settings panel
        }
    };

    // --- Level Selection & Copy ---
    document.querySelectorAll('input[name="level-select"]').forEach(radio => {
        radio.onchange = () => {
            state.currentLevel = radio.value;
            state.activeLayerIndex = 0;
            refreshUI();
        };
    });

    document.getElementById('btn-copy-level').onclick = () => {
        const sourceLevel = document.getElementById('copy-source-level').value;
        if (!sourceLevel) {
            alert('Please select a level to copy from.');
            return;
        }

        if (sourceLevel === state.currentLevel) {
            alert('Cannot copy level to itself.');
            return;
        }

        if (confirm(`Are you sure you want to overwrite Level ${state.currentLevel} with Level ${sourceLevel}? This cannot be undone.`)) {
            saveState();
            // Deep clone the layers array from source level
            const sourceLayers = state.levels[sourceLevel].layers;
            state.levels[state.currentLevel].layers = JSON.parse(JSON.stringify(sourceLayers));
            
            // Re-sync UI
            state.activeLayerIndex = 0;
            refreshUI();
        }
    };

    // --- Export / Import / Utilities ---

    document.getElementById('btn-download-blank').onclick = () => {
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = 500;
        tempCanvas.height = 700;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.fillStyle = '#FFFFFF';
        tempCtx.fillRect(0, 0, 500, 700);
        
        const link = document.createElement('a');
        link.download = 'brag-card-template-500x700.png';
        link.href = tempCanvas.toDataURL('image/png');
        link.click();
    };

    document.getElementById('grid-toggle').onchange = (e) => {
        state.showGrid = e.target.checked;
        refreshUI();
    };

    document.getElementById('btn-new').onclick = () => {
        if (confirm('Are you sure you want to start a new design? All unsaved progress will be permanently lost and the auto-save will be cleared.')) {
            clearDB();
            state = getDefaultState();
            
            // Remove injected custom font styles from the document
            document.querySelectorAll('style[id^="font-style-"]').forEach(el => el.remove());
            
            renderCustomFontsList();
            syncUIControls();
            refreshUI();
        }
    };

    document.getElementById('btn-restore').onclick = () => {
        if (confirm('Are you sure you want to restore the last auto-saved state? Your current workspace will be overwritten.')) {
            loadFromDB((restoredState) => {
                if (restoredState && restoredState.levels) {
                    state = restoredState;
                    if (state.customFonts) {
                        state.customFonts.forEach(font => injectFont(font.name, font.data));
                    }
                    renderCustomFontsList();
                    syncUIControls();
                    refreshUI();
                    alert('Auto-save restored successfully from database!');
                } else {
                    // Fallback to localStorage just in case it's an old save
                    const savedDesign = localStorage.getItem(AUTOSAVE_KEY);
                    if (savedDesign) {
                        try {
                            const parsed = JSON.parse(savedDesign);
                            if (parsed.levels) {
                                state = parsed;
                                if (state.customFonts) {
                                    state.customFonts.forEach(font => injectFont(font.name, font.data));
                                }
                                renderCustomFontsList();
                                syncUIControls();
                                refreshUI();
                                alert('Auto-save restored successfully from local storage!');
                                return;
                            }
                        } catch(err) {}
                    }
                    alert('No auto-save data found in this browser.');
                }
            });
        }
    };

    document.getElementById('btn-export').onclick = () => {
        let fileName = prompt('Enter a name for your template file:', `premium-template-v${STUDIO_VERSION}`);
        if (fileName === null) return; // Cancelled
        
        if (!fileName.trim()) fileName = `premium-template-v${STUDIO_VERSION}`;
        if (!fileName.toLowerCase().endsWith('.json')) fileName += '.json';

        const dataStr = JSON.stringify(state, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
        
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', fileName);
        linkElement.click();
    };

    function syncUIControls() {
        const lvlRadio = document.querySelector(`input[name="level-select"][value="${state.currentLevel}"]`);
        if (lvlRadio) lvlRadio.checked = true;
        document.getElementById('grid-toggle').checked = state.showGrid || false;
    }

    const importModal = new bootstrap.Modal(document.getElementById('importModal'));
    document.getElementById('btn-import').onclick = () => importModal.show();

    document.getElementById('btn-do-import').onclick = () => {
        const fileInput = document.getElementById('import-file-input');
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const importedState = JSON.parse(e.target.result);
                    // Minimal validation: check if it has levels
                    if (importedState.levels) {
                        const importedVersion = importedState.version || "1.0";
                        
                        // Version Warning/Blocking
                        if (parseFloat(importedVersion) > parseFloat(STUDIO_VERSION)) {
                            if (!confirm(`Warning: This template was created in a newer version of the Studio (v${importedVersion}). Some features may not load correctly in your v${STUDIO_VERSION} editor. Do you want to proceed?`)) {
                                return;
                            }
                        }

                        saveState();
                        state = importedState;
                        state.version = importedVersion; // Ensure version is preserved/backfilled
                        
                        // Inject imported custom fonts
                        if (state.customFonts && state.customFonts.length > 0) {
                            state.customFonts.forEach(font => {
                                injectFont(font.name, font.data);
                            });
                            renderCustomFontsList();
                        }
                        
                        syncUIControls();
                        refreshUI();
                        importModal.hide();
                    } else {
                        alert('Invalid JSON format.');
                    }
                } catch (err) {
                    alert('Error parsing JSON.');
                }
            };
            reader.readAsText(file);
        }
    };

    // --- Initial Load ---
    loadFromDB((restoredState) => {
        if (restoredState && restoredState.levels) {
            state = restoredState;
            if (state.customFonts) {
                state.customFonts.forEach(font => injectFont(font.name, font.data));
            }
            renderCustomFontsList();
            syncUIControls();
            console.log("Auto-save recovered successfully from database.");
        } else {
            // Fallback to local storage for migration
            const savedDesign = localStorage.getItem(AUTOSAVE_KEY);
            if (savedDesign) {
                try {
                    const parsed = JSON.parse(savedDesign);
                    if (parsed.levels) {
                        state = parsed;
                        if (state.customFonts) {
                            state.customFonts.forEach(font => injectFont(font.name, font.data));
                        }
                        renderCustomFontsList();
                        syncUIControls();
                        console.log("Auto-save recovered successfully from local storage.");
                    }
                } catch (e) {
                    console.error("Failed to restore auto-save.", e);
                }
            }
        }
        refreshUI();
    });

    window.onbeforeunload = function(e) {
        // Modern browsers require preventDefault or a return value to trigger the dialog
        // IMPORTANT: The user must have interacted with the page for this to fire.
        e.preventDefault();
        e.returnValue = 'Your changes will be lost if you refresh.';
        return 'Your changes will be lost if you refresh.';
    };
});
