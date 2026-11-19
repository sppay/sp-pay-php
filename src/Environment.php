<?php

namespace Sppay\SpPayPhp;

enum Environment: string
{
    case Production = 'https://engine.sppaygh.com';
    case Staging = 'https://engine.sppay.dev';
}
