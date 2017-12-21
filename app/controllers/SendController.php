<?php

use Hexim\HeximZcashBundle\Zcash\ZcashWrapper;

/**
 * Class SendController
 */
class SendController extends ControllerBase
{
    public function indexAction()
    {
        $gRecaptchaResponse = $this->request->getPost('g-recaptcha-response');
        $recaptcha = new \ReCaptcha\ReCaptcha($this->config->faucet->recaptcha_secret);
        $resp = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);
        if ($resp->getErrorCodes()) {
            $this->flashSession->error('Invalid captcha');
            $this->response->redirect('/');

            return;
        }

        $address = $this->request->getPost('address');

        if (!$this->validateAddress($address)) {
            $this->response->redirect('/');
            $this->flashSession->error('Invalid T Address');

            return;
        }

        $user = User::findFirst("address = '{$address}'");

        if ($user) {
            $this->response->redirect('/');
            $this->flashSession->error('This T Address already registered');

            return;
        }

        $sendResult = $this->sendMoney($address);
        if (is_string($sendResult)) {
            $this->response->redirect('/');
            $this->flashSession->error($sendResult);

            return;
        }

        if (isset($sendResult['error'])) {
            $this->response->redirect('/');
            $this->flashSession->error($sendResult['error']);

            return;
        }

        $user = new User();
        $user->setAddress($address);
        $user->setIpAddress(ip2long($_SERVER['REMOTE_ADDR']));
        $user->setSessionKey(session_id());
        $user->save();

        $this->flashSession->success('Выплата была отправлена');
        $this->response->redirect('/');
    }

    protected function sendMoney($address)
    {
        $wallet = new ZcashWrapper([
            'rpc_address'  => $this->config->faucet->server_addr,
            'rpc_user'     => $this->config->faucet->server_user,
            'rpc_password' => $this->config->faucet->server_passwd,
        ]);

        $command = [
            'jsonrpc' => '1.0',
            'id'      => 'curl',
            'method'  => 'sendtoaddress',
            'params'  =>
                [
                    $address,
                    $this->config->faucet->airdrop_amount,
                    'Airdrop',
                ],
        ];

        return $wallet->rpcZcashCommand($command);
    }

    /**
     * @param $address string
     * @return bool
     */
    protected function validateAddress($address)
    {
        if (strlen($address) !== 35) {
            return false;
        }

        if (strpos($address, 'zn') !== 0) {
            return false;
        }

        if (!preg_match('/[0-9A-Za-z]/', $address)) {
            return false;
        }

        return true;

    }
}