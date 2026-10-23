#!/usr/bin/env bash
# NextSaaS WordPress smoke runner (Playground by default, WP-CLI optionally).
set -euo pipefail
cd "$(dirname "$0")/.."
NEXTSAAS_DIR="$(pwd)"
PARENT_THEME="$(cd .. && pwd)/nextora"
BLUEPRINT="${BLUEPRINT:-blueprint.json}"
MODE="playground"
for arg in "$@"; do
	[ "$arg" = "--wp-cli" ] && MODE="wp-cli"
done

if [ ! -f assets/css/nextsaas.css ]; then
	npm run build
fi

if [ "$MODE" = "wp-cli" ]; then
	WP_PATH="${WP_PATH:-$(cd ../../../../ && pwd)}"
	if [ -n "${WP_CLI_CMD:-}" ]; then
		read -r -a run_command <<<"$WP_CLI_CMD"
	else
		run_command=(wp --path="$WP_PATH")
	fi
	"${run_command[@]}" theme activate nextsaas
	"${run_command[@]}" eval-file "$NEXTSAAS_DIR/tests/smoke/assertions.php"
	exit
fi

[ -d "$PARENT_THEME" ] || { echo "Missing parent theme: $PARENT_THEME" >&2; exit 1; }
npx --yes @wp-playground/cli@latest server \
	--blueprint="$BLUEPRINT" \
	--mount="$PARENT_THEME:/wordpress/wp-content/themes/nextora" \
	--mount="$NEXTSAAS_DIR:/wordpress/wp-content/themes/nextsaas"
