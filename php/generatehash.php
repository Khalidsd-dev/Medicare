<?php

echo "Generating password hash for 'password123'...\n";

$hash = password_hash("password", PASSWORD_DEFAULT);
echo "Generated hash: " . $hash . "\n";


