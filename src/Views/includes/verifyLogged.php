<?php

$sessionHelper = new \JairoJeffersont\Helpers\LoginSessionHelper();
$verifySession = $sessionHelper::validateSession();

if (!$verifySession) {
    header('Location: ?section=login');
}
