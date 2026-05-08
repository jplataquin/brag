import './bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
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

    hexToHsl(hex) {
        hex = hex.replace(/^#/, '');
        let r = 0, g = 0, b = 0;
        if (hex.length === 3) {
            r = parseInt(hex[0] + hex[0], 16);
            g = parseInt(hex[1] + hex[1], 16);
            b = parseInt(hex[2] + hex[2], 16);
        } else if (hex.length === 6) {
            r = parseInt(hex.substring(0, 2), 16);
            g = parseInt(hex.substring(2, 4), 16);
            b = parseInt(hex.substring(4, 6), 16);
        } else {
            return { h: 0, s: 0, l: 0 };
        }
        r /= 255; g /= 255; b /= 255;
        let max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0; 
        } else {
            let d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return { h: h * 360, s, l };
    }

    startAnimation() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        this.lastTime = performance.now();
        const loop = (time) => {
            if (!this.isAnimating) return;
            if (!document.body.contains(this.canvas)) {
                this.stopAnimation();
                return;
            }
            const dt = time - this.lastTime;
            this.lastTime = time;
            const speed = this.baseWidth * 0.12;
            
            if (this.currentOptions && this.currentOptions.processedTitle && this.currentOptions.processedTitle.length > 20) {
                this.titleOffset -= (speed * dt) / 1000;
            }
            
            this.drawFrame(time);
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
        const isCensored = options.isCensored || false;
        let title = (options.title || 'CARD TITLE').toString().toUpperCase().trim();

        const imageUrl = isCensored ? '' : (options.image || '');
        const rankBadgeUrl = this.getRankBadgeUrl(options.rankLevel || 1, mode, options.badgeVersion, options);

        await Promise.all([
            this.loadImage(imageUrl),
            this.loadImage(rankBadgeUrl)
        ]);

        if (mode === 'thumbnail' && !isFullScreenRender && !options.asThumbnail) {
            if (title.length > 20) title = title.substring(0, 20);
            this.currentOptions.processedTitle = title;
            this.stopAnimation();
            this.titleOffset = 0;
            this.drawFrame();
        } else {
            if (title.length > 50) title = title.substring(0, 50);
            this.currentOptions.processedTitle = title;

            let needsAnimation = false;
            if (title.length > 20) needsAnimation = true;
            
            // Only animate levels if not explicitly forced to thumbnail without fullscreen
            if (!options.asThumbnail && mode !== 'thumbnail') {
                if (options.rankLevel >= 1 && mode !== 'template') needsAnimation = true;
            }

            if (needsAnimation) {
                this.startAnimation();
            } else {
                this.stopAnimation();
                this.titleOffset = 0;
                this.drawFrame(performance.now());
            }
        }
    }

    drawFrame(time) {
        const options = this.currentOptions;
        const ctx = this.ctx;
        const w = this.baseWidth;
        const h = this.baseHeight;
        const mode = options.mode || 'default';
        const t = time || performance.now();
        const isCensored = options.isCensored || false;
        
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        
        let currentBgColor = options.backgroundColor || '#0a0a1a';
        let currentBorderColor = options.borderColor || '#00f0ff';
        let currentSectionColor = options.sectionColor || '#111122';
        const primaryTextColor = options.primaryTextColor || '#ffffff';
        const secondaryTextColor = options.secondaryTextColor || '#dddddd';
        
        let glowBlur = 0;
        let glowColor = currentBorderColor;

        if (isCensored) {
            currentBgColor = '#1a0a0a';
            currentBorderColor = '#ff4444';
            currentSectionColor = '#2a1111';
            glowColor = '#ff4444';
            glowBlur = 5;
        } else if (options.rankLevel && mode !== 'template' && !options.asThumbnail && mode !== 'thumbnail') {
            const level = options.rankLevel;
            const borderHsl = this.hexToHsl(options.borderColor || '#00f0ff');
            const sectionHsl = this.hexToHsl(options.sectionColor || '#111122');
            const bgHsl = this.hexToHsl(options.backgroundColor || '#0a0a1a');

            const lerp = (a, b, f) => a + (b - a) * f;
            const phase = (Math.sin(t / 2000) + 1) / 2; // 0 to 1 over 4 seconds

            if (level >= 1) {
                glowBlur = 4 + 3 * Math.sin(t / 1000);
                glowColor = `hsl(${borderHsl.h}, ${borderHsl.s * 100}%, ${borderHsl.l * 100}%)`;
            }
            if (level >= 2) {
                let lShift = 4 * Math.sin(t / 1500);
                currentSectionColor = `hsl(${sectionHsl.h}, ${sectionHsl.s * 100}%, ${Math.max(0, Math.min(100, sectionHsl.l * 100 + lShift))}%)`;
            }
            if (level >= 3) {
                // Level 3: Phase border color slightly towards background color
                let h = lerp(borderHsl.h, bgHsl.h, phase * 0.2);
                let s = lerp(borderHsl.s, bgHsl.s, phase * 0.2);
                let l = lerp(borderHsl.l, bgHsl.l, phase * 0.2);
                currentBorderColor = `hsl(${h}, ${s * 100}%, ${l * 100}%)`;
                glowColor = currentBorderColor;
                glowBlur = 6 + 4 * Math.sin(t / 1000);
            }
            if (level >= 4) {
                // Level 4: Full color phasing between Border and Background
                let hB = lerp(borderHsl.h, bgHsl.h, phase * 0.3);
                let sB = lerp(borderHsl.s, bgHsl.s, phase * 0.3);
                let lB = lerp(borderHsl.l, bgHsl.l, phase * 0.3);
                currentBorderColor = `hsl(${hB}, ${sB * 100}%, ${lB * 100}%)`;

                let hBg = lerp(bgHsl.h, borderHsl.h, phase * 0.15);
                let sBg = lerp(bgHsl.s, borderHsl.s, phase * 0.15);
                let lBg = lerp(bgHsl.l, borderHsl.l, phase * 0.15);
                currentBgColor = `hsl(${hBg}, ${sBg * 100}%, ${lBg * 100}%)`;

                let hS = lerp(sectionHsl.h, borderHsl.h, phase * 0.2);
                currentSectionColor = `hsl(${hS}, ${sectionHsl.s * 100}%, ${sectionHsl.l * 100}%)`;

                glowBlur = 12 + 8 * Math.sin(t / 1200);
                glowColor = `hsl(${borderHsl.h}, 100%, 70%)`;
            }
            if (level >= 5) {
                // Level 5: GOAT - Rainbow shifting and intense glow
                const rainbowH = (t / 20) % 360;
                currentBorderColor = `hsl(${rainbowH}, 100%, 50%)`;
                glowColor = `hsl(${rainbowH}, 100%, 60%)`;
                glowBlur = 20 + 10 * Math.sin(t / 800);
                
                // Pulsing background
                const bgPulse = 5 + 5 * Math.sin(t / 1000);
                currentBgColor = `hsl(${bgHsl.h}, ${bgHsl.s * 100}%, ${Math.min(100, bgHsl.l * 100 + bgPulse)}%)`;
            }
        }

        const title = isCensored ? '[CENSORED]' : (options.processedTitle || 'CARD TITLE');
        const game = (options.game || 'GAME').toString().trim();
        const creator = (options.creator || 'Creator').toString().trim();
        const quote = isCensored ? '[Content hidden pending review]' : (options.quote || 'No quote available.');
        const imageUrl = isCensored ? '' : (options.image || '');
        const bw = Math.max(8, w * 0.02);
        const pad = Math.max(12, w * 0.04);
        const spacing = Math.max(10, h * 0.03);
        
        ctx.clearRect(0, 0, w, h);
        ctx.fillStyle = currentBorderColor;
        ctx.fillRect(0, 0, w, h);
        ctx.fillStyle = currentBgColor;
        ctx.fillRect(bw, bw, w - bw * 2, h - bw * 2);
        


        
        const innerX = bw + pad;
        const innerW = w - (bw + pad) * 2;
        const titleY = bw + pad;
        const titleH = h * 0.16;
        const photoY = titleY + titleH + spacing;
        const photoH = h * 0.40; // Reduced photo height slightly to fit taller stats
        const statsY = photoY + photoH + spacing;
        const statsH = h * 0.08; // Increased stats height
        const descY = statsY + statsH + spacing;
        const descH = h - descY - bw - pad;
        const sectionRadius = Math.floor(w * 0.02);
        ctx.lineWidth = 2;
        
        const sections = [{ y: titleY, h: titleH }, { y: photoY, h: photoH }, { y: statsY, h: statsH }, { y: descY, h: descH }];
        sections.forEach(sec => {
            this.createRoundRectPath(ctx, innerX, sec.y, innerW, sec.h, sectionRadius);
            ctx.fillStyle = currentSectionColor;
            ctx.fill();
            
            ctx.save();
            if (glowBlur > 0) {
                ctx.shadowColor = glowColor;
                ctx.shadowBlur = glowBlur;
            }
            ctx.strokeStyle = currentBorderColor;
            ctx.stroke();
            ctx.restore();
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
        ctx.fillStyle = isCensored ? '#ff4444' : primaryTextColor;
        ctx.font = `bold ${fontSizeTitle}px sans-serif`;
        if (this.isAnimating && title.length > 20 && !isCensored) {
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

        const fontSizeSerial = Math.floor(fontSizeGame * 0.85);
        ctx.fillStyle = isCensored ? 'rgba(255, 68, 68, 0.6)' : primaryTextColor;
        ctx.font = `bold ${fontSizeSerial}px 'Orbitron', sans-serif`;
        if (options.mode === 'template') {
            ctx.fillText('#00000', textStartX, titleY + titleH * 0.52);
        } else if (options.serialNumber !== null && options.serialNumber !== undefined) {
            const paddedSerial = '#' + String(options.serialNumber).padStart(5, '0');
            ctx.fillText(paddedSerial, textStartX, titleY + titleH * 0.52);
        }

        ctx.fillStyle = isCensored ? 'rgba(255, 68, 68, 0.4)' : secondaryTextColor;
        ctx.font = `italic ${fontSizeGame}px sans-serif`;
        ctx.fillText(game, textStartX, titleY + titleH * 0.72);

        ctx.restore();
        
        // --- Custom Stats Section ---
        ctx.save();
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        
        // Setup stats values
        const wins = options.wins || 0;
        const losses = options.losses || 0;
        const lifePoints = options.lifePoints !== undefined ? options.lifePoints : 3;
        const totalGames = wins + losses;
        let winRateStr = totalGames > 0 ? Math.round((wins / totalGames) * 100) + '%' : '0%';
        if (options.mode === 'template') winRateStr = '0%';
        const status = options.status || 'Maintained';
        const rarityIcon = isCensored ? '🚫' : (options.rarityIcon || '🪵');
        
        let startX = innerX + (w * 0.04);
        const yCenterTopLine = statsY + statsH * 0.32;
        const yCenterBottomLine = statsY + statsH * 0.83;

        // --- TOP LINE ---
        // Rarity Badge (emoji icon)
        ctx.font = `${fontSizeStats}px sans-serif`;
        ctx.fillText(rarityIcon, startX, yCenterTopLine);
        startX += ctx.measureText(rarityIcon).width + fontSizeStats * 0.5;

        // Wins
        ctx.fillStyle = isCensored ? '#555' : '#39ff14'; // Lime Green
        ctx.font = `bold ${fontSizeStats}px sans-serif`;
        ctx.fillText(`W: ${wins}`, startX, yCenterTopLine);
        startX += ctx.measureText(`W: ${wins}`).width + fontSizeStats * 0.8;

        // Losses
        ctx.fillStyle = isCensored ? '#555' : '#ff0000'; // Red
        ctx.fillText(`L: ${losses}`, startX, yCenterTopLine);
        startX += ctx.measureText(`L: ${losses}`).width + fontSizeStats * 0.8;

        // Win Rate
        ctx.fillStyle = isCensored ? '#555' : '#00f0ff'; // Cyan
        ctx.fillText(`R: ${winRateStr}`, startX, yCenterTopLine);
        startX += ctx.measureText(`R: ${winRateStr}`).width + fontSizeStats * 0.8;

        // Integrity
        let integrityStatStr = (options.integrityStat || 0) + '%';
        if (options.mode === 'template') integrityStatStr = '0%';
        ctx.fillStyle = isCensored ? '#555' : '#ffdd00'; // Yellow
        ctx.fillText(`I: ${integrityStatStr}`, startX, yCenterTopLine);
        startX += ctx.measureText(`I: ${integrityStatStr}`).width + fontSizeStats * 0.8;

        // Discontinued Badge
        if (status === 'Discontinued' && !isCensored) {
            ctx.fillStyle = '#ff00ff'; // Magenta
            ctx.font = `bold ${fontSizeStats * 1.1}px sans-serif`;
            ctx.fillText('⚠️', startX, yCenterTopLine);
        }
        
        // --- BOTTOM LINE ---
        startX = innerX + (w * 0.04);
        
        // Life Points
        ctx.fillStyle = '#ffffff';
        ctx.font = `${fontSizeStats * 0.8}px sans-serif`;
        let lifePointsStr = '';
        
        if (lifePoints === 0) {
            lifePointsStr = '💀';
            ctx.font = `${fontSizeStats}px sans-serif`;
        } else {
            for(let i = 0; i < lifePoints; i++) {
                lifePointsStr += '❤️';
            }
        }
        
        // If template, just show default 3
        if (options.mode === 'template') lifePointsStr = '❤️❤️❤️';
        if (isCensored) {
            ctx.fillStyle = '#555';
            lifePointsStr = '---';
        }
        ctx.fillText(lifePointsStr, startX, yCenterBottomLine);

        ctx.restore();
        // ----------------------------

        ctx.save();
        ctx.fillStyle = isCensored ? 'rgba(255, 68, 68, 0.4)' : secondaryTextColor;
        ctx.font = `${fontSizeDesc}px sans-serif`;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        const year = options.year || new Date().getFullYear();
        const formattedQuote = isCensored ? quote : `"${quote}" — ${creator} (${year})`;
        this.wrapText(ctx, formattedQuote, textStartX, descY + (h * 0.02), innerW - (w * 0.08), descH - (h * 0.04), fontSizeDesc * 1.4);
        ctx.restore();

        const currentMode = options.mode || 'default';
        const rankBadgeUrl = this.getRankBadgeUrl(options.rankLevel || 1, currentMode, options.badgeVersion, options);
        const photoImg = isCensored ? null : this.imageCache[imageUrl];
        const badgeImg = isCensored ? null : this.imageCache[rankBadgeUrl];
        if (photoImg) {
            this.drawImageWithinBounds(ctx, photoImg, innerX, photoY, innerW, photoH, currentBorderColor, currentMode, options.imagePositionY !== undefined ? options.imagePositionY : 50, sectionRadius);
        } else if (isCensored) {
            ctx.save();
            this.createRoundRectPath(ctx, innerX, photoY, innerW, photoH, sectionRadius);
            ctx.clip();
            ctx.fillStyle = '#2a1111';
            ctx.fillRect(innerX, photoY, innerW, photoH);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = `bold ${h * 0.05}px sans-serif`;
            ctx.fillStyle = '#ff4444';
            ctx.fillText('CONTENT HIDDEN', innerX + innerW / 2, photoY + photoH / 2 - 10);
            ctx.font = `${h * 0.025}px sans-serif`;
            ctx.fillText('PENDING REVIEW', innerX + innerW / 2, photoY + photoH / 2 + 25);
            ctx.restore();
        }

        if (badgeImg) {
            const badgeSize = w * 0.25;
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
        
        // --- Watermarks (Burned / Surrendered / Censored) drawn LAST to be on top ---
        if (isCensored) {
            ctx.save();
            ctx.translate(w / 2, h / 2);
            ctx.rotate(-Math.PI / 6);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = `bold ${h * 0.1}px 'Orbitron', sans-serif`;
            ctx.fillStyle = 'rgba(255, 68, 68, 0.2)';
            ctx.strokeStyle = 'rgba(255, 68, 68, 0.4)';
            ctx.lineWidth = 2;
            ctx.strokeText("CENSORED", 0, 0);
            ctx.fillText("CENSORED", 0, 0);
            ctx.restore();
        } else if (options.burned) {
