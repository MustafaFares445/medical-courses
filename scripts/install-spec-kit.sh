#!/usr/bin/env bash
set -euo pipefail

TAG="${1:-}"
SOURCE="git+https://github.com/github/spec-kit.git"

if ! command -v uv >/dev/null 2>&1; then
    echo "uv is required to install Spec Kit Specify CLI. Install uv first: https://docs.astral.sh/uv/" >&2
    exit 1
fi

if [[ -n "$TAG" ]]; then
    SOURCE="${SOURCE}@${TAG}"
fi

uv tool install specify-cli --from "$SOURCE"

specify --version
