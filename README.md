# LandChain AI

**Smart Property Registration & Verification System** — a Final Year Project that lets a
land-registry office register properties, attach ownership documents, run AI-assisted
(OCR-based) verification, and seal each record into a blockchain-inspired hash chain so its
authenticity can be publicly verified.

> This is an educational project. The "blockchain" is a teaching model of hash-chain
> integrity (SHA-256, append-only ledger) — **not** a distributed ledger, cryptocurrency,
> or smart-contract system. "AI verification" uses OCR + rule-based validation, not trained ML models.

## Tech stack

- **Laravel 13** (PHP 8.3+), **Inertia** + **Vue 3** + **TypeScript**
- **Tailwind CSS 4** with **shadcn-vue** (reka-ui) components
- **Laravel Fortify** (auth, 2FA, passkeys) · **Spatie Permission** (roles)
- **Wayfinder** for type-safe routes · **Laravel Sail** (Docker, MySQL 8.4)
- **Tesseract OCR** via `thiagoalessio/tesseract_ocr`

## Domain model

| Table | Purpose |
| --- | --- |
| `properties` | The registry record (parcel number, owner identity, location, area, status). |
| `property_documents` | Ownership documents stored on a **private** disk; never publicly served. |
| `property_verifications` | Each AI verification run: status, score, per-check breakdown, OCR text. |
| `property_blocks` | The append-only hash-chain ledger. One immutable block per property. |

Lifecycle status: `Pending → Verified / Suspicious / Rejected`.

## Access model

This is an **officer-operated** registry (public self-registration is disabled).

| Role | Capabilities |
| --- | --- |
| `admin` | Full access, including deleting properties. |
| `officer` | Register, edit, verify and manage properties. |

Property **search & authenticity verification is public** at `/verify` (by registration number or block hash).

### Seeded credentials

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@admin.com` | `password123` |
| Officer | `officer@landchain.test` | `password123` |

## How verification works

1. The officer uploads a document (image scans give the best OCR results) and runs verification.
2. `VerificationService` extracts text via the configured OCR engine, then runs weighted checks
   against the registry record — property number, owner name, owner CNIC, and OCR quality
   (weights/thresholds live in `config/verification.php`).
3. The checks produce a 0–100 confidence score mapped to **Verified / Suspicious / Rejected**.

The OCR engine is pluggable (`config/ocr.php`):

- `tesseract` (default) — real OCR via the Tesseract binary.
- `fake` — deterministic text for tests/CI (set automatically in `phpunit.xml`).

If the Tesseract binary is unavailable at runtime, verification degrades gracefully
(records a *Suspicious* result with a reviewer note) instead of failing.

## How the hash chain works

Each registered property is sealed into `property_blocks` by `HashChainService`:

- A block stores an immutable **snapshot** of the property plus a **SHA-256** hash computed
  over that snapshot **and the previous block's hash** (genesis = 64 zeros).
- `HashChainService::verify()` walks the chain, recomputes every hash, and reports the first
  broken block — so any tampering with a sealed record is detectable.

## Local setup (Laravel Sail)

```bash
# Start containers
./vendor/bin/sail up -d

# Install the Tesseract OCR binary inside the app container (required for real OCR)
docker compose exec -u root laravel.test apt-get update
docker compose exec -u root laravel.test apt-get install -y tesseract-ocr

# Migrate & seed demo data
./vendor/bin/sail artisan migrate:fresh --seed

# Build front-end assets (or `sail npm run dev` while developing)
./vendor/bin/sail npm run build
```

Visit http://localhost — sign in at `/login`, or try public verification at `/verify`.

## Quality checks

```bash
./vendor/bin/sail pint --test          # PHP code style
./vendor/bin/sail php vendor/bin/phpstan analyse   # static analysis (level 7)
./vendor/bin/sail artisan test         # PHPUnit suite
./vendor/bin/sail npm run lint:check   # ESLint
./vendor/bin/sail npm run types:check  # vue-tsc
```
