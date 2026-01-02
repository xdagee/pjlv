# Testing Task Subset — PJLV

## PHPUnit Tests Suite

- id: test‑phpunit‑suite
  description: Write PHPUnit tests (unit + integration).
  inputFiles:
  - tests/
  expectedOutput: Comprehensive test suite.
  verification: `test_report.json` with pass results.

## Browser E2E Tests

- id: test‑browser‑suite
  description: Browser automation tests (Dusk or similar).
  inputFiles:
  - tests/Browser/
  expectedOutput: UI flows working (apply, approve).
  verification: Screenshot series + pass logs.
