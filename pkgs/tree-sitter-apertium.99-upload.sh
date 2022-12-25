#!/bin/bash

cd /pkgs/dist/tree-sitter-apertium
for F in *
do
	twine upload "$F"
done
