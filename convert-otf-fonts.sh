#!/usr/bin/env bash

### converts OTF fonts into WOFF2, WOFF and TTF formats

### adapted for MacOS after https://gist.github.com/gapurov/ece16f3b2de818c0de53628582346f0d

### you might need to install FontForge
### brew install fontforge

### usage for multiple files
### for file in *.otf; do ./convert-otf-fonts.sh "$file" .; done

set -e

SOURCE=$1
DESTINATION=$2
FILENAME=$(basename "${SOURCE%.otf}")

# Check input file format
if [[ ${SOURCE: -4} != ".otf" ]]; then
  echo "Usage: convertOtf input.otf /path/to/output/"
  exit 1
fi

# Check the destination is a folder
if [[ ! -d "$DESTINATION" ]]; then
  echo "Usage: convertOtf input.otf /path/to/output/"
  exit 1
fi

echo "[OTF --> WOFF]"
fontforge -script -c "font = fontforge.open('$SOURCE'); font.generate('$DESTINATION/$FILENAME.woff'); font.close();"

echo "[OTF --> WOFF2]"
woff2_compress "${SOURCE}"

echo "[WOFF2 --> TTF]"
woff2_decompress "${DESTINATION}/${FILENAME}.woff2"

exit 0
