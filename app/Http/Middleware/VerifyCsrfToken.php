<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
	    'user/register/registerinto',
	    'user/register/registerVerifyInfo',
	    'user/register/registerSendCode',
	    //入金
	    'user/deposit_request',
	    'user/deposit_notfiy',
	    'user/deposit_return',
	    'user/deposit_notfiy2',
	    'user/deposit_return2',
		'user/deposit_notfiy_otc',
		'user/withdraw_notfiy_otc',
		'user/withdraw_verify_otc',
	    //'user/position/positionSummarySearch',
    ];
}
