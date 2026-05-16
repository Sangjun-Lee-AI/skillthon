---
name: youngcart-growth-plugin
description: Build, package, install, or migrate the YoungCart5 Growth payment-promotion plugin. Use when Codex needs to deploy the Growth plugin to a GnuBoard5/YoungCart5 site, inspect or customize its checkout/product-page balloon and payment-method badge behavior, or port the bundled G5 hook/extend implementation toward GnuBoard7 plugin conventions.
---

# YoungCart Growth Plugin

## Purpose

Use this skill to work with the bundled Growth payment-promotion plugin.

The source plugin is in `assets/youngcart5-growth-plugin/` and includes:

- `extend/growth.extend.php`: GnuBoard5 auto-loaded hook bootstrap.
- `plugin/growth/admin.php`: standalone super-admin settings page.
- `plugin/growth/growth.js`: frontend DOM behavior for product/order pages.
- `plugin/growth/growth.css`: balloon animation, vertical payment layout, badge styles.

## Fast Workflow

Use the skill when the user asks to package, install, deploy, inspect, customize, or migrate this Growth plugin.

Example prompts:

```text
Use $youngcart-growth-plugin to package and install the Growth payment-promotion plugin.
영카트5 Growth 플러그인 배포 ZIP을 만들어.
영카트5 루트에 Growth 플러그인을 설치해.
Growth 플러그인을 그누보드7 플러그인 구조로 마이그레이션해.
```

1. For direct YoungCart5 deployment, copy `assets/youngcart5-growth-plugin/extend` and `assets/youngcart5-growth-plugin/plugin` into the target G5 root.
2. For a release artifact, run `scripts/package-growth-plugin.sh`; the output is `dist/youngcart5-growth-plugin.zip`.
3. For local installation into a mounted G5 root, run `scripts/install-g5-growth-plugin.sh /path/to/gnuboard5`.
4. For G7 migration, preserve the plugin behavior but replace G5 `add_event('tail_sub', ...)` and custom DB table setup with G7 plugin metadata, settings, assets, and layout-extension injection.

The GitHub-facing README at `README.md` includes end-user install notes and Codex skill usage. Keep the bundled plugin README at `assets/youngcart5-growth-plugin/README.md` focused on the deployable G5 plugin artifact.

## Direct Deployment Rules

- Install only into a GnuBoard5/YoungCart5 root that contains `common.php`.
- Preserve the target root structure:
  - `extend/growth.extend.php`
  - `plugin/growth/admin.php`
  - `plugin/growth/growth.js`
  - `plugin/growth/growth.css`
- Do not edit G5 core files.
- After install, visit `/plugin/growth/admin.php` as a super admin and enable Growth.
- The plugin auto-creates `g5_growth_config` on first load.

## G7 Migration Notes

When converting this plugin for GnuBoard7:

- Create a plugin folder such as `plugins/_bundled/sirsoft-growth`.
- Add `plugin.json`, `plugin.php`, `composer.json`, optional `config/settings/defaults.json`, and frontend assets under `resources/js`.
- Move the G5 settings fields to G7 plugin settings:
  - `enabled`
  - `balloon_text`
  - `balloon_color`
  - `balloon_bounce`
  - `top_payment_method`
  - `badge_enabled`
  - `badge_text`
  - `badge_color`
  - `vertical_payment_layout`
- Replace G5 checkout DOM selectors with G7 layout extension points when possible.
- Use layout extension JSON for checkout/product-page UI injection; use frontend handlers only for behavior that cannot be expressed safely in layout JSON.
- Keep user-visible Korean strings in Korean and code identifiers in English.

## Validation

For this skill package itself:

```bash
python3 /Users/sangjunlee/.codex/skills/.system/skill-creator/scripts/quick_validate.py .
```

For a G5 deploy artifact:

```bash
scripts/package-growth-plugin.sh
```

Then inspect the generated zip under `dist/`.
