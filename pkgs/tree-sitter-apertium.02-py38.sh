#!/bin/bash
set -eo pipefail

export PYTHONPATH=/usr/local/lib64/python3.8/site-packages
export PYTHON=python3.8

cd /pkgs/tree-sitter-apertium/python
./build.sh
