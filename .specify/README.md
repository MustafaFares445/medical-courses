# Spec Kit Workspace

This repository uses GitHub Spec Kit / Specify CLI to drive spec-first backend implementation.

## Install

```bash
composer speckit:install
```

To pin a specific Spec Kit release tag:

```bash
bash scripts/install-spec-kit.sh vX.Y.Z
```

## Workflow

Use the Spec Kit commands in this order for each feature:

```text
/speckit.constitution
/speckit.specify
/speckit.clarify
/speckit.checklist
/speckit.plan
/speckit.tasks
/speckit.analyze
/speckit.implement
```

Codex implementation should follow the generated `specs/*` artifacts and the project constitution in `.specify/memory/constitution.md`.
