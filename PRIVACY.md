# Privacy Policy

**Project:** OpenCollege — Higher-Education Management System
**Maintainer:** PeeapDev (Peeap Ltd, Sierra Leone)
**Website:** https://college.edu.sl
**Last updated:** 2026-04-15

OpenCollege is a software platform that partner higher-education institutions
self-host to manage their own students, staff, courses, and records. This
document describes the privacy principles the software follows and the
controls operators and end-users have over personal data.

It is written to meet the privacy-and-legal-compliance expectations of the
Digital Public Goods Alliance (DPGA) Standard and relevant data-protection
law (Sierra Leone Data Protection Act, EU GDPR, and comparable regimes).

---

## 1. Who is the data controller?

When an institution (a university, college, or polytechnic) installs and
operates OpenCollege, **that institution is the data controller** for all
data stored in its instance. PeeapDev, as the maintainer of the software,
is **not** the controller of student or staff records held by operators.

For institutions hosted on the reference deployment at https://college.edu.sl,
the data controller is the hosting institution identified on that instance's
"About" page.

## 2. What personal data does OpenCollege collect?

OpenCollege is an institutional management system and therefore processes
personal data by design. Typical categories include:

- **Student records:** full name, date of birth, national ID / index number,
  photograph, contact details, guardian details, admission and enrolment
  records, courses, grades, attendance, fee payments, disciplinary records,
  certificates issued.
- **Staff records:** full name, contact details, role, payroll information
  (where the HR module is enabled).
- **Authentication data:** email address, hashed password, session tokens,
  two-factor secrets (where enabled).
- **Audit / technical logs:** IP address, user-agent, timestamps for
  authentication and sensitive actions (where audit logging is enabled).

OpenCollege **does not** by default transmit personal data to any external
service. All data is stored in the operator's own database.

## 3. Lawful basis for processing

Operators must establish a lawful basis for each processing activity under
applicable law. Typical bases:

- **Contract / legitimate interest:** academic records required to deliver
  education.
- **Consent:** optional fields (photographs for marketing, alumni contact).
- **Legal obligation:** reporting to the national education ministry.

OpenCollege provides the technical means for operators to record consent
and to restrict processing; the legal framework is the operator's
responsibility.

## 4. Data subject rights

The software supports the following rights for data subjects (students,
staff, guardians):

- **Right of access:** data subjects can request a full copy of their data
  via the data-export endpoints (see `docs/api-export.md` when implemented).
  Operators must process these requests within the timelines set by local
  law.
- **Right to rectification:** students and staff can correct their own
  contact details through the portal. Academic records are corrected
  through a documented registrar workflow.
- **Right to erasure ("right to be forgotten"):** OpenCollege supports
  deleting personal data at the end of the retention period. Where academic
  records must be preserved by law, the software supports pseudonymisation.
- **Right to data portability:** all primary entities can be exported as
  CSV or JSON (see `docs/api-export.md`).
- **Right to object / withdraw consent:** optional data fields can be
  cleared at any time without affecting core records.

Operators must publish their own privacy notice identifying the controller
contact, data-protection officer, and retention periods.

## 5. Data retention

OpenCollege does not impose a fixed retention period — operators configure
this per data category, per local legal requirements. The software supports:

- Automatic purge of inactive sessions and auth tokens.
- Soft-delete for records, with documented hard-delete workflows.
- Audit-log rotation.

## 6. Security

OpenCollege follows standard Laravel security practices:

- Passwords hashed with bcrypt (Laravel default).
- CSRF protection on all state-changing requests.
- Role-based access control.
- Parameterised queries (Eloquent / query builder) to prevent SQL injection.
- HTTPS enforced in production deployments.

Security vulnerabilities must be reported via the process in `SECURITY.md`.

## 7. Children's data

OpenCollege is designed primarily for higher-education institutions. Where
operators use it for students under 18, they must:

- Obtain parental / guardian consent where required by local law.
- Limit collection to what is strictly necessary.
- Apply stricter access controls to minors' data.

The software provides guardian-relationship fields and access-control
flags to support this.

## 8. International transfers

OpenCollege itself does not transfer data internationally. If an operator
chooses to integrate with an external service (e.g., a cloud storage
backend, an AI grading assistant, a payment gateway), the operator is
responsible for ensuring the integration complies with cross-border
transfer rules.

## 9. Contact

Questions about this software's privacy features: support@college.edu.sl
Security issues: see `SECURITY.md`
Questions about a specific operator's use of the software: contact that
operator directly.

## 10. Changes to this policy

This file is versioned in Git. Material changes are announced in release
notes. Each operator should publish their own privacy notice for end-users
of their instance.
