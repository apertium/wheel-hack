#!/bin/bash
set -eo pipefail

export PYTHONPATH=/usr/local/lib64/python3.9/site-packages
export PYTHON=python3.9

cd /pkgs/tree-sitter-apertium/python
./build.sh
