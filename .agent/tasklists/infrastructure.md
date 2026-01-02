# Infrastructure Task Subset — PJLV

## Env Config Check

- id: infra‑env‑validation
  description: Validate production `.env` settings.
  inputFiles:
  - .env.example
  expectedOutput: Docs updated + config verified.
  verification: Config test & human review.

## Supervisor Setup Script

- id: infra‑supervisor‑script
  description: Supervisor configs for queue workers.
  inputFiles:
  - .agent/workflows/release‑pjlv.md
  expectedOutput: Ready Supervisor config.
  verification: Reviewed & approved script.

## Health Endpoint

- id: infra‑health‑endpoint
  description: Implement `/health` route.
  inputFiles:
  - routes/web.php
  expectedOutput: JSON status endpoint.
  verification: API test.
