#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: $0 /path/to/gnuboard5-root" >&2
  exit 1
fi

TARGET_ROOT="$1"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_DIR="${ROOT_DIR}/assets/youngcart5-growth-plugin"

if [[ ! -f "${TARGET_ROOT}/common.php" ]]; then
  echo "Target does not look like a GnuBoard5 root: ${TARGET_ROOT}" >&2
  exit 1
fi

mkdir -p "${TARGET_ROOT}/extend" "${TARGET_ROOT}/plugin"
cp "${SOURCE_DIR}/extend/growth.extend.php" "${TARGET_ROOT}/extend/growth.extend.php"
rm -rf "${TARGET_ROOT}/plugin/growth"
cp -R "${SOURCE_DIR}/plugin/growth" "${TARGET_ROOT}/plugin/growth"

echo "Installed Growth plugin into ${TARGET_ROOT}"
