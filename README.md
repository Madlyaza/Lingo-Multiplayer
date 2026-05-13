# Lingo Multiplayer

[🇬🇧 English](#english) &nbsp;|&nbsp; [🇳🇱 Nederlands](#nederlands)

---

<a name="english"></a>
## 🇬🇧 English

**Table of Contents**
- [What is Lingo Multiplayer?](#what-is-lingo-multiplayer)
- [How to Play](#how-to-play)
  - [1. Create an Account](#1-create-an-account)
  - [2. Find or Create a Room](#2-find-or-create-a-room)
  - [3. Start the Game](#3-start-the-game)
  - [4. Guess the Word](#4-guess-the-word)
  - [5. Winning the Match](#5-winning-the-match)
  - [6. Leaderboard](#6-leaderboard)
- [Inactivity Cleanup](#inactivity-cleanup)
- [Design Choices](#design-choices)
- [Stack Choices](#stack-choices)
- [AI Tool](#ai-tool)
- [What I Would Do Differently](#what-i-would-do-differently)
- [Installation & Running](#installation--running)
  - [Prerequisites](#prerequisites)
  - [Option A — Docker](#option-a--docker-recommended)
  - [Option B — Local development](#option-b--local-development-without-docker)
  - [phpMyAdmin](#phpmyadmin)

---

### What is Lingo Multiplayer?

Lingo Multiplayer is a real-time two-player word guessing game inspired by the classic Dutch TV show *Lingo*. Both players compete to guess the same secret five-letter word before their opponent does. The first player to score **5 points** wins the match.

---

### How to Play

#### 1. Create an Account
Register with a username and password. Your wins and losses are tracked on the global leaderboard.

#### 2. Find or Create a Room
- **Create a room** — choose a name and set it to Public or Private.
  - Public rooms appear in the lobby for anyone to join.
  - Private rooms generate a **join code** you can share with a friend.
- **Join a room** — browse public rooms in the lobby, or paste a join code into the join field.

#### 3. Start the Game
Once two players are in the room, the host presses **Start Game**. The first player to guess is chosen randomly.

#### 4. Guess the Word
- The word is **5 letters** long.
- You are shown the **first letter** of the word at the top. As correct letters are found, they are revealed in their positions.
- Players **take turns** — one guess per turn.
- After each guess you receive colour-coded feedback on every letter:
  | Colour | Meaning |
  |--------|---------|
  | 🟩 Green | Correct letter, correct position |
  | 🟨 Yellow | Correct letter, wrong position |
  | ⬛ Grey | Letter not in the word |
- The first player to guess the word correctly scores **1 point**.
- If neither player guesses within **10 guesses** (5 each), the round ends with no point awarded and a new word is chosen.

#### 5. Winning the Match
The first player to reach **5 points** wins the match. After the game ends you are taken back to the lobby.

#### 6. Leaderboard
Check the **Leaderboard** from the lobby to see the top 10 players ranked by total wins.

---

### Inactivity Cleanup
Rooms and games that have had no activity for **10 minutes** are automatically removed. Players still in those rooms are redirected to the lobby with a notification.

---

### Design Choices

#### Why a 1v1 real-time multiplayer format?

I deliberately chose a head-to-head, two-player format rather than a larger lobby or team-based game. The core idea behind this decision was to create something that works as a quick **"in-between" game** — something you can fire up with a single friend while you are waiting for something, sitting together bored, or just want to pass a few minutes. A two-player match stays short and snappy by design: you are always either guessing or watching your opponent guess, so there is no downtime. The 1v1 structure also makes every round feel personal and directly competitive, which is far more engaging than getting lost in a crowd.

#### Why add a leaderboard?

Even in a casual party-game context, having a persistent leaderboard adds a layer of long-term motivation. Wins and losses are tracked across all matches, and the top 10 players are ranked globally. This serves two purposes:

1. **Replayability** — players who enjoy competing have something to chase. Climbing the leaderboard gives repeated sessions a goal beyond the immediate match.
2. **Engagement hook** — people are naturally drawn to rankings. Seeing your name on a leaderboard, or being one win away from pushing someone else off it, is a surprisingly powerful reason to keep playing.

The leaderboard intentionally stays simple (wins, losses, win percentage) so it is readable at a glance and never feels overwhelming.

---

### Stack Choices

#### Why Laravel?

I chose Laravel as the backend framework primarily because of my previous experience with it. I know it is a solid, battle-tested framework capable of handling virtually anything you need from a backend. It has a well-structured ecosystem — routing, ORM, authentication, scheduling, and more are all built in or one package away. An added benefit is that Laravel has strong representation in AI training data, which made it significantly easier to direct an AI assistant to generate correct, idiomatic code without constant corrections.

#### Why Vue?

I went with Vue for the frontend for the same core reason — prior experience. I know Vue looks good out of the box, is intuitive to work with, and scales well from small components up to a full SPA. Like Laravel, Vue is well-represented in AI-generated code, which meant the AI assistant could produce reliable component and store code with minimal back-and-forth.

#### Why Docker and MySQL?

Docker and MySQL were chosen together for practical reasons around portability. Having the entire stack — backend, frontend, database, and phpMyAdmin — containerised means the project can be exported to any other machine and run with a single command. This is especially valuable during development when you may want to share or demo the project without walking someone through a local installation. MySQL was the natural database choice to pair with this: it is the standard relational database for PHP/Laravel projects and integrates cleanly with phpMyAdmin for visual database management.

---

### AI Tool

#### Claude Sonnet 4.6 inside Visual Studio Code

For this project I used **Claude Sonnet 4.6** as my AI assistant, running directly inside **Visual Studio Code** via the GitHub Copilot extension.

I chose Claude Sonnet specifically because of my past experience with it — it is robust, works methodically through problems, and tends to produce well-structured code rather than jumping to quick but brittle solutions. When something goes wrong it reasons through the issue step by step rather than just retrying the same approach, which made it a reliable partner for a project built from scratch.

Running it inside Visual Studio Code was a deliberate choice as well. Having the assistant embedded in the editor means it has direct access to every file in the workspace — it can read the current state of any file, check for errors, search across the codebase, and make edits in place. This removes the copy-paste back-and-forth of using a standalone chat interface and keeps the entire development loop in one place.

---

### What I Would Do Differently

#### Relying on AI for initial project setup

Looking back, I do not think I made any significant mistakes — the project works as intended and functions as expected. However, one thing I would reconsider is using AI to handle everything from the very beginning, including the initial setup of Laravel, Vue, and Docker. Letting an AI scaffold the entire foundation means you can lose track of what is happening under the hood. In this project, that cost me time with Docker: because I had not set it up manually myself, I did not fully understand the configuration it had generated, and troubleshooting an issue eventually required me to completely reinstall Docker on my machine to reset it. Setting up the core infrastructure by hand at least once gives you a mental model of how all the pieces connect, which makes debugging far faster when something goes wrong.

#### Future additions (not in scope due to time constraints)

These are not things I would do *differently* — they are simply features I thought about during development but could not fit within the available time. I am noting them here as a reminder of where the project could grow:

- **Configurable game length** — let players choose how many correct words are needed to win a match (e.g. first to 3, 5, or 10).
- **Leaderboard opt-out** — give players the choice to keep their stats private and not appear on the public leaderboard.
- **Guest player support** — allow a user without a registered account to join a game hosted by a registered player, so a friend can quickly hop in for a casual match without needing to sign up.

---

### Installation & Running

#### Prerequisites
Make sure the following are installed on your machine before you begin:

| Tool | Minimum version | Download |
|------|----------------|----------|
| [Git](https://git-scm.com/) | any | https://git-scm.com/ |
| [PHP](https://www.php.net/) | 8.2 | https://www.php.net/downloads |
| [Composer](https://getcomposer.org/) | 2 | https://getcomposer.org/ |
| [Node.js](https://nodejs.org/) | 18 | https://nodejs.org/ |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | any | https://www.docker.com/products/docker-desktop/ |

> **Note:** PHP, Composer, and Node.js are only required for the **local development** option. If you only want to run the project via Docker you only need Git and Docker Desktop.

---

#### Option A — Docker (recommended)

Docker runs everything (backend, frontend, MySQL, phpMyAdmin, scheduler) with a single command. No local PHP or Node installation required.

**1. Clone the repository**
```bash
git clone https://github.com/your-username/lingo-multiplayer.git
cd lingo-multiplayer
```

**2. Create the backend environment file**
```bash
cp backend/.env.example backend/.env
```
The Docker Compose file already injects the correct database credentials, so no further edits are needed for a local run.

**3. Build and start all services**
```bash
docker compose up --build
```
This will:
- Build the PHP-FPM backend image and run migrations + seeders automatically on first start
- Start the Nginx reverse proxy on **http://localhost:8000**
- Start the Vite dev server on **http://localhost:5173**
- Start MySQL on port **3306**
- Start phpMyAdmin on **http://localhost:8080**
- Start the Laravel scheduler (room cleanup every minute)

**4. Open the app**

Navigate to **http://localhost:5173** in your browser.

**5. Stop all services**
```bash
docker compose down
```
Add `-v` to also delete the database volume: `docker compose down -v`

---

#### Option B — Local development (without Docker)

Use this option if you prefer to run the backend and frontend directly on your machine.

**1. Clone the repository**
```bash
git clone https://github.com/your-username/lingo-multiplayer.git
cd lingo-multiplayer
```

**2. Set up the backend**
```bash
cd backend

# Install PHP dependencies
composer install

# Create the environment file
cp .env.example .env

# Generate the application key
php artisan key:generate
```

**3. Configure the database**

Open `backend/.env` in a text editor and update the database section to match your local MySQL server:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lingo_multiplayer
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```
Create the database in MySQL if it does not exist yet:
```sql
CREATE DATABASE lingo_multiplayer;
```

**4. Run migrations**
```bash
php artisan migrate
```

**5. Set up the frontend**
```bash
cd ../frontend
npm install
```

**6. Start everything**

Open the project in VS Code and press **Ctrl + Shift + B** to run the default build task. This launches three terminal panels in parallel:
- `php artisan serve` — backend API on http://localhost:8000
- `npm run dev` — Vite frontend on http://localhost:5173
- `php artisan schedule:work` — room cleanup scheduler

Alternatively, run each in a separate terminal manually:
```bash
# Terminal 1 — backend
cd backend && php artisan serve

# Terminal 2 — frontend
cd frontend && npm run dev

# Terminal 3 — scheduler
cd backend && php artisan schedule:work
```

**7. Open the app**

Navigate to **http://localhost:5173** in your browser.

---

#### phpMyAdmin
When running via Docker, phpMyAdmin is available at **http://localhost:8080**. Log in with:
- **Server:** db
- **Username:** lingo
- **Password:** secret

---

<a name="nederlands"></a>
## 🇳🇱 Nederlands

[↑ Terug naar boven](#lingo-multiplayer)

**Inhoudsopgave**
- [Wat is Lingo Multiplayer?](#wat-is-lingo-multiplayer)
- [Hoe speel je het?](#hoe-speel-je-het)
  - [1. Account aanmaken](#1-account-aanmaken)
  - [2. Kamer zoeken of aanmaken](#2-kamer-zoeken-of-aanmaken)
  - [3. Spel starten](#3-spel-starten)
  - [4. Het woord raden](#4-het-woord-raden)
  - [5. Het duel winnen](#5-het-duel-winnen)
  - [6. Klassement](#6-klassement)
- [Inactiviteitsopruiming](#inactiviteitsopruiming)
- [Ontwerpkeuzes](#ontwerpkeuzes)
- [Stackkeuzes](#stackkeuzes)
- [AI-tool](#ai-tool-1)
- [Wat ik de volgende keer anders zou doen](#wat-ik-de-volgende-keer-anders-zou-doen)
- [Installatie & Starten](#installatie--starten)
  - [Vereisten](#vereisten)
  - [Optie A — Docker](#optie-a--docker-aanbevolen)
  - [Optie B — Lokale ontwikkeling](#optie-b--lokale-ontwikkeling-zonder-docker)
  - [phpMyAdmin](#phpmyadmin-1)

---

### Wat is Lingo Multiplayer?

Lingo Multiplayer is een real-time woordraadspel voor twee spelers, geïnspireerd op het bekende Nederlandse televisiespel *Lingo*. Beide spelers proberen hetzelfde geheime woord van vijf letters te raden voordat de tegenstander dat doet. De eerste speler die **5 punten** haalt, wint het duel.

---

### Hoe speel je het?

#### 1. Account aanmaken
Registreer met een gebruikersnaam en wachtwoord. Je overwinningen en nederlagen worden bijgehouden op het algemene klassement.

#### 2. Kamer zoeken of aanmaken
- **Kamer aanmaken** — kies een naam en stel deze in als Openbaar of Privé.
  - Openbare kamers verschijnen in de lobby en zijn voor iedereen zichtbaar.
  - Privékamers genereren een **deelnemerscode** die je met een vriend kunt delen.
- **Deelnemen aan een kamer** — blader door openbare kamers in de lobby, of voer een deelnemerscode in het invoerveld in.

#### 3. Spel starten
Zodra twee spelers in de kamer zijn, drukt de host op **Start Game**. De eerste speler die mag raden wordt willekeurig bepaald.

#### 4. Het woord raden
- Het woord bestaat uit **5 letters**.
- De **eerste letter** van het woord wordt bovenaan getoond. Zodra letters op de juiste positie worden geraden, worden ze zichtbaar gemaakt.
- Spelers zijn om de beurt aan zet — één gok per beurt.
- Na elke gok ontvang je kleurgecodeerde feedback per letter:
  | Kleur | Betekenis |
  |-------|-----------|
  | 🟩 Groen | Juiste letter, juiste positie |
  | 🟨 Geel | Juiste letter, verkeerde positie |
  | ⬛ Grijs | Letter staat niet in het woord |
- De eerste speler die het woord goed raadt, krijgt **1 punt**.
- Als geen van beide spelers het woord raadt binnen **10 beurten** (5 per speler), eindigt de ronde zonder punt en wordt er een nieuw woord gekozen.

#### 5. Het duel winnen
De eerste speler die **5 punten** bereikt, wint het duel. Na afloop keer je terug naar de lobby.

#### 6. Klassement
Bekijk het **Klassement** via de lobby om de top 10 spelers te zien, gerangschikt op het aantal overwinningen.

---

### Inactiviteitsopruiming
Kamers en spellen zonder activiteit gedurende **10 minuten** worden automatisch verwijderd. Spelers die zich nog in die kamers bevinden, worden teruggeleid naar de lobby met een melding.

---

### Ontwerpkeuzes

#### Waarom een 1v1 real-time multiplayerformaat?

Ik heb bewust gekozen voor een directe twee-spelersmodus in plaats van een grotere lobby of teamgebaseerd spel. Het kernidee achter deze keuze was het creëren van iets dat werkt als een snel **"tussendoor-spelletje"** — iets wat je met één vriend kan opstarten terwijl je ergens op wacht, samen verveeld bent of gewoon even een paar minuten wil vullen. Een 1v1-duel blijft van nature kort en direct: je bent altijd óf aan het raden óf aan het kijken hoe je tegenstander raadt, waardoor er geen dode momenten zijn. De 1v1-opzet maakt elke ronde ook persoonlijk en direct competitief, wat veel meer betrokkenheid geeft dan verdwijnen in een grote groep.

#### Waarom een klassement toevoegen?

Zelfs in een casual context voegt een persistent klassement een laag van langetermijnmotivatie toe. Overwinningen en nederlagen worden bijgehouden over alle duels, en de top 10 spelers worden globaal gerangschikt. Dit dient twee doelen:

1. **Herhaalbaarheid** — spelers die van concurreren houden, hebben iets om naar te streven. Het beklimmen van het klassement geeft herhaalde sessies een doel buiten het directe duel om.
2. **Verslavende haak** — mensen worden van nature aangetrokken door ranglijsten. Je naam op een klassement zien staan, of één overwinning verwijderd zijn van iemand anders eraf te stoten, is een verrassend krachtige reden om te blijven spelen.

Het klassement blijft bewust eenvoudig (overwinningen, nederlagen, winstpercentage) zodat het in één oogopslag leesbaar is en nooit overweldigend aanvoelt.

---

### Stackkeuzes

#### Waarom Laravel?

Ik koos voor Laravel als backend-framework in de eerste plaats vanwege mijn eerdere ervaring ermee. Ik weet dat het een solide, bewezen framework is dat vrijwel alles aankan wat je van een backend nodig hebt. Het heeft een goed gestructureerd ecosysteem — routing, ORM, authenticatie, planning en meer zijn allemaal ingebouwd of één pakket verwijderd. Een bijkomend voordeel is dat Laravel sterk vertegenwoordigd is in AI-trainingsdata, wat het aanzienlijk makkelijker maakte om een AI-assistent te sturen naar correcte, idiomatische code zonder constante correcties.

#### Waarom Vue?

Ik koos voor Vue als frontend om dezelfde kernreden — eerdere ervaring. Ik weet dat Vue er standaard goed uitziet, intuïtief is om mee te werken en goed schaalt van kleine componenten tot een volledige SPA. Net als Laravel is Vue goed vertegenwoordigd in AI-gegenereerde code, wat betekende dat de AI-assistent betrouwbare component- en store-code kon produceren met minimaal heen-en-weer.

#### Waarom Docker en MySQL?

Docker en MySQL werden samen gekozen om praktische redenen rond overdraagbaarheid. De volledige stack — backend, frontend, database en phpMyAdmin — in containers hebben betekent dat het project naar elk ander apparaat geëxporteerd kan worden en met één commando draait. Dit is vooral waardevol tijdens ontwikkeling wanneer je het project wil delen of demonstreren zonder iemand door een lokale installatie te hoeven leiden. MySQL was de logische databasekeuze hierbij: het is de standaard relationele database voor PHP/Laravel-projecten en integreert soepel met phpMyAdmin voor visueel databasebeheer.

---

### AI-tool

#### Claude Sonnet 4.6 in Visual Studio Code

Voor dit project heb ik **Claude Sonnet 4.6** gebruikt als AI-assistent, rechtstreeks in **Visual Studio Code** via de GitHub Copilot-extensie.

Ik koos specifiek voor Claude Sonnet vanwege mijn eerdere ervaringen ermee — het is robuust, werkt methodisch door problemen heen en produceert doorgaans goed gestructureerde code in plaats van snel maar fragiele oplossingen. Wanneer er iets misgaat, redeneer het de oorzaak stap voor stap door in plaats van simpelweg dezelfde aanpak opnieuw te proberen. Dat maakte het een betrouwbare partner voor een project dat volledig vanaf nul is opgebouwd.

Het draaien binnen Visual Studio Code was ook een bewuste keuze. De assistent ingebed in de editor hebben betekent dat het directe toegang heeft tot elk bestand in de werkruimte — het kan de huidige staat van elk bestand lezen, fouten controleren, door de codebase zoeken en wijzigingen direct doorvoeren. Dit elimineert het heen-en-weer kopiëren en plakken van een losstaande chatinterface en houdt de volledige ontwikkelcyclus op één plek.

---

### Wat ik de volgende keer anders zou doen

#### Vertrouwen op AI voor de initiële projectopzet

Achteraf gezien denk ik niet dat ik grote fouten heb gemaakt — het project werkt zoals bedoeld en functioneert zoals verwacht. Eén ding dat ik echter zou heroverwegen is het gebruik van AI voor alles vanaf het allereerste begin, inclusief de initiële opzet van Laravel, Vue en Docker. Door een AI de volledige basis te laten opzetten, kun je het overzicht verliezen van wat er "onder de motorkap" gebeurt. In dit project kostte dat me tijd met Docker: omdat ik het niet zelf handmatig had opgezet, begreep ik de gegenereerde configuratie niet volledig, en bij het oplossen van een probleem moest ik uiteindelijk Docker volledig opnieuw installeren op mijn computer om het te resetten. De kerninfrastructuur minstens één keer zelf met de hand opzetten geeft je een mentaal model van hoe alle onderdelen samenhangen, waardoor debuggen veel sneller gaat als er iets misgaat.

#### Toekomstige toevoegingen (nu buiten scope door tijdsgebrek)

Dit zijn geen dingen die ik *anders* zou doen — het zijn simpelweg functies waar ik tijdens de ontwikkeling aan gedacht heb maar die niet binnen de beschikbare tijd pasten. Ik noteer ze hier als herinnering van waar het project naartoe zou kunnen groeien:

- **Instelbare spellengte** — laat spelers kiezen hoeveel correcte woorden er nodig zijn om een duel te winnen (bijv. eerst naar 3, 5 of 10).
- **Klassement afmelden** — geef spelers de keuze om hun statistieken privé te houden en niet op het openbare klassement te verschijnen.
- **Gastspeler-ondersteuning** — laat een gebruiker zonder geregistreerd account deelnemen aan een spel dat door een geregistreerde speler wordt gehost, zodat een vriend snel kan instappen voor een casual potje zonder zich te hoeven aanmelden.

---

### Installatie & Starten

#### Vereisten
Zorg dat de volgende software geïnstalleerd is voordat je begint:

| Tool | Minimale versie | Download |
|------|----------------|----------|
| [Git](https://git-scm.com/) | willekeurig | https://git-scm.com/ |
| [PHP](https://www.php.net/) | 8.2 | https://www.php.net/downloads |
| [Composer](https://getcomposer.org/) | 2 | https://getcomposer.org/ |
| [Node.js](https://nodejs.org/) | 18 | https://nodejs.org/ |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | willekeurig | https://www.docker.com/products/docker-desktop/ |

> **Let op:** PHP, Composer en Node.js zijn alleen nodig voor de **lokale ontwikkel**-optie. Als je het project alleen via Docker wilt draaien, heb je alleen Git en Docker Desktop nodig.

---

#### Optie A — Docker (aanbevolen)

Docker start alles (backend, frontend, MySQL, phpMyAdmin, scheduler) met één enkel commando. Geen lokale PHP- of Node.js-installatie vereist.

**1. Repository klonen**
```bash
git clone https://github.com/your-username/lingo-multiplayer.git
cd lingo-multiplayer
```

**2. Backend-omgevingsbestand aanmaken**
```bash
cp backend/.env.example backend/.env
```
Docker Compose injecteert de juiste databasegegevens automatisch, dus verdere aanpassingen zijn niet nodig voor een lokale uitvoering.

**3. Alle services bouwen en starten**
```bash
docker compose up --build
```
Dit doet het volgende:
- Bouwt de PHP-FPM backend-image en voert migraties automatisch uit bij de eerste start
- Start de Nginx reverse proxy op **http://localhost:8000**
- Start de Vite dev-server op **http://localhost:5173**
- Start MySQL op poort **3306**
- Start phpMyAdmin op **http://localhost:8080**
- Start de Laravel-scheduler (kameropruiming elke minuut)

**4. De app openen**

Ga naar **http://localhost:5173** in je browser.

**5. Alle services stoppen**
```bash
docker compose down
```
Voeg `-v` toe om ook het databasevolume te verwijderen: `docker compose down -v`

---

#### Optie B — Lokale ontwikkeling (zonder Docker)

Gebruik deze optie als je de backend en frontend liever rechtstreeks op je eigen machine draait.

**1. Repository klonen**
```bash
git clone https://github.com/your-username/lingo-multiplayer.git
cd lingo-multiplayer
```

**2. Backend instellen**
```bash
cd backend

# PHP-afhankelijkheden installeren
composer install

# Omgevingsbestand aanmaken
cp .env.example .env

# Applicatiesleutel genereren
php artisan key:generate
```

**3. Database configureren**

Open `backend/.env` in een teksteditor en pas het databasegedeelte aan naar jouw lokale MySQL-server:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lingo_multiplayer
DB_USERNAME=jouw_mysql_gebruiker
DB_PASSWORD=jouw_mysql_wachtwoord
```
Maak de database aan in MySQL als die nog niet bestaat:
```sql
CREATE DATABASE lingo_multiplayer;
```

**4. Migraties uitvoeren**
```bash
php artisan migrate
```

**5. Frontend instellen**
```bash
cd ../frontend
npm install
```

**6. Alles starten**

Open het project in VS Code en druk op **Ctrl + Shift + B** om de standaard build-taak uit te voeren. Dit start drie terminalpanelen tegelijk:
- `php artisan serve` — backend-API op http://localhost:8000
- `npm run dev` — Vite frontend op http://localhost:5173
- `php artisan schedule:work` — kameropruimings-scheduler

Of start elk onderdeel handmatig in een aparte terminal:
```bash
# Terminal 1 — backend
cd backend && php artisan serve

# Terminal 2 — frontend
cd frontend && npm run dev

# Terminal 3 — scheduler
cd backend && php artisan schedule:work
```

**7. De app openen**

Ga naar **http://localhost:5173** in je browser.

---

#### phpMyAdmin
Bij gebruik van Docker is phpMyAdmin beschikbaar op **http://localhost:8080**. Log in met:
- **Server:** db
- **Gebruikersnaam:** lingo
- **Wachtwoord:** secret
