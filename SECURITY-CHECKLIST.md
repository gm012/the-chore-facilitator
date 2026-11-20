# V1 verification and launch checklist

Local verification date: 19 November 2026. Synthetic records only. PHP 8.4.27, MariaDB 11.4.10, Chrome/Playwright on Windows. The public site was not redesigned. No production deployment, real client submission, or email to a real mailbox was performed.

## Implemented and locally exercised

- All three public forms save to real MariaDB with email disabled. Client submissions create a client and request; applications remain separate until staff approval; contact messages create enquiries.
- Staff login/dashboard, all six operational lists, request questionnaire/status/private notes, candidate approval/search/consideration, both placement-type selections and confirmed-placement status updates.
- Status history and private notes persist. Consideration history and related placements can be reviewed from a request. Optimistic versions reject stale edits. Confirmed/active placements prevent reusing an occupied candidate or already-filled request.
- PDO prepared queries, server-owned table/column allowlists, required/enum/role/date/length checks, malformed record-ID rejection and stored HTML escaping.
- CSRF rejection for public/admin changes; origin check; minimum completion time; honeypot; public and login rate limiting. Duplicate form retries in one session create only one record.
- HttpOnly, SameSite Strict, path-restricted staff cookies; session rotation in login; idle expiry; database-backed account/session invalidation after disabling users or resetting passwords. Production configuration sets Secure cookies; actual HTTPS behavior requires hosting verification.
- Super Admin vs Admin restrictions checked on the server, including direct requests to users, documents, downloads, settings and audit. Self-disable/demotion blocked; first-admin setup refuses reuse.
- Executable upload rejected. Valid PDF saved with a random filename in private storage, downloaded as an attachment by an authorised Super Admin, denied to anonymous/regular staff. Upload size/type/extension checks exist; no public uploads are enabled.
- Reset email captured by a local SMTP receiver (not a real mailbox). Stored token is a hash; token completes once, invalidates prior sessions and cannot be reused. Authenticated password change/re-login works. Expiry is enforced by the server query (30 minutes).
- Queue retries preserve records when mail configuration is rejected. Notification transport remains a hosting check: the portable Windows PHP CLI encountered a native failure during a simulated SMTP rejection, so that particular transport test is not claimed as passed. Local reset capture and disabled-mail persistence passed separately.
- Maintenance command and safe initial-admin creation exercised; logs do not contain tested passwords, DB password or reset token. Audit events contain metadata rather than questionnaire bodies.
- Admin dashboard checked at 390/768/1366/1920 pixels; record/staff/document screens at 390/768/1366. Tables scroll rather than compress into unreadable columns; scroll regions are keyboard-focusable.
- Public frontend: 48 page/viewport combinations, no broken local links/images, overflow or browser errors; cookie controls, back-to-top, mobile navigation and form success/failure behavior checked. These frontend transport checks use mocks; separate backend tests above use real MariaDB.
- Static GitHub review build inspected: no PHP, SQL, private configuration or admin backend included. Review forms remain disabled. All PHP files pass syntax checks.

## Must verify on xneelo before operational launch

- [ ] Account PHP/CLI versions and extensions, actual database host and restricted credentials.
- [ ] Actual private configuration/document/session paths outside every public domain root; permissions and backups.
- [ ] HTTPS/canonical redirects, Secure cookies and production environment; no reliance on spoofable forwarded headers.
- [ ] Apache access restrictions for configuration, schema, tools and includes. Local PHP's development server does not implement `.htaccess`; those rules have not been live-tested.
- [ ] No warning/stack trace/credentials in public errors, including oversized uploads and hosting failures. Set display_errors Off in hosting PHP configuration, not only in application code.
- [ ] Real public submissions visible in admin, actual notification and password-reset inbox receipt, and cron execution/retry behavior. Transport acceptance is not proof of delivery.
- [ ] Initial account created securely, setup tool removed, regular-user permissions and private downloads retested.
- [ ] Restore a consistent database + private-document + config backup on an isolated environment.
- [ ] Client approval of privacy wording, staff access, sensitive-data collection and retention/deletion procedure. Archiving does not erase information. No claim of legal compliance or independent penetration testing is made.
- [ ] Production indexability/public URLs correct; review-only mode removed from production. Admin/API remain noindex. Search Console ownership/submission handled separately.

## Scope and maintenance boundaries

Chore Payroll is a placement type only. There is no payroll calculation, client/candidate login, payment, scheduling or automated matching. Supporting files are restricted downloads, not malware-scanned documents; open only trusted files using protected workstations. Apply OS/PHP/database security updates, rotate credentials after exposure, restrict hosting access and review logs/failed queues regularly.

Ordinary operations archive records. Genuine privacy deletion is a restricted maintenance task with agreed retention and backup handling (DATABASE-SETUP.md). There is no publicly reachable installer or account-reset bypass. Keep application secrets and local verification artifacts outside the repository and public review package.
