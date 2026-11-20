# The Chore Facilitator — technical handover

Updated 19 November 2026. Local source changes only; nothing has been pushed or published.

## Confirmed details

- Canonical website: https://thechorefacilitator.co.za (non-www).
- Enquiries/applications: admin@thechorefacilitator.co.za.
- Hosting: xneelo.
- The existing photographs, branding, page composition and copy have been retained, apart from technical privacy/form disclosures and the functional controls requested.

## Added

- Sitemap and robots.txt; canonical URLs, social previews, organization metadata, favicon and touch icon.
- Cookie-settings disclosure in every footer. No analytics/advertising tracker is present, so there is no unnecessary consent banner or fake preference switch. Google Fonts remains in use and is disclosed. If optional trackers are introduced, they need a real consent gate before their requests load, with reject/manage/withdraw support.
- Back-to-top link in the footer flow, so it does not cover page content. Keyboard focus returns to main content; reduced-motion users do not get smooth scrolling.
- Escape/outside-click handling for mobile navigation, active-page indication and header visibility while keyboard-focused.
- Intrinsic image dimensions, appropriate lazy loading and responsive WebP copies. Original JPGs remain available.
- Tablet/laptop overflow correction on How It Works, without changing the layout system.
- Contact, candidate and client forms wired to a PHP handler. They stay unavailable until server configuration is complete. An email fallback is visible.
- Server-side required-field/type/length/email checks, consent validation, CSRF/session protection, honeypot, hashed-IP rate limits, duplicate-submission protection, failure input retention and safe mail headers. No public uploads are accepted. Authorised Super Admins can store supporting documents privately through the admin portal.
- Apache HTTPS/non-www redirects, custom 404, security headers, compression and caching rules.

## Placement-management V1 — 19 November 2026

The earlier mail-only form handler has been replaced with database-first intake. forms.php remains the compatible public endpoint and dispatches to api/intake.php. Form success means the record is saved, independent of email delivery. The server-config.php file is now a safe environment/private-file loader: do not place credentials in its committed source.

The admin portal includes clients, requests, applications, approved candidates, manual consideration, placements, enquiries, private notes/documents, staff permissions, settings, audit and reset/change-password flows. Chore Payroll remains a placement type, not a payroll engine. Public design and existing user edits have been retained.

## Setup and verification

- [ADMIN-SETUP.md](ADMIN-SETUP.md): staff bootstrap, permissions and daily workflow.
- [DATABASE-SETUP.md](DATABASE-SETUP.md): schema import, database access, backup/restore and retention.
- [XNEELO-DEPLOYMENT.md](XNEELO-DEPLOYMENT.md): private paths/configuration, upload, cron, HTTPS, mailbox and launch checks.
- [SECURITY-CHECKLIST.md](SECURITY-CHECKLIST.md): local evidence, test limitations and outstanding live checks.

The old TCF-verification/backend-report.json describes the superseded mail-only handler and must not be used as evidence for V1. Current synthetic MariaDB/admin tests are recorded in C:/Users/Alto Studio/Documents/Codex/TCF-admin-verification/ (workflow-report.json, security-report.json and extra-report.json). The public browser report is C:/Users/Alto Studio/Documents/Codex/TCF-verification/report.json.

Pre-backend checkpoint: C:/Users/Alto Studio/Documents/Codex/TCF-before-admin-2026-11-18. No commit, push, production database import or deployment was performed. Hosting configuration and actual inbox delivery remain pending.

## Consent foundation and client preview — 18 November 2026

- consent.js now owns Cookie settings. Accept all, Reject optional, category preferences, 180-day saved choices, expiry, withdrawal cleanup/reload and supported browser privacy signals are implemented. With the service registry empty, no banner appears and unavailable categories are disabled.
- Add an optional service only through TCF_OPTIONAL_SERVICES before consent.js loads, declaring its category, HTTPS script URL, cookie names/path/domain and storage keys. Do not also embed its script directly. Validate the actual vendor's storage cleanup and consent requirements when adding it. The generic foundation alone is not certification for a future vendor integration.
- The consent tests used a local dummy script, not a real tracker. Default-off, reject/persist, accept, withdraw, expiry, keyboard focus and privacy-signal behaviour passed.
- build-preview.cjs generates _preview with noindex metadata on every page, crawler exclusion, no PHP/configuration files and disabled form submission. Production files retain the real domain's metadata. The GitHub-hosted source also suppresses PHP requests by hostname.
- .github/workflows/preview.yml publishes this static package on pushes to main after GitHub Pages Source is set to GitHub Actions. The preview remains public to anyone with the URL; noindex is not password protection.
- Review URL: https://gm012.github.io/the-chore-facilitator/
- This session could read the existing preview but did not have GitHub publishing credentials. The changes have NOT been published. Once signed in, set Pages Source to GitHub Actions, push main, wait for the Client review preview workflow, and verify the published cookie settings and preview form notice before sending the URL to the client.
- No client message was sent. No form submission was delivered externally.
