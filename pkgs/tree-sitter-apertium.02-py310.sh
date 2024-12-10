#!/bin/bash
set -eo pipefail

export PYTHONPATH=/usr/local/lib64/python3.10/site-packages
export PYTHON=python3.10

cd /pkgs/tree-sitter-apertium
./build.sh
