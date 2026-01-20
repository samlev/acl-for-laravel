<?php

arch('contracts are interfaces')
    ->expect('\\SamLev\\Acl\\Support\\Contracts')
    ->toBeInterfaces();

arch('mechanisms are traits')
    ->expect('\\SamLev\\Acl\\Support\\Mechanisms')
    ->toBeInterfaces();
