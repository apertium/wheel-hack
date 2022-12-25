#!/bin/bash

cat Dockerfile.tools | docker build --squash -t wheel-tools -
cat Dockerfile.py38.x86_64_2_28 | docker build --squash -t wheel-py38-x86_64 -
cat Dockerfile.py39.x86_64_2_28 | docker build --squash -t wheel-py39-x86_64 -
cat Dockerfile.py310.x86_64 | docker build --squash -t wheel-py310-x86_64 -

ssh -l tino 192.168.1.12 'open -a Docker'
cat Dockerfile.py38.x86_64_2_28 | perl -wpne 's/x86_64/aarch64/g;' | ssh -l tino 192.168.1.12 '/usr/local/bin/docker build --squash -t wheel-py38-aarch64 -'
cat Dockerfile.py39.x86_64_2_28 | perl -wpne 's/x86_64/aarch64/g;' | ssh -l tino 192.168.1.12 '/usr/local/bin/docker build --squash -t wheel-py39-aarch64 -'
cat Dockerfile.py310.x86_64 | perl -wpne 's/amd64/arm64v8/g;' | ssh -l tino 192.168.1.12 '/usr/local/bin/docker build --squash -t wheel-py310-aarch64 -'
