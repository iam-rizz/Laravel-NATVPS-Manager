#!/bin/bash
# Setup noVNC vendor files for VPS Console Access feature
# This script downloads and extracts noVNC ES modules to public/vendor/novnc

set -e

NOVNC_VERSION="1.6.0"
NOVNC_URL="https://github.com/novnc/noVNC/archive/refs/tags/v${NOVNC_VERSION}.tar.gz"
VENDOR_DIR="public/vendor"
NOVNC_DIR="$VENDOR_DIR/novnc"

echo "=========================================="
echo "Setting up noVNC v${NOVNC_VERSION}..."
echo "=========================================="

# Clean up existing installation
echo "[1/6] Cleaning up existing files..."
rm -rf "$NOVNC_DIR"
rm -rf "$VENDOR_DIR/vendor"
rm -rf /tmp/novnc.tar.gz /tmp/noVNC-*

# Create directories
echo "[2/6] Creating directories..."
mkdir -p "$NOVNC_DIR"
mkdir -p "$VENDOR_DIR/vendor"

# Download noVNC
echo "[3/6] Downloading noVNC..."
curl -sL "$NOVNC_URL" -o /tmp/novnc.tar.gz

if [ ! -f /tmp/novnc.tar.gz ]; then
    echo "ERROR: Failed to download noVNC"
    exit 1
fi

# Extract
echo "[4/6] Extracting..."
tar -xzf /tmp/novnc.tar.gz -C /tmp/

if [ ! -d "/tmp/noVNC-${NOVNC_VERSION}" ]; then
    echo "ERROR: Failed to extract noVNC"
    exit 1
fi

# Copy core files (ES modules)
echo "[5/6] Copying files..."
echo "  - Copying core modules to $NOVNC_DIR/"
cp -r "/tmp/noVNC-${NOVNC_VERSION}/core/"* "$NOVNC_DIR/"

# Copy vendor files (pako) to the correct location
# noVNC inflator.js imports from "../vendor/pako/lib/zlib/inflate.js"
# Since noVNC is at /vendor/novnc/, the import resolves to /vendor/vendor/pako/
echo "  - Copying pako library to $VENDOR_DIR/vendor/"
cp -r "/tmp/noVNC-${NOVNC_VERSION}/vendor/"* "$VENDOR_DIR/vendor/"

# Cleanup
echo "[6/6] Cleaning up temporary files..."
rm -rf /tmp/novnc.tar.gz "/tmp/noVNC-${NOVNC_VERSION}"

echo ""
echo "=========================================="
echo "noVNC setup complete!"
echo "=========================================="
echo ""
echo "Installed files:"
echo "  - noVNC core: $NOVNC_DIR/"
echo "  - Pako lib:   $VENDOR_DIR/vendor/pako/"
echo ""
echo "Directory structure:"
find "$VENDOR_DIR" -type d | head -20
echo ""
