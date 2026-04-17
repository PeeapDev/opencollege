# Data Breach Notification Runbook

**Audience:** Institution operators running OpenCollege
**DPG Criterion:** 9 — Do No Harm
**Last updated:** 2026-04-17

This runbook tells you **what to do when student or staff data on your
OpenCollege instance is compromised, suspected to be compromised, or
accidentally disclosed**. It is written so a non-security-specialist
registrar or IT officer can execute it.

Regulatory context: Sierra Leone Data Protection Act obliges controllers
to notify data subjects and the national commissioner of breaches
affecting their personal data. Equivalent duties exist under GDPR (EU),
NDPR (Nigeria), POPIA (South Africa), FERPA (USA), and similar regimes.
Check the regulation that applies to your jurisdiction in addition to
this playbook.

---

## 1. What counts as a breach

Any of the following:

| Event | Example |
|-------|---------|
| Unauthorised access | An attacker logs in with a stolen password |
| Unauthorised disclosure | An admin emails a class list to the wrong external address |
| Data loss | A laptop containing a DB backup is stolen |
| Data alteration | Grades are modified by someone who shouldn't |
| Availability loss | Ransomware encrypts the DB |

If you are unsure, treat it as a breach until you know otherwise.

## 2. Immediate actions — first 60 minutes

**Time matters.** Most regulators require notification **within 72 hours
of becoming aware** of a personal-data breach.

### 2.1 Contain

1. **Revoke suspected compromised credentials.** Use:
   ```
   php artisan tinker
   > User::where('email', 'suspect@example.com')->first()->update(['password' => bcrypt(Str::random(32)), 'must_change_password' => true]);
   ```
2. **Force-expire all sessions:**
   ```
   php artisan session:flush
   ```
   (Wipes `sessions` table; every user must log in again. Acceptable
   during an incident.)
3. **Block the attacker's IP** at the web-server level (nginx/Apache):
   ```
   # nginx
   deny 203.0.113.45;
   ```
   Or add to cPanel's IP Blocker / Cloudflare firewall.
4. **If the DB was exfiltrated**, rotate ALL secrets in `.env`
   (`APP_KEY`, DB password, mail password, Peeap keys). Generate a new
   `APP_KEY` with `php artisan key:generate` — note this will break
   encrypted session cookies, which is the desired effect.

### 2.2 Preserve evidence

Do **not** wipe logs. You will need them. Copy these off the server:

- `storage/logs/laravel.log` — application errors
- `audit_logs` table export — who did what (DB-level)
- Web server access logs — IPs, requests, user-agents
- MySQL slow/general logs if enabled

```
mkdir -p ~/incident-$(date +%Y%m%d)
cp -a storage/logs ~/incident-$(date +%Y%m%d)/app-logs
mysqldump -u root -p <db_name> audit_logs > ~/incident-$(date +%Y%m%d)/audit_logs.sql
cp /var/log/nginx/access.log* ~/incident-$(date +%Y%m%d)/
```

Hash each artifact with `sha256sum` and record the hashes — this
establishes a chain of custody.

### 2.3 Assess

Fill in the incident sheet (next section) with what you know right now.
You will update it as more becomes clear.

## 3. Incident intake sheet

| Field | Value |
|-------|-------|
| Incident ID | `YYYYMMDD-nnn` |
| Detected at | date + time, timezone |
| Detected by | name + role |
| Nature of breach | (from §1) |
| Systems affected | e.g. production DB, student portal, payment module |
| Data categories exposed | names / DOB / national_id / NSI / photos / grades / financial / passwords |
| Estimated # of people affected | |
| Root cause hypothesis | |
| Contained at | date + time |
| Evidence archive path | |

Save this as `incidents/YYYYMMDD-nnn.md` in a private ops repo — never
in the public OpenCollege repo.

## 4. Notification — within 72 hours

### 4.1 Regulator

For Sierra Leone deployments: notify the **Office of the Data
Protection Commissioner** as designated by the Data Protection Act.
Include:

- Institution name and contact person
- Nature of the breach (from §3)
- Categories and approximate number of affected subjects
- Likely consequences
- Measures taken or proposed to mitigate

For other jurisdictions, consult local counsel — thresholds and
timelines vary.

### 4.2 Data subjects

If the breach is likely to result in a **high risk** to subjects (e.g.
plaintext passwords leaked, financial data exposed), notify them
directly **without undue delay**. Template:

> Subject: Important security notice about your OpenCollege account
>
> On <date>, we detected <short description>. The information involved
> was <categories>. We have <actions taken>. We recommend you
> immediately <user action — e.g. change your password, monitor your
> bank statements>.
>
> If you have questions, contact us at <email/phone>.

Don't bury the lede. No marketing language. Do not apologise — state
the facts.

For **low-risk** breaches (e.g. a test-account password leaked), a
summary notice posted to the institution website is acceptable in most
jurisdictions.

### 4.3 Internal stakeholders

Notify in this order:

1. Institution legal counsel (or data protection officer if you have one)
2. Senior leadership (VC, Registrar, Bursar as relevant)
3. IT / infrastructure team
4. Communications — for any external messaging

## 5. Investigation

Once contained and initial notifications are out, investigate with a
5-why loop:

- **How did the attacker get in?** (weak password? SQL injection? reused
  session cookie? compromised laptop?)
- **Why was that vector open?** (no rate limiting? outdated dependency?
  missing MFA?)
- **Why didn't we detect it sooner?** (no alerting? no log review?)

Record findings in `incidents/YYYYMMDD-nnn.md`. Aim for a post-mortem
document within 5 working days.

## 6. Remediation

Common remediations after common breach types:

| Root cause | Remediation |
|------------|-------------|
| Weak user password | Enforce password policy; force reset; (future) require 2FA |
| Reused application secret | Rotate `APP_KEY`, DB password, API keys |
| Phishing targeting admin | Mandatory 2FA for admin roles; phishing training |
| Unpatched dependency | Upgrade; subscribe to security advisories |
| SQL injection | Add prepared statements; add WAF rule |
| Stolen backup | Encrypt backups at rest; rotate backup creds |

Tie each remediation to a tracked ticket with an owner and due date.

## 7. Recovery

- Verify all compromised credentials have been rotated
- Verify attacker persistence is gone (check for added admin users,
  unusual cron jobs, modified files) — `find . -mtime -30 -name "*.php"`
  is a good starting point
- Restore any altered data from the last clean backup, carefully
  merging in legitimate changes made since
- Bring affected services back online

## 8. Lessons learned

Within 2 weeks of the incident:

- Share the post-mortem internally (sanitised if needed)
- Update this runbook with anything that was unclear
- Ensure remediation tickets are closed (not deferred indefinitely)
- If appropriate, publish a public transparency note — a
  well-handled incident that is openly reported builds more trust than
  a silent one

## 9. Drill the runbook

**Once per semester** run a tabletop exercise: pick a hypothetical
breach, walk through the runbook, find the gaps. This is how you avoid
reading the runbook for the first time during a real incident.

## 10. Contacts template

Fill this in for your deployment and store it in the incident runbook:

```
Data Protection Commissioner (SL):  <contact>
Institution legal counsel:          <name, email, phone>
Institution DPO:                    <name, email, phone>
Senior leadership:                  <names, emails>
Hosting provider (emergency):       <contact>
OpenCollege upstream (advice):      support@college.edu.sl
```

Print a copy. Keep it with the BCP binder.
