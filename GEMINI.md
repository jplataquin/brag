# Brag - The Ultimate Gamer Card-Battling Social Platform

## About The Application
"Brag" is a sleek, competitive social platform where gamers can forge unique "Digital Cards" from customizable "Templates" and use them as stakes in PvP (Player vs. Player) battles. 

### Core Features
- **Templates & Forging:** Users can create unique templates (one per Game Title). From these templates, they can forge a "Digital Card"—a unique trophy.
- **Digital Cards:** These act as the core stakes of the platform. Each card tracks its own stats (Level, Wins, Losses, and Copies in Circulation), determining its rarity and social worth. Users can keep a maximum of three per game title of their own forged cards in their inventory.
- **The Arena & Battles:** Users can join or create matches ("Battles") to bet their digital cards. 
- **Marshalls:** Battles can optionally have an "Marshall"—a neutral third-party user invited to oversee the match and declare the winner.
- **Trophy Collection:** When a user loses a match, their staked digital card is transferred to the winner. Winners collect these cards as trophies to showcase their dominance.
- **Profiles & Social:** Users have dedicated profile pages to showcase their available digital cards, display their collected trophies, and search for other users.
- **Real-Time Rooms:** Battle rooms feature real-time updates (powered by Laravel Reverb WebSockets) for joining, starting, and resolving matches, complete with live activity logs and in-game notifications.
- **AI Integration:** Users can enhance their template display photos using text-to-image AI generation (powered by Nano Banana / Gemini).

## Technology Stack
- **Backend:** PHP 8+, Laravel 12+
- **Database:** MySQL 8+
- **Frontend:** Blade Templating, Vanilla JavaScript, Bootstrap 5.3+ (No Tailwind)
- **Real-Time:** Laravel Echo, Pusher JS, Laravel Reverb (WebSockets)
- **Assets:** Vite, SASS

## UI / UX & Design System
The application strictly adheres to a **Gamer Neon Aesthetic**. The design feels modern, competitive, and "alive."

### Styling Guidelines
- **Color Palette:** Deep, dark backgrounds (`#0a0a1a`, `#111122`) contrasted with vibrant, glowing neon accents (`#00f0ff` cyan, `#ff00ff` magenta, `#39ff14` lime green, and `#ffdd00` yellow).
- **Typography:** 
  - Headings and structural text use the **Orbitron** sans-serif font for a futuristic, sci-fi feel.
  - Body text uses clean, readable sans-serif fonts.
- **Components:**
  - **Cards & Modals:** Utilize "glassmorphism" effects (`backdrop-filter: blur()`), semi-transparent dark backgrounds, and thin neon borders.
  - **Buttons:** Styled with glowing hover states, gradient backgrounds, and crisp icons.
  - **Alerts & Prompts:** Standard browser alerts and confirms are forbidden. All prompts are routed through a globally accessible, custom neon modal system (`window.neonAlert`, `window.neonConfirm`, `window.neonPrompt`).
  - **Icons:** Heavy use of Bootstrap Icons (`bi`) to add visual flair to headers, buttons, and activity logs.
- **Responsiveness:** The UI is mobile-first. Complex desktop grids (like card selections or battle stakes) automatically convert into swipeable Bootstrap Carousels on smaller screens to ensure a premium mobile experience. QR codes are also heavily utilized to facilitate easy mobile sharing of battle rooms.
