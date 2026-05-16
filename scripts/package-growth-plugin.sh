#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_DIR="${ROOT_DIR}/assets/youngcart5-growth-plugin"
DIST_DIR="${ROOT_DIR}/dist"
PACKAGE_DIR="${DIST_DIR}/youngcart5-growth-plugin"
ZIP_PATH="${DIST_DIR}/youngcart5-growth-plugin.zip"

if [[ ! -f "${SOURCE_DIR}/extend/growth.extend.php" ]]; then
  echo "Missing Growth plugin source under ${SOURCE_DIR}" >&2
  exit 1
fi

rm -rf "${PACKAGE_DIR}" "${ZIP_PATH}"
mkdir -p "${PACKAGE_DIR}" "${DIST_DIR}"

cp -R "${SOURCE_DIR}/extend" "${PACKAGE_DIR}/"
cp -R "${SOURCE_DIR}/plugin" "${PACKAGE_DIR}/"
cp "${SOURCE_DIR}/README.md" "${PACKAGE_DIR}/"
cp "${SOURCE_DIR}/LICENSE" "${PACKAGE_DIR}/"

(
  cd "${DIST_DIR}"
  zip -qr "$(basename "${ZIP_PATH}")" "$(basename "${PACKAGE_DIR}")"
)

echo "${ZIP_PATH}"
