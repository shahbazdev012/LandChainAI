# LandChain AI

**Smart Property Registration & Verification System** — a Final Year Project that lets a
land-registry office register properties, attach ownership documents, run AI-assisted
(OCR-based) verification, and seal each record into a blockchain-inspired hash chain so its
authenticity can be publicly verified.

> This is an educational project. The "blockchain" is a teaching model of hash-chain
> integrity (SHA-256, append-only ledger) — **not** a distributed ledger, cryptocurrency,
> or smart-contract system. "AI verification" uses OCR + rule-based validation, not trained ML models.

## Local setup (Laravel Sail)

### 1. Clone & install dependencies

```bash
git clone <repo-url> LandChainAI
cd LandChainAI

# PHP dependencies (via a throwaway PHP container, no local PHP needed)
docker run --rm -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# Environment file
cp .env.example .env
```

### 2. Add the local virtual host

Point `landchainai.local` at your machine so the app is reachable at a friendly URL
(edit `/etc/hosts` on Linux/Mac, or `C:\Windows\System32\drivers\etc\hosts` on Windows):

```bash
echo "127.0.0.1 landchainai.local" | sudo tee -a /etc/hosts
```

Then set `APP_URL=http://landchainai.local` in `.env`.

### 3. Start Sail (Docker)

```bash
# First-time boot / after pulling changes to docker config
./vendor/bin/sail up -d

# Generate the app key (first run only)
./vendor/bin/sail artisan key:generate

# Install the Tesseract OCR binary inside the app container (required for real OCR)
docker compose exec -u root laravel.test apt-get update
docker compose exec -u root laravel.test apt-get install -y tesseract-ocr

# Migrate & seed demo data
./vendor/bin/sail artisan migrate:fresh --seed

# JS dependencies & front-end assets (or `sail npm run dev` while developing)
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Visit **http://landchainai.local** — sign in at `/login`, or try public verification at `/verify`.

### Everyday commands

```bash
./vendor/bin/sail up -d          # start containers (background)
./vendor/bin/sail down           # stop & remove containers
./vendor/bin/sail artisan migrate   # run new migrations
./vendor/bin/sail artisan migrate:fresh --seed   # reset DB + reseed demo data
```

## Tech stack

- **Laravel 13** (PHP 8.3+), **Inertia** + **Vue 3** + **TypeScript**
- **Tailwind CSS 4** with **shadcn-vue** (reka-ui) components
- **Laravel Fortify** (auth, 2FA, passkeys) · **Spatie Permission** (roles)
- **Wayfinder** for type-safe routes · **Laravel Sail** (Docker, MySQL 8.4)
- **Tesseract OCR** via `thiagoalessio/tesseract_ocr`

## Domain model

| Table | Purpose |
| --- | --- |
| `properties` | The ground-truth registry record (plot number, owner identity, location, area, status, created_by, approved_by). |
| `property_verifications` | Public users' ownership-verification attempts: uploaded image, OCR data, AI result, final status. |
| `property_blocks` | The append-only hash-chain ledger. One immutable block per **approved** property. |

Property lifecycle (admin side): `Pending Approval → Approved` (or `Rejected`).
Public verification outcome: `Verified / Rejected`.

## Access model

Two sides:

**Staff (admin side)** — roles:

| Role | Capabilities |
| --- | --- |
| `data_entry` | Create property records only. **Cannot** edit, approve, or upload. |
| `officer` | Create, edit, and approve/reject records. |
| `admin` | Everything, including deleting records. |

Flow: Data Entry enters text data → `Pending Approval` → an Officer reviews the text and **Approves**
(→ `Approved`, sealed into the hash chain) or **Rejects** it (Officer/Admin then correct & re-approve).
No images or OCR on the staff side.

**Public (citizen side)** — no login required, at `/verify-property`:
search approved records (by CNIC, owner, plot number, city, province) → open a record → upload an
ownership document → automated verification returns **VERIFIED / REJECTED**.

### Seeded credentials

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@admin.com` | `password123` |
| Officer | `officer@landchain.test` | `password123` |
| Data Entry | `dataentry@landchain.test` | `password123` |

## How public verification works

Primary flow — **upload once on `/verify-property`**:
1. A citizen uploads their ownership document on the search page.
2. `DocumentScanner` reads the identifying fields off the image — owner name, **CNIC**, plot number —
   using **Gemini** (handles handwriting) with an **OCR + regex** fallback (recovers the CNIC at least).
3. Those fields become the search filters against **approved** records:
   - **one match** → the same image is verified immediately and the result is shown;
   - **several matches** → listed with a one-click "Verify" that **reuses the uploaded image** (no re-upload);
   - **no match** → the detected fields are shown and manual search is offered.
4. Verification (`VerificationService`) combines OCR comparison + the Gemini cross-check
   (`{match, confidence, issues, notes}`) into a binary **VERIFIED / REJECTED**, stored on
   `property_verifications` (nullable `user_id` for guests) for audit and shown with reasons.

Manual filter search, and uploading directly on a property's detail page, remain as fallbacks.

This is **automated OCR + (optional) AI cross-check** — accurate framing: "AI-assisted verification",
not "AI decides alone". With no `GEMINI_API_KEY`, it falls back to OCR only; any OCR/Gemini failure
degrades gracefully instead of crashing.

The OCR engine is pluggable (`config/ocr.php`): `tesseract` (default, real) or `fake` (tests/CI).
The AI cross-check is optional (`config/services.php` → `gemini`): **no `GEMINI_API_KEY` → OCR only.**
Any OCR or Gemini failure degrades gracefully (records a reviewer note) instead of crashing.

## Demo data

`migrate:fresh --seed` loads a curated set of realistic records (Pakistani housing
societies/colonies) — 5 **approved**, 3 **pending approval**, 2 **rejected**.

Each **approved** record has a matching sample title-deed image under `public/demo/`
(served at `/demo/...`) so you can demonstrate the public verification end-to-end:

| Plot no. | Owner | Owner CNIC | Location | Sample deed |
| --- | --- | --- | --- | --- |
| `DHA-5C-1207` | Imran Yousaf | 35201-1234567-1 | DHA Phase 5, Lahore | `/demo/deed-dha-5c-1207.png` |
| `BTK-P4-0889` | Sana Riaz | 42101-7654321-2 | Bahria Town, Karachi | `/demo/deed-btk-p4-0889.png` |
| `GLB3-MB-045` | Tariq Mehmood | 35202-2233445-6 | Gulberg III, Lahore | `/demo/deed-glb3-mb-045.png` |
| `F11-3-220` | Ayesha Khan | 61101-9988776-5 | F-11/3, Islamabad | `/demo/deed-f11-3-220.png` |
| `JT-G4-512` | Bilal Ahmed | 35202-5566778-9 | Johar Town, Lahore | `/demo/deed-jt-g4-512.png` |

**To demo a VERIFIED result:** open `/verify-property`, **upload the matching deed** from `/demo/...`
(e.g. `deed-dha-5c-1207.png`). The system reads the owner/CNIC/plot off the image, finds the single
matching approved record, and shows **VERIFIED** automatically. Upload an unrelated image (or a
*different* deed against a property you reach via manual search) to get **REJECTED**.

## How the hash chain works

Each **approved** property is sealed into `property_blocks` by `HashChainService`:

- A block stores an immutable **snapshot** of the property plus a **SHA-256** hash computed
  over that snapshot **and the previous block's hash** (genesis = 64 zeros).
- `HashChainService::verify()` walks the chain, recomputes every hash, and reports the first
  broken block — so any tampering with a sealed record is detectable.

## Quality checks

```bash
./vendor/bin/sail pint --test          # PHP code style
./vendor/bin/sail php vendor/bin/phpstan analyse   # static analysis (level 7)
./vendor/bin/sail artisan test         # PHPUnit suite
./vendor/bin/sail npm run lint:check   # ESLint
./vendor/bin/sail npm run types:check  # vue-tsc
```
