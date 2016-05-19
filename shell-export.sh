# Makes app config available as shell variables when sourced
# Should be sourced from the directory containing the vendor folder
# Note: No shebang since this file is supposed to be sourced

export TMPDIR="$(mktemp -d)"
export PHP_APP_CONFIG_EXPORTED=0
php -d variables_order="EGPCS" vendor/neam/php-app-config/export.php > $TMPDIR/php-app-config.sh

if [ "$?" == "0" ]; then
    source $TMPDIR/php-app-config.sh
    export PHP_APP_CONFIG_EXPORTED=1
else
    # show error messages
    cat $TMPDIR/php-app-config.sh
fi

rm -r "$TMPDIR"

[ "$PHP_APP_CONFIG_EXPORTED" == "1" ] || exit 1;
