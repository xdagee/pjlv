---
trigger: always_on
---

# PJLV Security & Safety Rules

* DO NOT execute any terminal commands automatically. Always present them and await explicit human approval.
* Restrict file system access only to the project root (`pjlv/`) and config files.
* Confirm before reading or writing sensitive files such as `.env` or deployment keys.
* Follow OWASP best practices for shared code, input validation, and authentication logic.
* Do not generate code that logs, prints, or exposes credentials, tokens, or private configuration.
