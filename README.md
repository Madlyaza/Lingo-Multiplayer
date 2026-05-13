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
