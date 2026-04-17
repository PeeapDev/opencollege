# Audit Log Viewer — Design & Specification

**Status:** Planned (Q2 2026)
**DPG Criterion:** 9 — Do No Harm
**Backend:** implemented (see `app/Traits/LogsAudit.php`, `app/Models/AuditLog.php`)
**UI:** not yet built

---

## 1. Why it matters

OpenCollege already writes an append-only audit trail for every
create/update/delete on the high-sensitivity models (User, Institution,
Grade, Invoice, Payment). The trail records:

- **who** acted (user_id → User)
- **what** action (`created` / `updated` / `deleted`)
- **on what** (`model_type` + `model_id`)
- **when** (`created_at`)
- **from where** (`ip_address`, `user_agent`)
- **what changed** (`before` / `after` JSON blobs — sensitive fields like
  `password` and `remember_token` are stripped)

Writing logs without letting operators see them is useless. This spec
describes the admin UI that makes the trail actionable for:

- Investigating grade tampering ("who changed this student's mark?")
- Detecting unauthorised access ("who updated this finance record at 3am?")
- Responding to data-subject access requests (DPG Criterion 7)
- Meeting institutional accountability requirements (Sierra Leone Data
  Protection Act, comparable GDPR duties)

## 2. Who uses it

| Role | Access | What they see |
|------|--------|---------------|
| Super admin (`role=super_admin`) | Full read access across all institutions | All audit rows |
| College admin (`role=admin`, institution-scoped) | Read access to rows **scoped to their institution** | Only rows whose `model_id` belongs to their tenant |
| Student / faculty / staff | No access | N/A |

The institution scope is derived from the audited record, not from the
audit row itself. Implementation detail: join the model to its parent
institution at query time (e.g. a Grade row's institution comes through
`Grade → Student → institution_id`).

## 3. Page layout

**Route:** `GET /admin/audit-logs` (super admin + college admin)

### 3.1 Filters (top bar)

- **Date range** — default: last 7 days. Presets: today / 7d / 30d / custom.
- **Action** — multi-select: `created`, `updated`, `deleted`.
- **Model type** — multi-select populated from distinct `model_type`
  values in the DB (e.g. `App\Models\User`, `App\Modules\Exam\Models\Grade`).
- **Actor (user)** — autocomplete search across the `users` table.
- **IP address** — free-text match.
- **Search** — matches any JSON field in `before` / `after` (e.g. student
  name, invoice number).

Filters combine via AND. URL-persisted so admins can bookmark / share
specific queries.

### 3.2 Results table

Columns:

| Column | Notes |
|--------|-------|
| Time | Relative ("2 min ago") with absolute tooltip |
| Actor | Avatar + name, linking to their profile |
| Action | Pill badge (green/yellow/red for C/U/D) |
| Target | `ModelType #id` — clickable, opens 3.3 |
| Summary | One-liner: "Grade updated: marks 85 → 92" |
| IP | With geo hint when available (CloudFlare header or MaxMind lookup) |

Pagination: 50 per page, cursor-based to handle large trails.

### 3.3 Row detail (side panel)

Clicking a row opens a side panel showing the full before/after JSON
diff, rendered as a visual diff (react-style syntax highlighting —
can use [jsondiffpatch](https://github.com/benjamine/jsondiffpatch) in
a vanilla JS wrapper).

- Fields that changed are highlighted
- Unchanged fields collapsed by default, expandable
- User-agent string decoded to friendly browser/OS label

### 3.4 Exports

- **CSV** — current filtered result (all rows, not just current page)
- **JSON** — full structured export for forensics

Both routed via the existing `ExportController` (add `audit` entity).

## 4. Performance considerations

Audit tables grow quickly. Expected volume: ~1,000 rows per active
student per year. A 5,000-student college over 3 years ≈ 15M rows.
Measures:

- Partitioned by month (`audit_logs_2026_04`, `audit_logs_2026_05`, ...)
  via a cron that rolls forward monthly. Query the current + previous
  partition for the default 7-day window.
- Composite index on `(created_at, model_type, user_id)` — covers the
  default filters.
- Archive to S3-compatible cold storage after 2 years; UI only queries
  hot partitions unless "include archive" is ticked.
- Saved searches: cache common filter combinations (e.g. "all grade
  deletions this month") for 10 minutes.

## 5. Integrity guarantees

The `AuditLog` model blocks `UPDATE` at the ORM level. To survive direct
DB access:

- Revoke `UPDATE` and `DELETE` privileges on `audit_logs` from the
  application DB user; grant `INSERT` and `SELECT` only. A dedicated
  backup user keeps `SELECT` for exports. Root retains full access for
  schema migrations.
- Nightly checksum: hash each row into a running Merkle chain, stored
  in a sibling `audit_log_seals` table. Divergence between the chain
  and the DB state would indicate tampering.

This is belt-and-suspenders — the DB-level revoke alone blocks 99% of
edit scenarios.

## 6. Retention & privacy

- **Retention period**: 7 years (exceeds most jurisdictions). Configurable
  per-institution via `config/audit.php`.
- **PII in audit logs**: names, emails, IDs are in `before`/`after` by
  design. A subject-access export (DSAR) must include matching audit
  rows scoped to that subject.
- **Right to erasure**: when a user is hard-deleted, their audit rows
  are **not** deleted — they are rewritten with `user_id = NULL` and a
  `user_fingerprint` placeholder (hash of former email). This preserves
  accountability while satisfying erasure requests.

## 7. Implementation milestones

1. **Phase 1 — read-only admin view** (~2 days)
   - Controller + paginated index
   - Filter bar (date, action, model type, actor)
   - Row detail side panel with diff view
   - Institution-scope middleware on the route
2. **Phase 2 — exports** (~0.5 day)
   - Add `audit` to `ExportController`
   - CSV + JSON streaming downloads
3. **Phase 3 — performance** (~1 day, once tables exceed 5M rows)
   - Monthly partition rotation
   - Cursor-based pagination
4. **Phase 4 — integrity** (~1 day)
   - Revoke DB privileges
   - Merkle-chain seals + nightly verification job

Total: ~4.5 dev-days. Phases 1 + 2 are enough to flip Criterion 9 from
partial to full.

## 8. Open questions

- Should grade-change audit entries automatically notify the affected
  student? (Recommended: yes, but throttle to one notification per
  student per hour.)
- Should failed login attempts appear in the viewer even though no
  model was mutated? (Lean yes — write a synthetic row with
  `action='login_failed'` and `model_type='App\Models\User'`.)
