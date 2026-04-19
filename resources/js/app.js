import './bootstrap';
import * as bootstrap from 'bootstrap';

// Make bootstrap available globally for the alert auto-dismiss script
window.bootstrap = bootstrap;

class DigitalCardRenderer {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error(`Canvas with id ${canvasId} not found.`);
            return;
        }
        this.ctx = this.canvas.getContext('2d');
        this.imageCache = {};
        this.titleOffset = 0;
        this.isAnimating = false;
        this.animationFrameId = null;
        this.lastTime = 0;
        this.baseWidth = this.canvas.width;
        this.baseHeight = this.canvas.height;
        this.setupHighDPI();
    }

    setupHighDPI() {
        const dpr = window.devicePixelRatio || 1;
        this.canvas.width = this.baseWidth * dpr;
        this.canvas.height = this.baseHeight * dpr;
        this.ctx.scale(dpr, dpr);
        this.canvas.style.width = this.baseWidth + 'px';
        this.canvas.style.maxWidth = '100%';
        this.canvas.style.height = 'auto';
    }

    startAnimation() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        this.lastTime = performance.now();
        const loop = (time) => {
            if (!this.isAnimating) return;
            const dt = time - this.lastTime;
            this.lastTime = time;
            const speed = this.baseWidth * 0.12;
            this.titleOffset -= (speed * dt) / 1000;
            this.drawFrame();
            this.animationFrameId = requestAnimationFrame(loop);
        };
        this.animationFrameId = requestAnimationFrame(loop);
    }

    stopAnimation() {
        this.isAnimating = false;
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        }
    }

    async draw(options) {
        this.currentOptions = options;
        const mode = options.mode || 'default';
        const isFullScreenRender = options.isFullScreenRender || false;
        let title = (options.title || 'CARD TITLE').toString().toUpperCase().trim();

        const imageUrl = options.image || '';
        const rankBadgeUrl = this.getRankBadgeUrl(options.rankLevel || 1, mode);

        await Promise.all([
            this.loadImage(imageUrl),
            this.loadImage(rankBadgeUrl)
        ]);

        if (mode === 'thumbnail' && !isFullScreenRender) {
            if (title.length > 20) title = title.substring(0, 20);
            this.currentOptions.processedTitle = title;
            this.stopAnimation();
            this.titleOffset = 0;
            this.drawFrame();
        } else {
            if (title.length > 50) title = title.substring(0, 50);
            this.currentOptions.processedTitle = title;

            if (title.length > 20) {
                this.startAnimation();
            } else {
                this.stopAnimation();
                this.titleOffset = 0;
                this.drawFrame();
            }
        }
    }

    drawFrame() {
        const options = this.currentOptions;
        const ctx = this.ctx;
        const w = this.baseWidth;
        const h = this.baseHeight;
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        const bgColor = options.backgroundColor || '#0a0a1a';
        const borderColor = options.borderColor || '#00f0ff';
        const sectionColor = options.sectionColor || '#111122';
        const primaryTextColor = options.primaryTextColor || '#ffffff';
        const secondaryTextColor = options.secondaryTextColor || '#dddddd';
        const title = options.processedTitle || 'CARD TITLE';
        const game = (options.game || 'GAME').toString().trim();
        const creator = (options.creator || 'Creator').toString().trim();
        const quote = options.quote || 'No quote available.';
        const imageUrl = options.image || '';
        const statsText = (options.statsText || 'LVL 1 • W: 0 • L: 0 • COPIES: 1').toString().trim();
        const bw = Math.max(8, w * 0.02);
        const pad = Math.max(12, w * 0.04);
        const spacing = Math.max(10, h * 0.03);
        ctx.clearRect(0, 0, w, h);
        ctx.fillStyle = borderColor;
        ctx.fillRect(0, 0, w, h);
        ctx.fillStyle = bgColor;
        ctx.fillRect(bw, bw, w - bw * 2, h - bw * 2);
        const innerX = bw + pad;
        const innerW = w - (bw + pad) * 2;
        const titleY = bw + pad;
        const titleH = h * 0.16;
        const photoY = titleY + titleH + spacing;
        const photoH = h * 0.42;
        const statsY = photoY + photoH + spacing;
        const statsH = h * 0.06;
        const descY = statsY + statsH + spacing;
        const descH = h - descY - bw - pad;
        const sectionRadius = Math.floor(w * 0.02);
        ctx.lineWidth = 2;
        const sections = [{ y: titleY, h: titleH }, { y: photoY, h: photoH }, { y: statsY, h: statsH }, { y: descY, h: descH }];
        sections.forEach(sec => {
            this.createRoundRectPath(ctx, innerX, sec.y, innerW, sec.h, sectionRadius);
            ctx.fillStyle = sectionColor;
            ctx.fill();
            ctx.strokeStyle = borderColor;
            ctx.stroke();
        });
        const fontSizeTitle = Math.floor(h * 0.035);
        const fontSizeGame = Math.floor(h * 0.025);
        const fontSizeStats = Math.floor(h * 0.025);
        const fontSizeDesc = Math.floor(h * 0.025);
        ctx.save();
        ctx.beginPath();
        this.createRoundRectPath(ctx, innerX, titleY, innerW, titleH, sectionRadius);
        ctx.clip();
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const textStartX = innerX + (w * 0.04);
        ctx.fillStyle = primaryTextColor;
        ctx.font = `bold ${fontSizeTitle}px sans-serif`;
        if (this.isAnimating) {
            const textWidth = ctx.measureText(title).width;
            const gap = w * 0.2;
            ctx.fillText(title, textStartX + this.titleOffset, titleY + titleH * 0.28);
            ctx.fillText(title, textStartX + this.titleOffset + textWidth + gap, titleY + titleH * 0.28);
            if (-this.titleOffset >= textWidth + gap) {
                this.titleOffset = 0;
            }
        } else {
            ctx.fillText(title, textStartX, titleY + titleH * 0.28);
        }
        ctx.fillStyle = secondaryTextColor;
        ctx.font = `italic ${fontSizeGame}px sans-serif`;
        ctx.fillText(game, textStartX, titleY + titleH * 0.55);

        const fontSizeSerial = Math.floor(fontSizeGame * 0.85);
        ctx.fillStyle = primaryTextColor;
        ctx.font = `bold ${fontSizeSerial}px 'Orbitron', sans-serif`;
        if (options.serialNumber) {
            const paddedSerial = '#' + String(options.serialNumber).padStart(5, '0');
            ctx.fillText(paddedSerial, textStartX, titleY + titleH * 0.72);
        }

        const fontSizeCreator = Math.floor(fontSizeGame * 0.85);
        ctx.fillStyle = secondaryTextColor;
        ctx.font = `${fontSizeCreator}px sans-serif`;
        ctx.fillText(`By ${creator}`, textStartX, titleY + titleH * 0.88);
        ctx.restore();
        ctx.save();
        ctx.fillStyle = primaryTextColor;
        ctx.font = `bold ${fontSizeStats}px sans-serif`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(statsText, innerX + innerW / 2, statsY + statsH / 2);
        ctx.restore();
        ctx.save();
        ctx.fillStyle = secondaryTextColor;
        ctx.font = `${fontSizeDesc}px sans-serif`;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        this.wrapText(ctx, quote, textStartX, descY + (h * 0.02), innerW - (w * 0.08), descH - (h * 0.04), fontSizeDesc * 1.4);
        ctx.restore();
        const currentMode = options.mode || 'default';
        const rankBadgeUrl = this.getRankBadgeUrl(options.rankLevel || 1, currentMode);
        const photoImg = this.imageCache[imageUrl];
        const badgeImg = this.imageCache[rankBadgeUrl];
        if (photoImg) {
            this.drawImageWithinBounds(ctx, photoImg, innerX, photoY, innerW, photoH, borderColor, currentMode);
        }
        if (badgeImg) {
            const badgeSize = w * 0.32;
            const bx = (w * 0.05) - innerX + innerW - badgeSize * 0.5;
            const by = titleY - badgeSize * 0.3;
            ctx.save();
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            if (currentMode === 'template') {
                ctx.shadowColor = 'rgba(255,221,0,0.4)';
                ctx.shadowBlur = 10;
            } else {
                ctx.shadowColor = 'rgba(0,0,0,0.6)';
                ctx.shadowBlur = 8;
                ctx.shadowOffsetY = 4;
            }
            ctx.drawImage(badgeImg, bx, by, badgeSize, badgeSize);
            ctx.restore();
        }
        if (currentMode === 'thumbnail') {
            const imgEl = document.getElementById('img_' + this.canvas.id);
            if (imgEl) {
                imgEl.src = this.canvas.toDataURL('image/png');
            }
        }
    }

    loadImage(src) {
        return new Promise((resolve) => {
            if (!src) return resolve(null);
            if (this.imageCache[src]) return resolve(this.imageCache[src]);
            const img = new Image();
            img.crossOrigin = "Anonymous";
            img.onload = () => {
                this.imageCache[src] = img;
                resolve(img);
            };
            img.onerror = () => resolve(null);
            img.src = src;
        });
    }

    getRankBadgeUrl(level, mode) {
        if (mode === 'template') {
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="30" fill="#1a1800" stroke="#ffdd00" stroke-width="4"/>
                <path d="M16 48 L48 48 L46 40 L18 40 Z" fill="#ffdd00"/>
                <path d="M22 40 L42 40 L40 28 L20 28 C 24 28 26 34 22 40" fill="#ffdd00"/>
                <path d="M12 28 C 12 28 16 22 24 24 L48 24 L48 28 Z" fill="#ffdd00"/>
                <path d="M38 10 L52 24 L48 28 L34 14 Z" fill="#ffdd00"/>
                <path d="M28 16 L40 4 L46 10 L34 22 Z" fill="#ffdd00"/>
                </svg>`;
            return 'data:image/svg+xml;base64,' + btoa(svg);
        }
        const badges = {
            1: '/build/assets/lv1-nXVxY6jR.png',
            2: '/build/assets/lv2-CbCEwos4.png',
            3: '/build/assets/lv3-DKLQZVa4.png',
            4: '/build/assets/lv4-BXpA1DTa.png'
        };
        return badges[level] || badges[1];
    }

    drawImageWithinBounds(ctx, img, x, y, w, h, borderColor, mode) {
        const sRatio = img.width / img.height;
        const dRatio = w / h;
        let sx = 0, sy = 0, sw = img.width, sh = img.height;
        if (sRatio > dRatio) {
            sw = img.height * dRatio;
            sx = (img.width - sw) / 2;
        } else {
            sh = img.width / dRatio;
            sy = (img.height - sh) / 2;
        }
        const radius = Math.floor(ctx.canvas.width * 0.02);
        ctx.save();
        this.createRoundRectPath(ctx, x, y, w, h, radius);
        ctx.clip();
        if (mode === 'template') {
            ctx.filter = 'grayscale(100%)';
        }
        ctx.drawImage(img, sx, sy, sw, sh, x, y, w, h);
        if (mode === 'template') {
            ctx.filter = 'none';
            ctx.globalCompositeOperation = 'multiply';
            ctx.fillStyle = '#ffdd00';
            ctx.fillRect(x, y, w, h);
        }
        ctx.restore();
        ctx.save();
        this.createRoundRectPath(ctx, x, y, w, h, radius);
        ctx.strokeStyle = borderColor;
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();
    }

    createRoundRectPath(ctx, x, y, w, h, r) {
        if (w < 2 * r) r = w / 2;
        if (h < 2 * r) r = h / 2;
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    wrapText(context, text, x, y, maxWidth, maxHeight, lineHeight) {
        const paragraphs = text.split('\\n');
        let currentY = y;
        const maxY = y + maxHeight;
        for (let p = 0; p < paragraphs.length; p++) {
            const words = paragraphs[p].split(' ');
            let line = '';
            for (let n = 0; n < words.length; n++) {
                let word = words[n];
                let testLine = line + word + ' ';
                let metrics = context.measureText(testLine);
                if (metrics.width > maxWidth) {
                    if (line !== '') {
                        if (currentY + lineHeight > maxY - lineHeight) {
                            context.fillText(line.trim() + '...', x, currentY);
                            return;
                        }
                        context.fillText(line.trim(), x, currentY);
                        line = '';
                        currentY += lineHeight;
                    }
                    if (context.measureText(word + ' ').width > maxWidth) {
                        let tempWord = '';
                        for (let c = 0; c < word.length; c++) {
                            let char = word[c];
                            let hyphen = (c < word.length - 1) ? '-' : '';
                            let testWord = tempWord + char + hyphen;
                            if (context.measureText(testWord).width > maxWidth && tempWord.length > 0) {
                                if (currentY + lineHeight > maxY - lineHeight) {
                                    context.fillText(tempWord + '...', x, currentY);
                                    return;
                                }
                                context.fillText(tempWord + '-', x, currentY);
                                tempWord = char;
                                currentY += lineHeight;
                            } else {
                                tempWord += char;
                            }
                        }
                        line = tempWord + ' ';
                    } else {
                        line = word + ' ';
                    }
                } else {
                    line = testLine;
                }
            }
            if (currentY + lineHeight > maxY) {
                if (line.trim() !== '') context.fillText(line.trim() + '...', x, currentY);
                return;
            }
            if (line.trim() !== '') {
                context.fillText(line.trim(), x, currentY);
            }
            currentY += lineHeight;
            if (currentY > maxY) return;
        }
    }
}

window.DigitalCardRenderer = DigitalCardRenderer;

window.neonAlert = function(message, title = 'ALERT') {
    return new Promise((resolve) => {
        document.getElementById('globalNeonAlertTitle').innerText = title;
        document.getElementById('globalNeonAlertMessage').innerText = message;
        const modalEl = document.getElementById('globalNeonAlertModal');
        const modal = new bootstrap.Modal(modalEl);
        
        const onHidden = () => {
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            resolve();
        };
        modalEl.addEventListener('hidden.bs.modal', onHidden);
        modal.show();
    });
};

window.neonConfirm = function(message, title = 'CONFIRM') {
    return new Promise((resolve) => {
        document.getElementById('globalNeonConfirmTitle').innerText = title;
        document.getElementById('globalNeonConfirmMessage').innerText = message;
        const modalEl = document.getElementById('globalNeonConfirmModal');
        const modal = new bootstrap.Modal(modalEl);
        
        let result = false;
        
        const confirmBtn = document.getElementById('globalNeonConfirmBtn');
        const onConfirm = () => {
            result = true;
            modal.hide();
        };
        confirmBtn.addEventListener('click', onConfirm);
        
        const onHidden = () => {
            confirmBtn.removeEventListener('click', onConfirm);
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            resolve(result);
        };
        modalEl.addEventListener('hidden.bs.modal', onHidden);
        
        modal.show();
    });
};

window.neonPrompt = function(message, defaultValue = '', title = 'PROMPT') {
    return new Promise((resolve) => {
        document.getElementById('globalNeonPromptTitle').innerText = title;
        document.getElementById('globalNeonPromptMessage').innerText = message;
        const input = document.getElementById('globalNeonPromptInput');
        input.value = defaultValue;
        const modalEl = document.getElementById('globalNeonPromptModal');
        const modal = new bootstrap.Modal(modalEl);
        
        let result = null;
        
        const submitBtn = document.getElementById('globalNeonPromptBtn');
        const onSubmit = () => {
            result = input.value;
            modal.hide();
        };
        submitBtn.addEventListener('click', onSubmit);
        
        const onHidden = () => {
            submitBtn.removeEventListener('click', onSubmit);
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            resolve(result);
        };
        modalEl.addEventListener('hidden.bs.modal', onHidden);
        
        modal.show();
        modalEl.addEventListener('shown.bs.modal', () => input.focus(), { once: true });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-confirm]');
        if (!trigger) return;
        
        e.preventDefault();
        
        if (trigger.dataset.confirmed === 'true') {
            return; 
        }
        
        const message = trigger.dataset.confirm;
        const result = await window.neonConfirm(message);
        
        if (result) {
            trigger.dataset.confirmed = 'true';
            if (trigger.tagName === 'A') {
                window.location.href = trigger.href;
            } else if (trigger.tagName === 'BUTTON' && trigger.type === 'submit') {
                const form = trigger.closest('form');
                if (form) form.submit();
            } else if (trigger.tagName === 'FORM') {
                trigger.submit();
            } else {
                trigger.click();
            }
            setTimeout(() => { delete trigger.dataset.confirmed; }, 100);
        }
    });
});

// Process images with Vite
import.meta.glob([
    '../img/**',
]);
