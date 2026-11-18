# The Chore Facilitator — technical handover

Updated 18 November 2026. Local source changes only; nothing has been pushed or published.

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
- Server-side required-field/type/length/email checks, consent validation, CSRF/session protection, honeypot, hashed-IP rate limits, duplicate-submission protection, failure input retention and safe mail headers. No uploaded documents are accepted or stored.
- Apache HTTPS/non-www redirects, custom 404, security headers, compression and caching rules.

## xneelo activation — still required

1. Confirm the actual account paths in the hosting panel. The public website may live under /usr/www/users/<account>. Create a private, writable directory under the account's confirmed private storage (often /usr/home/<account>), OUTSIDE the public document root. Do not guess the account or use the website's parent folder automatically.
2. Select a supported PHP 8.x version and confirm PHP mail is enabled for this domain. Confirm admin@thechorefacilitator.co.za exists and is permitted as the sending address; check SPF/DKIM and reply routing with the hosting provider.
3. In server-config.php set private_dir to that verified absolute private directory (or set TCF_PRIVATE_DIR on the server). Set enabled to true only when ready to test on HTTPS. The handler creates sessions and rate-limits subdirectories with restrictive permissions. It does not store form bodies.
4. Upload the site including .htaccess, forms.php, server-config.php and form-schema.json. Do not upload development checkpoints or local tests. Apache should deny browser access to server-config.php and form-schema.json while PHP can read them.
5. Verify HTTPS, redirects, security headers, compression, caching and a genuinely missing nested URL returning HTTP 404 with the styled 404 page. These settings cannot be established by a static local preview.
6. With permission, submit all three forms using synthetic data on the LIVE site. Confirm receipt in the actual admin inbox, reply-to behaviour and spam placement. A successful PHP mail() response means host acceptance only; it does not prove inbox delivery. No external test messages have been sent during this work.
7. Verify all pages on the deployed version and check the browser console/network for policy or missing-asset errors.
8. Ask the client to review privacy/cookie/terms content, retention practices and placement claims. Existing legal pages are not a legal-compliance guarantee.
9. Verify Search Console ownership and submit https://thechorefacilitator.co.za/sitemap.xml after publication. Confirm the deployed site has no staging noindex or blocking headers. Do not submit review/staging URLs.

## Verification evidence

Local browser report: C:/Users/Alto Studio/Documents/Codex/TCF-verification/report.json

Local PHP/backend report: C:/Users/Alto Studio/Documents/Codex/TCF-verification/backend-report.json

The browser checks cover all 12 pages at 390, 768, 1366 and 1920 pixels wide, assets/links, basic headings and alternatives, cookie-dialog focus/Escape, navigation, back-to-top, and frontend delivery success/error states. The backend checks use an isolated local SMTP receiver, not the real client inbox. These are not live-host verification.

Original source checkpoint: C:/Users/Alto Studio/Documents/Codex/TCF-checkpoint-1794997869438

Existing user edits to index.html, services.html and the nannies image were retained. No Git commit, push or deployment was performed.
