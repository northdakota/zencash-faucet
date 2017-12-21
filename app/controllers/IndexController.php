<?php

use Hexim\HeximZcashBundle\Zcash\ZcashWallet;

/**
 * Class IndexController
 */
class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $ipAddress = ip2long($_SERVER['REMOTE_ADDR']);
        $forwardedFor = $_SERVER['HTTP_FORWARDED_FOR'] || $_SERVER['HTTP_FORWARDED'] || $_SERVER['HTTP_X_FORWARDED_FOR'];

        $user = User::findFirst("ip_address = {$ipAddress}");

        $this->view->registered = User::count();

        $isAllowed = true;

        if ($user && $user->getId()) {
            $isAllowed = false;
        }

        if ($forwardedFor) {
            $isAllowed = false;
        }

        $sessionId = session_id();
        $user = User::findFirst("session_key = '{$sessionId}'");

        if ($user) {
            $isAllowed = false;
        }

        $this->view->is_allowed = $isAllowed;

        $wallet = new ZcashWallet([
            'rpc_address'  => $this->config->faucet->server_addr,
            'rpc_user'     => $this->config->faucet->server_user,
            'rpc_password' => $this->config->faucet->server_passwd,
        ]);

        $walletInfo = $wallet->getWalletInfo();

        $this->view->is_online = true;
        $this->view->balance = 'N/A';

        if (!$walletInfo || isset($walletInfo['error'])) {
            $this->view->is_online = false;
        } else {
            $this->view->balance = $walletInfo['result']['balance'];
        }

        $this->view->recaptcha_sitecode = $this->config->faucet->recaptcha_sitecode;

    }
}

