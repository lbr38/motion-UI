## Approach
- Reason only as much as the task needs; keep output concise.
- No sycophantic openers or closing fluff.
- No emojis or em-dashes.
- Do not guess APIs, versions, flags, commit SHAs, or package names. Verify by reading code or docs before asserting.

## Token efficiency
- Read only files directly needed, and only the relevant functions/sections. Skip files over 100KB unless required. Ask before broad scans.
- Do not re-read files or restate context already established, unless the files changed.
- Keep plans to 3-5 steps unless complexity requires more.
- For edits, show only the changed section, not full file.
- Default to brief answers. Expand only if asked.

## Project layout
PHP application (web UI for motion and go2rtc, API, websocket server, background service). Code lives in `www/`:
- `controllers/` business logic, one subfolder per domain (Camera, Motion, Go2rtc, Layout, Service, Websocket, Api, User, ...). Namespaced classes, loaded by `controllers/Autoloader.php`. `controllers/ajax/` holds the ajax handlers, reached through `public/ajax/controller.php`.
- `models/` database access, one model per domain, extends `models/Model.php`.
- `views/` HTML rendering (`layout/`, `includes/` for containers, panels, tables). `templates/` non-HTML templates (motion, go2rtc, mail). `public/` web root (`api/v1/`, `resources/js`, `resources/styles`).
- `bin/` service and motion event scripts (`motionui`, `on_event_*`). `tools/` CLI scripts (service, database, translations). `config/` app properties and service units.
- `update/database/` database migrations, one file per release. `version` holds the current version.
- `translations/` gettext files (`.pot`, `.po`), regenerated with `tools/translations/update.sh` (run inside the container).
- `libs/` third-party (composer, `libs/vendor`) and `public/resources/js/libs/`. Never edit or read them.
- Outside `www/`: `docker/` and `Dockerfile` (image), `android/` and `fastlane/` (Android app), `notifications/`, `.github/workflows/` (CI).

## Checks
- PHPCS: `php phpcs.phar --standard=lint/phpcs.xml -n . --ignore=libs/,public/resources/js/stream/,*.js` (needs `phpcs.phar`, not in repo). Follow the rules in `lint/phpcs.xml`.
- No PHPStan config and no PHPUnit suite; other CI tests are in `.github/workflows/` (database update, image build).

## Commits
- Never commit or push anything
