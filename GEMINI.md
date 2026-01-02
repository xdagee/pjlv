# GLOBAL GEMINI SAFETY & BEHAVIOR DIRECTIVES

# 1. Execution Safety
NEVER execute ANY terminal or shell command without explicit human confirmation and approval.
This includes but is not limited to:
- file deletions (rm, rmdir)
- database drops
- migrations to production
- server restarts
- CI/CD triggers

If a command is required, always output it first for approval.

# 2. Sensitive Data Protection
DO NOT read or print contents of:
- .env or environment configuration files
- API keys, secrets, tokens, private keys
- production database credentials
- deployment credentials
If access is needed, request explicit human consent and redacted values.

# 3. Database & Migrations
Database schema changes (migrations) require:
- explicit human intent confirmation
- documented rollback plan
- reversible migration logic
Never apply migrations directly to production environments.

# 4. File System Restrictions
Only operate within the project root (`pjlv/`).
Do not access or modify parent directories, system paths, user home folders, or other repositories.

# 5. Test & Verification Policy
All generated code must be accompanied by:
- unit tests
- integration or end‑to‑end tests (if applicable)
- verification steps linked to acceptance criteria

Do not finalize or merge code without passing test reports.

# 6. Artifact Traceability
All work must reference:
- SPEC files (`PROJECT_SPEC.md`, `QA_SPEC.md`)
- architecture documents (`ARCHITECTURE.md`)
- task lists (`TASK_LIST.md`)
- acceptance criteria

Artifacts should include spec section IDs or line references for traceability.

# 7. Human Review Gates
Before significant actions (deployments, schema changes, performance scans):
- pause and present artifacts
- request human review and explicit approval
- document reviewer decision

# 8. Communication Clarity
When summarizing decisions or outputs, include:
- rationale for changes
- spec references
- test results summary
- potential risks or side effects

# 9. Error & Recovery Behavior
If an error occurs:
- stop the current workflow
- generate a clear diagnostic report
- propose corrective tasks
- do NOT retry automatically without approval

# 10. Security Best Practices
Follow OWASP and Laravel best practices:
- sanitize all inputs
- validate data before persistence
- never trust user or agent‑supplied inputs

# END OF GEMINI DIRECTIVES
