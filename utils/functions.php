<?php

function runsOnTestMachine(): bool {

    return $_SERVER['HTTP_HOST'] == "localhost:8100";
}