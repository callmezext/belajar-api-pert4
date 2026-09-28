# Design System & Direction (Anti-Slop Standard)

## Metadata
- Reading this as: Developer AI API Provider and Multi-Tier Gateway Platform for developers and tech administrators.
- Aesthetic Language: Modern technical utility, high readability, clear data hierarchy.
- Dials:
  - **ENERGY: 1 (Calm)**: Focus on utility, clear metrics, legible tables, and purposeful actions.
  - **RHYTHM: 2 (Balanced)**: Structured layouts, distinct cards for metrics, clear tabular data for CRUD.
  - **MOTION: 1 (Subtle)**: Fast responsive transitions (150ms-200ms), zero frivolous bouncing or decorative floating.

## Palette
- **Backgrounds**: Slate 50 (`#f8fafc`) for light surface, Slate 900 (`#0f172a`) for dark sidebar/shell.
- **Card/Surface**: Pure white (`#ffffff`) with subtle border Slate 200 (`#e2e8f0`).
- **Text**: Slate 900 (`#0f172a`) for titles, Slate 700 (`#334155`) for body, Slate 500 (`#64748b`) for labels.
- **Primary Accent**: Indigo 600 (`#4f46e5`) - used with purpose on primary actions and focus states.
- **Tier Identity Colors**:
  - Member Biasa (Free): Slate 600 / Slate 100 badge (1,000,000 Tokens limit).
  - STARTER: Blue 700 / Blue 50 badge (5,000,000 Tokens limit).
  - SUPER: Emerald 700 / Emerald 50 badge (20,000,000 Tokens limit).
  - Admin: Amber 700 / Amber 50 badge.

## Typography
- System Sans font stack: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif.
- Monospace stack: ui-monospace, SFMono-Regular, "Cascadia Code", "Roboto Mono", Consolas, monospace for API keys, JSON, and curl snippets.

## Anti-Slop Enforcement
- No em dashes (`—`) in copy.
- No fake numbers or fake trust badges.
- Every button, modal, form, and link is fully functional.
- Empty states, loading spinners, and error alerts provided for all dynamic views.
- Contrast ratio strictly conforms to WCAG AA (>= 4.5:1).
