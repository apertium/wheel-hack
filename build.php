#!/usr/bin/env php
<?php

chdir(__DIR__.'/pkgs/tree-sitter-apertium');

echo "Updating repo\n";
echo shell_exec('git reset --hard HEAD 2>&1');
echo shell_exec('git clean -f -d -x 2>&1');
echo shell_exec('git fetch --all -f 2>&1');
echo shell_exec('git remote update -p 2>&1');
if (intval(shell_exec('git branch | grep [*] | wc -l') == 0)) {
  $head = trim(shell_exec('git remote show origin | grep "HEAD branch" | egrep -o \'([^ ]+)\$\''));
  echo shell_exec("git symbolic-ref HEAD refs/heads/$head 2>&1");
  echo shell_exec('git fetch --all -f 2>&1');
  echo shell_exec('git remote update -p 2>&1');
  if (intval(shell_exec('git branch | grep [*] | wc -l') == 0)) {
	 echo "Could not determine new default branch!\n";
	 exit(-1);
  }
}
echo shell_exec('git pull --all --rebase --autostash 2>&1');
echo shell_exec('git clean -f -d -x 2>&1');
echo shell_exec('git reflog expire --expire=now --all 2>&1');
echo shell_exec('git repack -ad 2>&1');
echo shell_exec('git prune 2>&1');

$old_log = file_get_contents('../tree-sitter-apertium.log');
$new_log = shell_exec('git log -1');

if ($old_log === $new_log) {
	echo "No changes since last - skipping!\n";
	exit(0);
}

echo shell_exec('ssh -l tino 192.168.1.12 "open -a Docker" 2>&1');
echo shell_exec('rm -rfv ../dist/tree-sitter-apertium; mkdir -pv ../dist/tree-sitter-apertium');

echo "Building languages\n";
echo shell_exec('docker run -i --rm -v '.__DIR__.'/pkgs:/pkgs wheel-tools /pkgs/tree-sitter-apertium.01-tools.sh 2>&1');

echo "\n";
echo "Building wheel py38 x86_64\n";
echo shell_exec('git clean -f -d -x 2>&1');
echo shell_exec('docker run -i --rm -v '.__DIR__.'/pkgs:/pkgs wheel-py38-x86_64 /pkgs/tree-sitter-apertium.02-py38.sh 2>&1');
echo shell_exec('cp -avf dist/* ../dist/tree-sitter-apertium/');

echo "\n";
echo "Building wheel py39 x86_64\n";
echo shell_exec('git clean -f -d -x 2>&1');
echo shell_exec('docker run -i --rm -v '.__DIR__.'/pkgs:/pkgs wheel-py39-x86_64 /pkgs/tree-sitter-apertium.02-py39.sh 2>&1');
echo shell_exec('cp -avf dist/* ../dist/tree-sitter-apertium/');

echo "\n";
echo "Building wheel py310 x86_64\n";
echo shell_exec('git clean -f -d -x 2>&1');
echo shell_exec('docker run -i --rm -v '.__DIR__.'/pkgs:/pkgs wheel-py310-x86_64 /pkgs/tree-sitter-apertium.02-py310.sh 2>&1');
echo shell_exec('cp -avf dist/* ../dist/tree-sitter-apertium/');

echo shell_exec('git clean -f -d -x 2>&1');

echo "\n";
echo "Building wheel py38 aarch64\n";
echo shell_exec('rsync -avz --delete '.__DIR__.'/pkgs tino@192.168.1.12:/tmp/ 2>&1');
echo shell_exec('ssh -l tino 192.168.1.12 "/usr/local/bin/docker run -i --rm -e AUDIT_PLATFORM=manylinux2014_aarch64 -v /tmp/pkgs:/pkgs wheel-py38-aarch64 /pkgs/tree-sitter-apertium.02-py38.sh 2>&1" 2>&1');
echo shell_exec('rsync -avz tino@192.168.1.12:/tmp/pkgs/tree-sitter-apertium/dist/* ../dist/tree-sitter-apertium/ 2>&1');

echo "\n";
echo "Building wheel py39 aarch64\n";
echo shell_exec('rsync -avz --delete '.__DIR__.'/pkgs tino@192.168.1.12:/tmp/ 2>&1');
echo shell_exec('ssh -l tino 192.168.1.12 "/usr/local/bin/docker run -i --rm -e AUDIT_PLATFORM=manylinux2014_aarch64 -v /tmp/pkgs:/pkgs wheel-py39-aarch64 /pkgs/tree-sitter-apertium.02-py39.sh 2>&1" 2>&1');
echo shell_exec('rsync -avz tino@192.168.1.12:/tmp/pkgs/tree-sitter-apertium/dist/* ../dist/tree-sitter-apertium/ 2>&1');

echo "\n";
echo "Building wheel py310 aarch64\n";
echo shell_exec('rsync -avz --delete '.__DIR__.'/pkgs tino@192.168.1.12:/tmp/ 2>&1');
echo shell_exec('ssh -l tino 192.168.1.12 "/usr/local/bin/docker run -i --rm -e AUDIT_PLATFORM=manylinux2014_aarch64 -v /tmp/pkgs:/pkgs wheel-py310-aarch64 /pkgs/tree-sitter-apertium.02-py310.sh 2>&1" 2>&1');
echo shell_exec('rsync -avz tino@192.168.1.12:/tmp/pkgs/tree-sitter-apertium/dist/* ../dist/tree-sitter-apertium/ 2>&1');

$user = escapeshellarg('TWINE_USERNAME='.getenv('TWINE_USERNAME'));
$pass = escapeshellarg('TWINE_PASSWORD='.getenv('TWINE_PASSWORD'));
echo shell_exec('docker run -i --rm -e '.$user.' -e '.$pass.' -v '.__DIR__.'/pkgs:/pkgs wheel-tools /pkgs/tree-sitter-apertium.99-upload.sh 2>&1');

file_put_contents('../tree-sitter-apertium.log', $new_log);
