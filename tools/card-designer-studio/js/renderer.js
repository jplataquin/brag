class StudioRenderer {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.width = this.canvas.width;
        this.height = this.canvas.height;
    }

    clear() {
        this.ctx.clearRect(0, 0, this.width, this.height);
    }

    async draw(config, activeLayerIndex = -1) {
        this.clear();
        if (!config || !config.layers) return;

        for (let i = config.layers.length - 1; i >= 0; i--) {
            const layer = config.layers[i];
            if (layer.visible === false) continue;
            
            await this.renderLayer(layer);
            
            // Draw highlight if it's the active layer in the studio
            if (i === activeLayerIndex) {
                this.drawHighlight(layer);
            }
        }
    }

    async renderLayer(layer) {
        switch (layer.type) {
            case 'image':
                await this.drawAsset(layer);
                break;
            case 'photo':
                this.drawPhotoPlaceholder(layer);
                break;
            case 'text':
                this.drawTextElements(layer);
                break;
        }
    }

    async drawAsset(layer) {
        if (!layer.data) return;
        
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                const w = layer.width || this.width;
                const h = layer.height || this.height;
                this.ctx.drawImage(img, layer.x || 0, layer.y || 0, w, h);
                resolve();
            };
            img.onerror = () => resolve();
            img.src = layer.data;
        });
    }

    drawPhotoPlaceholder(layer) {
        const { x, y, width, height, shape } = layer;
        const currentShape = shape || 'rectangle';
        
        this.ctx.setLineDash([5, 5]);
        this.ctx.strokeStyle = '#00f0ff';
        this.ctx.lineWidth = 2;
        this.ctx.fillStyle = 'rgba(0, 240, 255, 0.1)';

        this.ctx.beginPath();
        if (currentShape === 'circle') {
            const centerX = x + width / 2;
            const centerY = y + height / 2;
            const radius = Math.min(width, height) / 2;
            this.ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
        } else {
            // Square or Rectangle (Portrait)
            this.ctx.rect(x, y, width, height);
        }
        
        this.ctx.fill();
        this.ctx.stroke();
        this.ctx.setLineDash([]);

        // Text in middle
        this.ctx.fillStyle = '#00f0ff';
        this.ctx.font = '14px Orbitron';
        this.ctx.textAlign = 'center';
        this.ctx.fillText('USER PHOTO', x + width / 2, y + height / 2);
    }

    drawTextElements(layer) {
        if (!layer.elements) return;

        Object.entries(layer.elements).forEach(([key, el]) => {
            if (!el.visible) return;

            this.ctx.fillStyle = el.color || '#ffffff';
            this.ctx.font = `${el.weight || 'normal'} ${el.size || 16}px ${el.font || 'Arial'}`;
            this.ctx.textAlign = el.align || 'left';
            
            let text = el.content || this.getPlaceholderText(key);
            text = this.parseTemplate(text);
            
            this.ctx.fillText(text, el.x, el.y);
        });
    }

    parseTemplate(content) {
        const data = {
            title: 'CARD TITLE',
            game: 'GAME TITLE',
            serial: '#001',
            wins: '10',
            losses: '2',
            rate: '83%',
            integrity: '85',
            health: '❤️❤️❤️',
            quote: 'This is a sample card quote for the designer studio.',
            creator: 'CREATOR_NAME',
            year: '2026'
        };

        return content.replace(/\{(\w+)\}/g, (match, key) => {
            return data[key] !== undefined ? data[key] : match;
        });
    }

    getPlaceholderText(key) {
        const placeholders = {
            card_title: 'CARD TITLE',
            game_title: 'GAME TITLE',
            serial: '#001',
            wins: 'W: 10',
            losses: 'L: 2',
            rate: 'R: 83%',
            integrity: 'I: 85%',
            health: 'HP: 3/3',
            quote: 'This is a sample card quote for the designer studio.',
            creator: 'CREATOR_NAME',
            year: '2026'
        };
        return placeholders[key] || key.toUpperCase();
    }

    drawHighlight(layer) {
        this.ctx.strokeStyle = '#ff00ff';
        this.ctx.lineWidth = 3;
        
        if (layer.type === 'photo') {
            this.ctx.strokeRect(layer.x - 2, layer.y - 2, layer.width + 4, layer.height + 4);
        } else if (layer.type === 'text') {
            // Highlighting text layer is harder since it has multiple elements.
            // Maybe just highlight the bounding box of all visible elements?
            // For now, let's just highlight the active element if we add that.
        }
    }

    drawGrid(step = 50) {
        this.ctx.beginPath();
        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
        this.ctx.lineWidth = 1;

        for (let x = 0; x <= this.width; x += step) {
            this.ctx.moveTo(x, 0);
            this.ctx.lineTo(x, this.height);
        }

        for (let y = 0; y <= this.height; y += step) {
            this.ctx.moveTo(0, y);
            this.ctx.lineTo(this.width, y);
        }

        this.ctx.stroke();

        // Optional: Major lines at every 100px
        this.ctx.beginPath();
        this.ctx.strokeStyle = 'rgba(0, 240, 255, 0.2)';
        this.ctx.lineWidth = 1;
        for (let x = 0; x <= this.width; x += step * 2) {
            this.ctx.moveTo(x, 0);
            this.ctx.lineTo(x, this.height);
        }
        for (let y = 0; y <= this.height; y += step * 2) {
            this.ctx.moveTo(0, y);
            this.ctx.lineTo(this.width, y);
        }
        this.ctx.stroke();
    }

    drawGuides(x, y) {
        this.ctx.beginPath();
        this.ctx.strokeStyle = 'rgba(255, 0, 255, 0.5)'; // Magenta
        this.ctx.lineWidth = 1;
        this.ctx.setLineDash([2, 2]);

        // Horizontal line
        this.ctx.moveTo(0, y);
        this.ctx.lineTo(this.width, y);

        // Vertical line
        this.ctx.moveTo(x, 0);
        this.ctx.lineTo(x, this.height);

        this.ctx.stroke();
        this.ctx.setLineDash([]);
        
        // Draw coordinate text
        this.ctx.fillStyle = 'rgba(255, 0, 255, 0.8)';
        this.ctx.font = '10px Arial';
        this.ctx.fillText(`X: ${Math.round(x)}, Y: ${Math.round(y)}`, x + 5, y - 5);
    }
}
