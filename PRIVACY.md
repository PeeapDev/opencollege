# OpenCollege privacy information

**Project:** OpenCollege — Higher-Education Management System

**Maintainer:** PeeapDev (Peeap Ltd, Sierra Leone)

**Website:** https://college.edu.sl

**Privacy contact for the reference deployment:** college.edu.sl@gmail.com
**Last reviewed:** 2026-09-23

This document describes the personal data OpenCollege can process, the controls
visible in the public source code, and responsibilities that remain with each
institution operating it. It is not a certification of compliance with every
law in every jurisdiction. Features in the public repository may not yet be
deployed at college.edu.sl; operators must verify their own installation.

## Controller and scope

An institution using OpenCollege decides why and how its student and staff
records are processed and must identify itself and a privacy contact to its
users. The maintainer provides software and may also operate the reference
deployment; those roles must not be treated as interchangeable. For a request
about a particular student's or employee's records, contact the institution
that holds the record. If you cannot identify the operator of a record on the
reference deployment, write to college.edu.sl@gmail.com so the request can be
directed to the appropriate operator. Do not email sensitive records or
identity documents.

## Data and purposes

Depending on the modules an institution enables, the software can hold names,
contact and guardian details, date of birth, photographs, enrolment and
employment information, grades, attendance, invoices and payments, account
credentials, and technical logs. Institutions use this information to provide
education and administration, authenticate users, maintain records, and process
payments. An institution must tell its users which fields are required, which
are optional, its lawful basis for each purpose, and how long records are kept.

The application can integrate with external verification and payment services.
An operator must identify active integrations and any associated data sharing
or international transfers in its own notice. We do not claim that every
deployment keeps all data solely within its own database.

## Consent and user choices

Core academic and employment records are not necessarily processed on consent;
the operator must determine and document the applicable basis. An operator
must provide any required notice or consent before collecting optional data or
using information for a new purpose, such as marketing. We have not verified a
universal consent-management or withdrawal workflow in the deployed software.
Operators must record and honour such choices through their own documented
process until an appropriate workflow is implemented and tested.

## Access, correction, deletion, and export requests

Users may ask the institution holding their data for access, correction,
deletion, restriction, or a copy, where applicable law provides those rights.
The operator must verify the requester's authority, review legal retention
requirements, and respond within the period required by applicable law.
Deleting an account must not be assumed to erase every related academic,
financial, backup, or audit record. The public repository contains export
code, but availability and authorization of those endpoints must be verified
before they are offered to users or cited as deployed functionality.

See [the privacy-request procedure](docs/privacy-requests.md) for the steps
operators and the maintainer should follow. Do not send passwords, full student
records, or identity documents by ordinary email.

## Security and retention

The code uses Laravel authentication, password hashing, CSRF protection for
web forms, and institution-scoped queries in many modules. These measures do
not replace an independent security review, and they do not guarantee that
every endpoint is correctly authorized. Each operator must set access roles,
protect backups, configure retention periods, and review its integrations.
No universal, verified schedule for automatic deletion or pseudonymisation of
student and staff records is claimed here.

Report security issues through [SECURITY.md](SECURITY.md). Privacy requests
should use the privacy contact above or the operator's published contact.

## Children and international use

If an institution enrols minors, it must apply applicable guardian-notice or
consent requirements and restrict access to children's records. Guardian
fields alone do not constitute a verified child-safety or consent workflow.
Institutions outside Sierra Leone, or serving people in other jurisdictions,
must assess those jurisdictions' laws independently. We do not claim blanket
GDPR or other international-law compliance.

## Changes

This document is versioned in the public repository. Each operator should
publish its own notice with its legal identity, contact, purposes, lawful
bases, recipients, retention periods, and request procedure.
